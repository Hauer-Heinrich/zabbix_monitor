<?php
declare(strict_types=1);

namespace HauerHeinrich\ZabbixMonitor\Controller;

use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \Psr\Log\LoggerAwareTrait;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class Typo3InformationController extends ActionController implements \Psr\Log\LoggerAwareInterface {

    use LoggerAwareTrait;

    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var \HauerHeinrich\ZabbixMonitor\Domain\Repository\ClientinfoRepository
     */
    private $clientinfoRepository = null;

    public $methodList = [
        'CheckPathExists',
        'GetDiskSpace',
        'GetExtensionList' => 'local',
        // 'GetExtensionList' => [
        //     'scope' => 'local'
        // ],
        // 'GetExtensionVersion',
        'GetFilesystemChecksum',
        'GetPHPVersion',
        'GetTYPO3Version',
        'GetLogResults',
        // 'HasForbiddenUsers',
        'HasUpdate',
        'HasSecurityUpdate',
        'GetLastSchedulerRun',
        'GetLastExtensionListUpdate' => true,
        // 'GetLastExtensionListUpdate' => [
        //     'extensionlist' => true
        // ],
        'GetDatabaseVersion',
        'GetApplicationContext',
        'GetInsecureExtensionList',
        'GetOutdatedExtensionList',
        'GetTotalLogFilesSize',
        'HasRemainingUpdates',
        'GetZabbixLogFileSize',
        // 'HasExtensionUpdate',
        'HasExtensionUpdateList' => 'loaded',
        // 'GetProgramVersion',
        // 'GetFeatureValue',
        'GetOpCacheStatus',
    ];

    /**
     * Inject the client_info repository
     *
     * @param \HauerHeinrich\ZabbixMonitor\Domain\Repository\ClientinfoRepository $clientinfoRepository
     */
    public function injectClientinfoRepository(\HauerHeinrich\ZabbixMonitor\Domain\Repository\ClientinfoRepository $clientinfoRepository) {
        $this->clientinfoRepository = $clientinfoRepository;
    }

    /**
     * injectConfigurationManager
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     * @return void
     */
    public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager): void {
        $this->configurationManager = $configurationManager;
    }

    public function __construct(FrontendInterface $cache) {
        $this->cache = $cache;
    }

    /**
     * initializeView
     *
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
     * @return void
     */
    public function initializeView(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view): void {
        $settings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        $this->settings = $settings;

        $this->view->assignMultiple([
            'settings' => $this->settings,
            'data' => $this->configurationManager->getContentObject()->data
        ]);

    }

    /**
     * getCachedValue
     * get cached value and if value is not cached then cache it
     *
     * @param string $domain
     * @param string $apiKey
     * @param string $method - examples see $methodList
     * @param string $methodParams - examples see $methodList -> GetExtensionList
     * @param array $tags
     * @param integer $lifetime
     * @return array
     */
    protected function getCachedValue(string $domain, string $apiKey, string $method, $methodParams = '', array $tags = [], int $lifetime = 60): array {
        $cacheIdentifier = md5($domain.'-'.$method);

        // If $entry is false, it hasn't been cached. Calculate the value and store it in the cache:
        if (($cacheValue = $this->cache->get($cacheIdentifier)) === false) {
            if(empty($methodParams)) { $methodParams = null; }
            $requestHandler = new \HauerHeinrich\ZabbixMonitor\Helper\RequestHelper($domain, $apiKey);
            $cacheValue = $requestHandler->{$method}($methodParams);
            // Save value in cache
            $this->cache->set($cacheIdentifier, $cacheValue, $tags, $lifetime);
        }

        return $cacheValue;
    }

    /**
     * listAction
     *
     * @return void
     */
    public function listAction(): void {
        // get domain list and api key
        $domainList = $this->clientinfoRepository->findAll();

        $apiData = [];
        foreach ($domainList as $value) {
            $apiUrl = $value->getApiUrl();
            $apiKey = $value->getApiKey();
            $name = empty($value->getTitle()) ? $apiUrl : $value->getTitle();

            $apiData[$name]['apiUrl'] = $apiUrl;

            foreach ($this->methodList as $methodKey => $method) {
                $methodParams = '';

                if(is_string($methodKey)) {
                    $methodParams = $method;
                    $method = $methodKey;
                }

                $apiData[$name][$method] = $this->getCachedValue($apiUrl, $apiKey, $method, $methodParams, [$method]);
            }
        }

        $this->view->assignMultiple([
            'apiData' => $apiData,
            'domainList' => $domainList
        ]);
    }

    /**
     * showAction
     *
     * @param string $apiUrl - must be valid URL e. g. https://www.domain.tld
     * @return void
     */
    public function showAction(string $apiUrl): void {
        $apiUrl = htmlspecialchars(strip_tags($apiUrl));
        $errors = [];
        if (!filter_var($apiUrl, FILTER_VALIDATE_URL)) {
            $errors[] = 'Given parameter apiUrl is not a valid URL!';
            $this->forward('customError', null, null, ['params' => [ 'errors' => $errors ]]);
        }

        $domainInfo = $this->clientinfoRepository->findByApiUrl($apiUrl)[0];

        if(!empty($domainInfo)) {
            $apiUrl = $domainInfo->getApiUrl();
            $apiKey = $domainInfo->getApiKey();
            $name = empty($domainInfo->getTitle()) ? $apiUrl : $domainInfo->getTitle();

            $apiData = [];
            $apiData['name'] = $name;
            $apiData['apiUrl'] = $apiUrl;
            foreach ($this->methodList as $methodKey => $method) {
                $methodParams = '';

                if(is_string($methodKey)) {
                    $methodParams = $method;
                    $method = $methodKey;
                }

                $apiData[$method] = $this->getCachedValue($apiUrl, $apiKey, $method, $methodParams, [$method]);
            }

            if(!empty($apiData['GetDiskSpace'])) {
                $totalBytes = $apiData['GetDiskSpace']['value']['total'];
                $freeBytes = $apiData['GetDiskSpace']['value']['free'];
                $totalFormated = \HauerHeinrich\ZabbixMonitor\Helper\FormatHelper::formatBytes($totalBytes);
                $freeFormated = \HauerHeinrich\ZabbixMonitor\Helper\FormatHelper::formatBytes($freeBytes);

                $apiData['GetDiskSpace']['value']['total'] = $totalFormated;
                $apiData['GetDiskSpace']['value']['free'] = $freeFormated;
            }

            $phpVersion = $apiData['GetPHPVersion']['value'];
            $apiData['HasOutDatedPhpVersion']['status'] = false;
            if(!empty($phpVersion)) {
                $apiData['HasOutDatedPhpVersion']['status'] = true;
                $apiData['HasOutDatedPhpVersion']['value'] = $this->checkPhpVersion($phpVersion);
            }

            $this->view->assignMultiple([
                'apiData' => $apiData
            ]);
        }
    }

    /**
     * customErrorAction
     * custom error / warning output -> fluid template
     * additionally writes errors to the log file
     *
     * @param array $params
     * @return void
     */
    public function customErrorAction(array $params = []): void {
        if(empty($params)) {
            $this->logger->warning('No parameters given');
        }

        if(!empty($params['warnings'])) {
            foreach ($variable as $key => $warning) {
                $this->logger->warning($warning);
            }
        }

        if(!empty($params['errors'])) {
            foreach ($params['errors'] as $key => $error) {
                $this->logger->error($error);
            }
        }

        $this->view->assignMultiple([
            'params' => $params
        ]);
    }

    /**
     * checkPhpVersion
     * checks if given PHP version is outdated
     *
     * @param string $phpVersion
     * @return bool
     */
    public function checkPhpVersion(string $phpVersion): bool {
        $phpVersionArray = explode('.', $phpVersion);
        $phpShortVersion = $phpVersionArray[0].'.'.$phpVersionArray[1];

        $phpVersionsList = [
            '7.2' => strtotime('2019-11-30'),
            '7.3' => strtotime('2020-12-06'),
            '7.4' => strtotime('2022-11-28'),
            '8.0' => strtotime('2023-11-26'),
        ];

        foreach ($phpVersionsList as $key => $value) {
            if($key === $phpShortVersion) {
                if($value < time()) {
                    return true;
                }

                break;
            }
        }

        return false;
    }
}

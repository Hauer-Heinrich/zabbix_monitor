<?php
declare(strict_types=1);

namespace HauerHeinrich\ZabbixMonitor\Command;

use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Object\ObjectManager;
use \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use \HauerHeinrich\ZabbixMonitor\Domain\Repository\ClientinfoRepository;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class UpdateDomainListCommand extends Command {

    /**
     * @var FrontendInterface
     */
    private $cache;

    /**
     * @var \HauerHeinrich\ZabbixMonitor\Domain\Repository\ClientinfoRepository
     */
    private $clientinfoRepository = null;

    public $methodList = [
        'CheckPathExists',
        'GetDiskSpace',
        'GetExtensionList' => [
            'additionalParams' => [
                'scope' => 'local'
            ]
        ],
        // 'GetExtensionVersion',
        'GetFilesystemChecksum',
        'GetPHPVersion',
        'GetTYPO3Version',
        'GetLogResults',
        // 'HasForbiddenUsers',
        'HasUpdate',
        'HasSecurityUpdate',
        'GetLastSchedulerRun',
        'GetLastExtensionListUpdate' => [
            'additionalParams' => [
                'extensionlist' => true
            ]
        ],
        'GetDatabaseVersion',
        'GetApplicationContext',
        'GetInsecureExtensionList',
        'GetOutdatedExtensionList',
        'GetTotalLogFilesSize',
        'HasRemainingUpdates',
        'GetZabbixLogFileSize',
        // 'HasExtensionUpdate',
        'HasExtensionUpdateList' => [
            'additinalParams' => [
                'scope' => 'loaded'
            ]
        ],
        // 'GetProgramVersion',
        // 'GetFeatureValue',
        'GetOpCacheStatus',
    ];

    public function __construct(FrontendInterface $cache) {
        $this->cache = $cache;
        parent::__construct();
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure() {
        $this->setHelp('TEST: Prints a list of recent sys_log entries.' . LF . 'If you want to get more detailed information, use the --verbose option.');
    }

    /**
     * Executes the command for showing sys_log entries
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int error code
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->clientinfoRepository = $objectManager->get(ClientinfoRepository::class);

        $domainList = $this->clientinfoRepository->findAll();

        $methodList = $this->methodList;

        foreach ($domainList as $domainKey => $domainValue) {
            $content = [];
            $errorCode = '';
            $errorMessage = '';
            $client = new Client([
                'base_uri'    => $domainValue->getApiUrl(),
                'timeout'     => 40, // how long one request may take at most, in seconds
            ]);

            // Initiate each request but do not block.
            $promises = [];
            $promisesMethod = [];

            foreach ($methodList as $methodKey => $methodValue) {
                $additionalParams = '';
                if(is_string($methodKey) && is_array($methodValue)) {
                    $method = $methodKey;
                    if(is_array($methodValue['additionalParams'])) {
                        foreach ($methodValue['additionalParams'] as $additionalParamsKey => $additionalParamsValue) {
                            $additionalParams .= '&'.$additionalParamsKey.'='.$additionalParamsValue;
                        }
                    }
                } else {
                    $method = $methodValue;
                }

                $cacheIdentifier = md5($domainValue->getApiUrl().'-'.$method);
                if (($cacheValue = $this->cache->get($cacheIdentifier)) === false) {
                    $promises[] = $client->getAsync('/zabbixclient/?key='.$domainValue->getApiKey().'&operation='.$method.$additionalParams);
                    $promisesMethod[] = $method;
                }
            }

            // Wait for the requests to complete, even if some of them fail.
            $results = Promise\settle($promises)->wait();

            foreach ($results as $data => $download) {
                if ($download['state'] === 'fulfilled') {
                    $returnArray = json_decode( (string)$download['value']->getBody(), true);
                    $content[$domainValue->getApiUrl()][$promisesMethod[$data]] = $returnArray;

                    if(is_array($methodList[$promisesMethod[$data]]) && is_int($methodList[$promisesMethod[$data]]['lifetime'])) {
                        $lifetime = $methodList[$promisesMethod[$data]]['lifetime'];
                    } else {
                        // default 1 day = 86400
                        $lifetime = 86400;
                    }

                    $this->getCachedValue($domainValue->getApiUrl(), $promisesMethod[$data], [$promisesMethod[$data]], $returnArray, $lifetime);
                }
                if ($download['state'] === 'rejected') {
                    $errorCode = $download['reason']->getCode();
                    $errorMessage = $download['reason']->getMessage();
                }
            }
        }

        $io = new SymfonyStyle($input, $output);
        $io->title('zabbix_monitor - update cache entries');

        $io->writeln('zabbix_monitor domain info update successfully');

        return Command::SUCCESS;
    }

    /**
     * getCachedValue
     * get cached value and if value is not cached then cache it
     *
     * @param string $domain
     * @param string $method - examples see $methodList
     * @param array $tags
     * @param mixed $data - data which should be cached
     * @param integer $lifetime
     * @return array
     */
    protected function getCachedValue(string $domain, string $method, array $tags = [], $data, int $lifetime = 60): array {
        $cacheIdentifier = md5($domain.'-'.$method);

        // If $entry is false, it hasn't been cached. Calculate the value and store it in the cache:
        if (($cacheValue = $this->cache->get($cacheIdentifier)) === false) {
            $cacheValue = $data;
            // Save value in cache
            $this->cache->set($cacheIdentifier, $cacheValue, $tags, $lifetime);
        }

        return $cacheValue;
    }
}

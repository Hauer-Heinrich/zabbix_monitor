<?php
declare(strict_types=1);

namespace HauerHeinrich\ZabbixMonitor\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Object\ObjectManager;
use \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use \HauerHeinrich\ZabbixMonitor\Domain\Repository\ClientinfoRepository;

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
        'CheckPathExists' => [],
        'GetDiskSpace' => [],
        'GetExtensionList' => [
            'additionalParams' => [
                'scope' => 'local'
            ]
        ],
        // 'GetExtensionVersion' => [],
        'GetFilesystemChecksum' => [],
        'GetPHPVersion' => [
            'lifetime' => 172800
        ],
        'GetTYPO3Version' => [],
        'GetLogResults' => [],
        // 'HasForbiddenUsers' => [],
        'HasUpdate' => [],
        'HasSecurityUpdate' => [],
        'GetLastSchedulerRun' => [],
        'GetLastExtensionListUpdate' => [
            'additionalParams' => [
                'extensionlist' => true
            ]
        ],
        'GetDatabaseVersion' => [],
        'GetApplicationContext' => [],
        'GetInsecureExtensionList' => [],
        'GetOutdatedExtensionList' => [],
        'GetTotalLogFilesSize' => [],
        'HasRemainingUpdates' => [],
        'GetZabbixLogFileSize' => [],
        // 'HasExtensionUpdate' => [],
        'HasExtensionUpdateList' => [
            'additinalParams' => [
                'scope' => 'loaded'
            ]
        ],
        // 'GetProgramVersion' => [],
        // 'GetFeatureValue' => [],
        'GetOpCacheStatus' => [],
    ];

    public function __construct(FrontendInterface $cache) {
        $this->cache = $cache;
        parent::__construct();
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure() {
        $this
            ->setDescription('Updates cache entrys of zabbix_monitor')
            ->setHelp('Usage example: php typo3 zabbix_monitor:update --url=http://domain.tld -m GetLastExtensionListUpdate -p extensionlist=true -p other=parameter')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('debugOutput', 'd', InputOption::VALUE_NONE, 'outputs debug informations'),
                    new InputOption('url', 'u', InputOption::VALUE_REQUIRED, 'URL to update - must be exactly the same value as safed at the database, e. g. https://www.domain.tld'),
                    new InputOption('method', 'm', InputOption::VALUE_REQUIRED, 'Updates a specific API method. Example: GetLastExtensionListUpdate'),
                    new InputOption('methodList', 'l', InputOption::VALUE_NONE, 'Lists available methods (-m). Example: GetLastExtensionListUpdate'),
                    new InputOption('lifetime', 't', InputOption::VALUE_REQUIRED, 'Set the lifetime of the cached data. Overwrites the default-values!'),
                ])
            );
    }

    /**
     * Executes the command for showing sys_log entries
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int error code
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $io->title('zabbix_monitor - update cache entries');

        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->clientinfoRepository = $objectManager->get(ClientinfoRepository::class);

        if($input->getOption('methodList')) {
            $list = [];
            foreach ($this->methodList as $key => $value) {
                $list[$key][] = $key;
            }

            $io->table(
                ['Method list'],
                $list
            );

            return Command::SUCCESS;
        }

        $inputUrl = $input->getOption('url');
        if(empty($inputUrl)) {
            $domainList = $this->clientinfoRepository->findAll();
        } else {
            if (filter_var($inputUrl, FILTER_VALIDATE_URL)) {
                $domainList = $this->clientinfoRepository->findByApiUrl($inputUrl);
            } else {
                $io->error('No valid URL given!');
                return Command::FAILURE;
            }
        }

        $inputMethod = $input->getOption('method');
        if(array_key_exists($inputMethod, $this->methodList)) {
            $methodList[$inputMethod] = $this->methodList[$inputMethod];
        } else {
            $methodList = $this->methodList;
        }

        $inputlifetime = intval($input->getOption('lifetime'));

        if(!empty($domainList)) {
            foreach ($domainList as $domainValue) {
                $io->writeln('process: '.$domainValue->getApiUrl());

                $content = [];
                $errors = [];
                $client = new Client([
                    'base_uri'    => $domainValue->getApiUrl(),
                    'timeout'     => 40, // how long one request may take at most, in seconds
                    'headers' => [
                        'api-key' => $domainValue->getApiKey(),
                    ],
                ]);

                // Initiate each request but do not block.
                $promises = [];
                $promisesMethod = [];

                foreach ($methodList as $methodKey => $methodValue) {
                    $additionalParams = '';
                    $method = $methodKey;

                    if(is_array($methodValue['additionalParams'])) {
                        foreach ($methodValue['additionalParams'] as $additionalParamsKey => $additionalParamsValue) {
                            $additionalParams .= '&'.$additionalParamsKey.'='.$additionalParamsValue;
                        }
                    }

                    $cacheIdentifier = md5($domainValue->getApiUrl().'-'.$method);
                    if (($cacheValue = $this->cache->get($cacheIdentifier)) === false) {
                        // $promises[] = $client->getAsync('/zabbixclient/?key='.$domainValue->getApiKey().'&operation='.$method.$additionalParams);
                        $promises[] = $client->requestAsync('POST', '/zabbixclient/?'.'operation='.$method.$additionalParams);
                        $promisesMethod[] = $method;
                    }
                }

                // Wait for the requests to complete, even if some of them fail.
                $results = Promise\settle($promises)->wait();

                foreach ($results as $data => $download) {
                    if ($download['state'] === 'fulfilled') {
                        $returnArray = json_decode( (string)$download['value']->getBody(), true);
                        $content[$domainValue->getApiUrl()][$promisesMethod[$data]] = $returnArray;

                        if($inputlifetime > 0) {
                            $lifetime = $inputlifetime;
                        } else {
                            // get the lifetime of the default method array
                            if(is_array($methodList[$promisesMethod[$data]]) && is_int($methodList[$promisesMethod[$data]]['lifetime'])) {
                                $lifetime = $methodList[$promisesMethod[$data]]['lifetime'];
                            } else {
                                // default 1 day = 86400
                                $lifetime = 86400;
                            }
                        }

                        $this->getCachedValue($domainValue->getApiUrl(), $promisesMethod[$data], [$promisesMethod[$data]], $returnArray, $lifetime);
                    }

                    if ($download['state'] === 'rejected') {
                        $errors[] = [
                            'errorCode' => $download['reason']->getCode(),
                            'errorMessage' => $download['reason']->getMessage()
                        ];
                    }
                }

                if(!empty($errors)) {
                    $io->warning($domainValue->getApiUrl(). ' -------- has errors!');

                    if($input->getOption('debugOutput')) {
                        $io->warning('Skip: '.$domainValue->getApiUrl(), 'Errors: '.$errorCode);
                        $io->table(
                            ['ErrorCode', 'Error Message'],
                            $errors
                        );
                    }
                }
            }

            $io->writeln('');
            $io->writeln('zabbix_monitor domain info updated');

            return Command::SUCCESS;
        }

        $io->writeln('');
        $io->error('domainList - no domains found!');

        return Command::FAILURE;
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

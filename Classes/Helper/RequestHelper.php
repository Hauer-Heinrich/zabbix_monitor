<?php
declare(strict_types=1);

namespace HauerHeinrich\ZabbixMonitor\Helper;

// use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class RequestHelper {

    /**
     * domain
     *
     * @var string
     */
    public $domain;

    /**
     * apiKey
     *
     * @var string
     */
    private $apiKey;

    public function __construct(string $domain, string $apiKey) {
        $this->checkDomain($domain);
        $this->checkApiKey($apiKey);

        $this->domain = $domain;
        $this->apiKey = $apiKey;
    }

    public function checkDomain(string $domain) {
        $urlValidator = new \TYPO3\CMS\Extbase\Validation\Validator\UrlValidator;
        return $urlValidator->isValid($domain);
    }

    public function checkApiKey(string $apiKey) {
        $stringLengthValidator = new \TYPO3\CMS\Extbase\Validation\Validator\StringLengthValidator(['minimum' => 1]);
        return $stringLengthValidator->isValid($apiKey);
    }

    /**
     * getAllData
     * Gets all data from the TYPO3 EXT:zabbix_client
     *
     * @return array
     */
    public function getAllData(): array {
        $result = [];
        $result['apiData'][$this->domain]['checkPathExists'] = $this->checkPathExists();
        $result['apiData'][$this->domain]['getDiskSpace'] = $this->getDiskSpace();
        $result['apiData'][$this->domain]['getExtensionList'] = $this->getExtensionList('local');
        $result['apiData'][$this->domain]['getFilesystemChecksum'] = $this->getFilesystemChecksum();
        $result['apiData'][$this->domain]['getPHPVersion'] = $this->getPHPVersion();
        $result['apiData'][$this->domain]['getTYPO3Version'] = $this->getTYPO3Version();
        $result['apiData'][$this->domain]['getLogResults'] = $this->getLogResults();
        $result['apiData'][$this->domain]['hasUpdate'] = $this->hasUpdate();
        $result['apiData'][$this->domain]['hasSecurityUpdate'] = $this->hasSecurityUpdate();
        $result['apiData'][$this->domain]['getLastSchedulerRun'] = $this->getLastSchedulerRun();
        $result['apiData'][$this->domain]['getLastExtensionListUpdate'] = $this->getLastExtensionListUpdate();
        $result['apiData'][$this->domain]['getDatabaseVersion'] = $this->getDatabaseVersion();
        $result['apiData'][$this->domain]['getApplicationContext'] = $this->getApplicationContext();
        $result['apiData'][$this->domain]['getInsecureExtensionList'] = $this->getInsecureExtensionList();
        $result['apiData'][$this->domain]['GetOutdatedExtensionList'] = $this->GetOutdatedExtensionList();
        $result['apiData'][$this->domain]['GetTotalLogFilesSize'] = $this->GetTotalLogFilesSize();
        $result['apiData'][$this->domain]['HasRemainingUpdates'] = $this->HasRemainingUpdates();
        $result['apiData'][$this->domain]['GetZabbixLogFileSize'] = $this->GetZabbixLogFileSize();
        $result['apiData'][$this->domain]['GetOpCacheStatus'] = $this->GetOpCacheStatus();

        return $result;
    }

    /**
     * getApiData
     * Does the api request
     *
     * @param string $operation - request api method e. g. getPHPVersion or getTYPO3Version
     * @param string $additionalParams - request api method parameter e. g. for operation "HasExtensionUpdate" -> 'extensionKey=my_extension'
     * @return array
     */
    public function getApiData(string $operation, string $additionalParams = ''): array {
        $url = $this->domain.'/zabbixclient/';
        $urlParams = [
            // 'key' => $this->apiKey,
            'operation' => $operation
        ];
        $data = [ ];
        $options =  [
            'http'=> [
                'header' => "Content-type: application/json\r\n" . "api-key: ". $this->apiKey ."\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ]
        ];

        $context  = stream_context_create($options);
            $result = file_get_contents( $url.'?'.http_build_query($urlParams).'&'.$additionalParams, false, $context );

        try {
            $context  = stream_context_create($options);
            $result = file_get_contents( $url.'?'.http_build_query($urlParams).'&'.$additionalParams, false, $context );
        } catch (\Throwable $th) {
            throw $th;
        }

        if($result) {
            return json_decode($result, true);
        }

        return [];
    }

    public function checkPathExists() {
        return $this->getApiData('CheckPathExists');
    }

    public function getDiskSpace() {
        return $this->getApiData('GetDiskSpace');
    }

    public function getExtensionList(string $scope = 'local') {
        return $this->getApiData('GetExtensionList', $scope);
    }

    public function getExtensionVersion(string $extensionKey) {
        return $this->getApiData('GetExtensionVersion', 'extensionKey='.$extensionKey);
    }

    public function getFilesystemChecksum() {
        return $this->getApiData('GetFilesystemChecksum');
    }

    public function getPHPVersion() {
        return $this->getApiData('GetPHPVersion');
    }

    public function getTYPO3Version() {
        return $this->getApiData('GetTYPO3Version');
    }

    public function getLogResults() {
        return $this->getApiData('GetLogResults');
    }

    public function hasForbiddenUsers(string $userNames) {
        return $this->getApiData('HasForbiddenUsers', 'usernames='.$userNames);
    }

    public function hasUpdate() {
        return $this->getApiData('HasUpdate');
    }

    public function hasSecurityUpdate() {
        return $this->getApiData('HasSecurityUpdate');
    }

    public function getLastSchedulerRun() {
        return $this->getApiData('GetLastSchedulerRun');
    }

    public function getLastExtensionListUpdate(bool $extensionlist = true) {
        return $this->getApiData('GetLastExtensionListUpdate', 'extensionlist='.$extensionlist);
    }

    public function getDatabaseVersion() {
        return $this->getApiData('GetDatabaseVersion');
    }

    public function getApplicationContext() {
        return $this->getApiData('GetApplicationContext');
    }

    public function getInsecureExtensionList() {
        return $this->getApiData('GetInsecureExtensionList');
    }

    public function getOutdatedExtensionList() {
        return $this->getApiData('GetOutdatedExtensionList');
    }

    public function getTotalLogFilesSize() {
        return $this->getApiData('GetTotalLogFilesSize');
    }

    public function hasRemainingUpdates() {
        return $this->getApiData('HasRemainingUpdates');
    }

    public function getZabbixLogFileSize() {
        return $this->getApiData('GetZabbixLogFileSize');
    }

    /**
     * hasExtensionUpdate
     *
     * @param string $extensionKey
     * @return void
     */
    public function hasExtensionUpdate(string $extensionKey) {
        return $this->getApiData('HasExtensionUpdate', 'extensionKey='.$extensionKey);
    }

    /**
     * hasExtensionUpdateList
     *
     * @param string $scope
     * @return void
     */
    public function hasExtensionUpdateList(string $scope) {
        return $this->getApiData('HasExtensionUpdateList', 'scope='.$scope);
    }

    /**
     * getProgramVersion
     *
     * @param string $program - e. g. openssl or gm or im or optipng or jpegoptim or webp
     * @return void
     */
    public function getProgramVersion(string $program) {
        return $this->getApiData('GetProgramVersion', 'program='.$program);
    }

    /**
     * getFeatureValue
     *
     * @param string $feature - e. g. context or image or mail or passwordhashing
     * @return void
     */
    public function getFeatureValue(string $feature) {
        return $this->getApiData('GetFeatureValue', 'feature='.$feature);
    }

    /**
     * getOpCacheStatus
     *
     * @return void
     */
    public function getOpCacheStatus() {
        return $this->getApiData('GetOpCacheStatus');
    }
}

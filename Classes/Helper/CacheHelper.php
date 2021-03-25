<?php
declare(strict_types=1);

namespace HauerHeinrich\ZabbixMonitor\Helper;

// use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class CacheHelper {
    /**
     * @var FrontendInterface
     */
    private $cache;

    public function __construct(FrontendInterface $cache) {
        $this->cache = $cache;
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

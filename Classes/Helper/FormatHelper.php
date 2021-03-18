<?php
declare(strict_types=1);

namespace HauerHeinrich\ZabbixMonitor\Helper;

// use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class FormatHelper {

    /**
     * formatBytes
     *
     * @param integer $bytes
     * @param integer $precision
     * @return void
     */
    public static function formatBytes(int $bytes, int $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

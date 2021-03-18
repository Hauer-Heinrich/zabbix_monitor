<?php
declare(strict_types=1);

namespace HauerHeinrich\ZabbixMonitor\Domain\Repository;

// use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;

/**
 *
 * This file is part of the "zabbix_monitor" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2021 Christian Hackl <chackl@hauer-heinrich.de>, www.Hauer-Heinrich.de
 */

/**
 * This file is part of the "zabbix_monitor" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * (c) 2021 Christian Hackl <chackl@hauer-heinrich.de>, www.Hauer-Heinrich.de
 * This file is part of the "Job offers - simple" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * (c) 2021 Christian Hackl <chackl@hauer-heinrich.de>, www.Hauer-Heinrich.de
 * The repository for Jobposts
 */
class ClientinfoRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    protected $query;
    protected $queryParser;
    protected $queryConstraint;

    /**
     * @var array
     */
    protected $defaultOrderings = [
        'sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING
    ];

    // Class Initialization (after all dependencies have been injected) (similar to __construct)
    public function initializeObject(): void {
        // Initialize QueryParser (Typo3DbQueryParser)
        $this->queryParser = $this->objectManager->get(Typo3DbQueryParser::class);

        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings $querySettings */
        $querySettings = $this->objectManager->get(\TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setRespectSysLanguage(TRUE);
        $this->setDefaultQuerySettings($querySettings);

        // Initialize Query
        $this->query = $this->createQuery();
    }
}

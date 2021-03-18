<?php
declare(strict_types=1);

namespace HauerHeinrich\ZabbixMonitor\Domain\Model;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * This file is part of the "Job offers - simple" Extension for TYPO3 CMS.
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 * (c) 2021 Christian Hackl <chackl@hauer-heinrich.de>, www.Hauer-Heinrich.de
 * Joboffer
 */
class Clientinfo extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * @var \DateTime
     */
    protected $crdate;

    /**
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * @var \DateTime
     */
    protected $starttime;

    /**
     * @var \DateTime
     */
    protected $endtime;

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * apiUrl
     *
     * @var string
     */
    protected $apiUrl;

    /**
     * apiKey
     *
     * @var string
     */
    protected $apiKey;

    /**
     * Initialize categories and media relation
     */
    public function __construct()
    {
    }

    /**
     * Get creation date
     *
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Set creation date
     *
     * @param \DateTime $crdate
     * @return void
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * Get year of crdate
     *
     * @return string
     */
    public function getYearOfCrdate()
    {
        return $this->getCrdate()->format('Y');
    }

    /**
     * Get month of crdate
     *
     * @return string
     */
    public function getMonthOfCrdate()
    {
        return $this->getCrdate()->format('m');
    }

    /**
     * Get day of crdate
     *
     * @return int
     */
    public function getDayOfCrdate()
    {
        return (int)$this->crdate->format('d');
    }

    /**
     * Get timestamp
     *
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * Set time stamp
     *
     * @param \DateTime $tstamp time stamp
     * @return void
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }

    /**
     * Get starttime
     *
     * @return \DateTime
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Set starttime
     *
     * @param \DateTime $starttime
     * @return void
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * Get endtime
     *
     * @return \DateTime
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Set endtime
     *
     * @param \DateTime $endtime
     * @return void
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
    }

    /**
     * Returns the title
     *
     * @return string title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns the apiUrl
     *
     * @return string apiUrl
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * Sets the apiUrl
     *
     * @param string $apiUrl
     * @return void
     */
    public function setApiUrl(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * Returns the apiKey
     *
     * @return string apiKey
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Sets the apiKey
     *
     * @param string $apiKey
     * @return void
     */
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
}

<?php

namespace Hoogi91\AccessRestriction\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class IpValidationService
 * @package Hoogi91\AccessRestriction\Service
 */
class IpValidationService
{
    /**
     * @var string
     */
    protected $remoteIp;

    /**
     * IpValidationService constructor.
     *
     * @param null $ip
     */
    public function __construct($ip = null)
    {
        $this->remoteIp = $ip ?? $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @param string $ip
     * @param string $compareIp
     *
     * @return bool
     */
    public function equals($ip, $compareIp = null)
    {
        return ip2long($compareIp ?? $this->remoteIp) === ip2long($ip);
    }

    /**
     * @param string|array $list
     * @param string $compareIp
     *
     * @return bool
     */
    public function findInList($list, $compareIp = null)
    {
        // check if list needs to be converted to array
        if (is_string($list)) {
            $list = GeneralUtility::trimExplode(PHP_EOL, trim($list), true);
        }

        if (empty($list) || !is_array($list)) {
            return false;
        }

        // validate each list entry as ip or range against compare ip
        foreach ($list as $ipOrRange) {
            if ($this->validate($ipOrRange, $compareIp) === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $ipOrRange
     * @param string $compareIp
     *
     * @return bool
     */
    public function validate($ipOrRange, $compareIp = null)
    {
        $remoteAddress = ip2long($compareIp ?? $this->remoteIp);

        // first try to match against bit range
        list ($subnet, $bits) = GeneralUtility::trimExplode('/', $ipOrRange, true, 2);
        if (!empty($bits)) {
            $subnet = ip2long($subnet);
            $mask = -1 << (32 - (int)$bits);
            $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned
            return ($remoteAddress & $mask) === $subnet;
        }

        // then try to match against minus (-) delimited ip range
        $ipRange = array_map('ip2long', GeneralUtility::trimExplode('-', $ipOrRange, true, 2));
        if (count($ipRange) === 2) {
            return (min($ipRange) <= $remoteAddress && $remoteAddress <= max($ipRange));
        }

        // otherwise try to match single ip
        return $this->equals($ipOrRange, $compareIp);
    }
}

<?php

declare(strict_types=1);

namespace Hoogi91\AccessRestriction\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

use const PHP_EOL;

class IpValidationService
{
    private readonly string $remoteIp;

    public function __construct(?string $ip = null)
    {
        $this->remoteIp = $ip ?? $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function equals(string $ip, string $compareIp = null): bool
    {
        return ip2long($compareIp ?? $this->remoteIp) === ip2long($ip);
    }

    /**
     * @param string|array<string> $list
     */
    public function findInList(string|array $list, string $compareIp = null): bool
    {
        // check if list needs to be converted to array
        if (is_string($list)) {
            $list = GeneralUtility::trimExplode(PHP_EOL, trim($list), true);
        }

        if (empty($list)) {
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

    public function validate(string $ipOrRange, string $compareIp = null): bool
    {
        $remoteAddress = ip2long($compareIp ?? $this->remoteIp);

        // first try to match against bit range
        [$subnet, $bits] = GeneralUtility::trimExplode('/', $ipOrRange, true, 2) + ['', ''];
        if (!empty($bits)) {
            $subnet = ip2long($subnet);
            $mask = -1 << 32 - (int)$bits;
            $subnet &= $mask; # nb: in case the supplied subnet wasn't correctly aligned

            return ($remoteAddress & $mask) === $subnet;
        }

        // then try to match against minus (-) delimited ip range
        $ipRange = array_map('ip2long', GeneralUtility::trimExplode('-', $ipOrRange, true, 2));
        if (count($ipRange) === 2) {
            return min($ipRange) <= $remoteAddress && $remoteAddress <= max($ipRange);
        }

        // otherwise try to match single ip
        return $this->equals($ipOrRange, $compareIp);
    }
}

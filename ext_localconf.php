<?php

defined('TYPO3') or die();

(static function (string $extKey) {
    // register datahandler hook to clear cache when a access restricted group is changed/saved
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][$extKey] = \Hoogi91\AccessRestriction\Hook\DataHandler::class;

    // override tyoscriptfrontend controller to set user group list without hazzle
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Context\UserAspect::class] = array(
        'className' => \Hoogi91\AccessRestriction\Hook\UserAspect::class,
    );

    // add caching configuration for reading access restricted frontend groups
    $cacheConfiguration = &$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'];
    if (empty($cacheConfiguration['cache_accessrestriction'])) {
        $cacheConfiguration['cache_accessrestriction'] = [];
    }
    if (!isset($cacheConfiguration['cache_accessrestriction']['frontend'])) {
        $cacheConfiguration['cache_accessrestriction']['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class;
    }
    if (!isset($cacheConfiguration['cache_accessrestriction']['backend'])) {
        $cacheConfiguration['cache_accessrestriction']['backend'] = \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class;
    }
    if (!isset($cacheConfiguration['cache_accessrestriction']['groups'])) {
        $cacheConfiguration['cache_accessrestriction']['groups'] = ['pages'];
    }
    if (!isset($cacheConfiguration['cache_accessrestriction']['options']['defaultLifetime'])) {
        $cacheConfiguration['cache_accessrestriction']['options']['defaultLifetime'] = 86400; // on default one day ;)
    }
})(
    'access_restriction'
);

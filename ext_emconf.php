<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Access Restriction',
    'description'  => 'Extension to register additional user access restrictions (IP, etc.)',
    'category'     => 'fe',
    'author'       => 'Thorsten Hogenkamp',
    'author_email' => 'thorsten@hogenkamp-bocholt.de',
    'version'      => '4.0.0',
    'state'        => 'stable',
    'constraints'  => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
        ],
    ],
    'autoload'     => [
        'psr-4' => [
            'Hoogi91\\AccessRestriction\\' => 'Classes',
        ],
    ],
];

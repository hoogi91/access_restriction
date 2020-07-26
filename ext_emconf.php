<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Access Restriction',
    'description'  => 'Extension to register additional user access restrictions (IP, etc.)',
    'category'     => 'fe',
    'author'       => 'Thorsten Hogenkamp',
    'author_email' => 'hoogi20@googlemail.com',
    'version'      => '2.0.1',
    'state'        => 'stable',
    'constraints'  => [
        'depends' => [
            'typo3'    => '9.5.0-10.4.99',
            'frontend' => '9.5.0-10.4.99',
        ],
    ],
    'autoload'     => [
        'psr-4' => [
            'Hoogi91\\AccessRestriction\\' => 'Classes',
        ],
    ],
];

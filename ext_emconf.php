<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Access Restriction',
    'description'  => 'Extension to register additional user access restrictions (IP, etc.)',
    'category'     => 'fe',
    'author'       => 'Thorsten Hogenkamp',
    'author_email' => 'hoogi20@googlemail.com',
    'version'      => '2.0.0',
    'state'        => 'stable',
    'constraints'  => [
        'depends' => [
            'typo3'    => '9.5.0-9.99.99',
            'frontend' => '9.5.0-9.99.99',
        ],
    ],
];

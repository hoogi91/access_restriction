<?php
$EM_CONF[$_EXTKEY] = [
    'title'        => 'Access Restriction',
    'description'  => 'Extension to register additional user access restrictions (IP, etc.)',
    'category'     => 'fe',
    'author'       => 'Thorsten Hogenkamp',
    'author_email' => 'hoogi20@googlemail.com',
    'version'      => '1.0.0',
    'state'        => 'stable',
    'constraints'  => [
        'depends' => [
            'typo3' => '8.7.0-8.99.99',
        ],
    ],
];

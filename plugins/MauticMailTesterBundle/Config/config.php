<?php

return [
    'name'        => 'Mail Tester',
    'description' => 'Email spam test by mail-tester.com',
    'author'      => 'kuzmany.biz',
    'version'     => '1.0.0',
    'routes'      => [
        'main' => [
            'mautic_plugin_mail_tester_action' => [
                'path'       => '/mail-tester/{objectAction}/{objectId}',
                'controller' => 'MauticMailTesterBundle:MailTester:execute',
            ],
        ],
    ],
    'services' => [
        'events' => [
            'mautic.plugin.mail.tester.button.subscriber' => [
                'class'     => \MauticPlugin\MauticMailTesterBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
        ],
    ],
];

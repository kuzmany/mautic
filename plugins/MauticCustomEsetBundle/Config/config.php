<?php
return [
    'name' => 'Custom Eset',
    'description' => '',
    'author' => 'kuzmany.biz',
    'version' => '1.0.0',
    'services' => [
        'events' => [
            'mautic.plugin.customeset.js.subscriber' => [
                'class' => 'MauticPlugin\MauticCustomEsetBundle\EventListener\BuildJsSubscriber',
                'arguments' => [
                    'templating.helper.assets',
                ],
            ],
            'mautic.plugin.customeset.pagebundle.subscriber' => [
                'class' => 'MauticPlugin\MauticCustomEsetBundle\EventListener\PageSubscriber',
                'arguments' => [
                    'mautic.campaign.model.event',
                    'mautic.lead.model.lead',
                    'mautic.user.provider',
                    'mautic.helper.cookie',
                ],
            ],
        ],
    ],
    'parameters' => array(
    ),
];
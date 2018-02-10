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
            'mautic.plugin.customeset.campaignbundle.subscriber' => [
                'class' => 'MauticPlugin\MauticCustomEsetBundle\EventListener\CampaignSubscriber',
                'arguments' => [
                    'mautic.lead.model.lead',
                    'doctrine.dbal.default_connection',
                ],
            ],
        ],
        'forms' => [
            'mautic.plugin.customeset.type.update.page.session' => [
                'class' => 'MauticPlugin\MauticCustomEsetBundle\Form\Type\CampaignEventUpdatePageSessionType',
                'alias' => 'update_page_session',
            ],
        ],
    ],
        'parameters' => array(
    ),
];
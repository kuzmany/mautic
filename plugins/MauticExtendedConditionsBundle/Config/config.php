<?php
return [
    'name' => 'Extended Conditions',
    'description' => '',
    'author' => 'MadeSimple.shop',
    'version' => '1.0.0',
    'services' => [
        'events' => [
            'mautic.plugin.extendedconditions.subscriber' => [
                'class'     => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\CampaignSubscriber',
                'arguments' => [
                    'mautic.page.model.page',
                    'mautic.campaign.model.event',
                ],
            ],
        ],
        'forms' => [
            'mautic.plugin..extendedconditions.type.last_active.campaign_trigger' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventLastActiveConditionType',
                'alias' => 'extendedconditionsnevent_last_active',
            ],
        ],
    ],
];
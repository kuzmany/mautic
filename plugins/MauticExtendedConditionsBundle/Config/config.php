<?php
return [
    'name' => 'Extended Conditions',
    'description' => '',
    'author' => 'MadeSimple.shop',
    'version' => '1.0.0',
    'services' => [
        'events' => [
            'mautic.plugin.extendedconditions.campaignbundle.subscriber' => [
                'class'     => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\CampaignSubscriber',
                'arguments' => [
                    'mautic.campaign.model.event',
                    'mautic.lead.model.lead',
                ],
            ],
            'mautic.plugin.extendedconditions.leadbundle.subscriber' => [
                'class'     => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\LeadSubscriber',
                'arguments' => [
                    'mautic.campaign.model.event',
                    'mautic.lead.model.lead',
                ],
            ],
            'mautic.plugin.extendedconditions.pagebundle.subscriber' => [
                'class'     => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\PageSubscriber',
                'arguments' => [
                    'mautic.campaign.model.event',
                    'mautic.lead.model.lead',
                    'doctrine.dbal.default_connection',
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
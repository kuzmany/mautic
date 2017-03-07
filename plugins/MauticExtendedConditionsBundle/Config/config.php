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
                    'mautic.helper.core_parameters',
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
            'mautic.plugin.extendedconditions.configbundle.subscriber' => [
                'class'     => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\ConfigSubscriber',
                'arguments' => [
                    'mautic.helper.core_parameters',
                ],
            ],
        ],
        'forms' => [
            'mautic.plugin.extendedconditions.type.last_active.campaign_trigger' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventLastActiveConditionType',
                'alias' => 'extendedconditionsnevent_last_active',
            ],
            'mautic.plugin.extendedconditions.type.config' => [
                'class'     => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\ConfigType',
                'alias'     => 'extendedconditionsnevent_config',
            ],
        ],
    ],
    'parameters' => array(
        'lists' => [],
    )
];
<?php
return [
    'name' => 'Extended Conditions',
    'description' => '',
    'author' => 'MadeSimple.shop',
    'version' => '1.0.0',
    'services' => [
        'events' => [
            'mautic.plugin.extendedconditions.campaignbundle.subscriber' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\CampaignSubscriber',
                'arguments' => [
                    'mautic.campaign.model.event',
                    'mautic.lead.model.lead',
                    'session',
                    'mautic.page.model.page',
                    'request_stack',
                    'doctrine.dbal.default_connection',
                    'mautic.helper.cookie',
                    'mautic.campaign.model.campaign',
                ],
            ],
            'mautic.plugin.extendedconditions.js.subscriber' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\BuildJsSubscriber',
                'arguments' => [
                    'templating.helper.assets',
                ],
            ],
            'mautic.plugin.extendedconditions.leadbundle.subscriber' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\LeadSubscriber',
                'arguments' => [
                    'mautic.campaign.model.event',
                    'mautic.lead.model.lead',
                    'mautic.helper.core_parameters',
                ],
            ],
            'mautic.plugin.extendedconditions.pagebundle.subscriber' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\PageSubscriber',
                'arguments' => [
                    'mautic.campaign.model.event',
                    'mautic.lead.model.lead',
                ],
            ],
            'mautic.plugin.extendedconditions.configbundle.subscriber' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\EventListener\ConfigSubscriber',
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
            'mautic.plugin.extendedconditions.type.page_session.campaign_trigger' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventPageSessionConditionType',
                'alias' => 'extendedconditionsnevent_page_session',
            ],
            'mautic.plugin.extendedconditions.type.click.campaign_trigger' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventClickConditionType',
                'alias' => 'extendedconditionsnevent_click',
            ],
            'mautic.plugin.extendedconditions.type.dynamic.campaign_trigger' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventDynamicConditionType',
                'alias' => 'extendedconditionsnevent_dynamic',
            ],
            'mautic.plugin.extendedconditions.type.dynamic.stop.campaign_action' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventDynamicStopActionType',
                'alias' => 'extendedconditionsnevent_dynamic_stop',
            ],
            'mautic.plugin.extendedconditions.type.remove.logs.campaign_action' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventRemoveLogsActionType',
                'alias' => 'extendedconditionsnevent_remove_logs',
            ],
            'mautic.plugin.extendedconditions.type.on.change.segment.campaign_decision' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventOnChangeSegmentDecisionType',
                'alias' => 'extendedconditionsnevent_on_change_segment',
            ],
            'mautic.plugin.extendedconditions.type.on.change.campaign.campaign_decision' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\CampaignEventOnChangeCampaignDecisionType',
                'alias' => 'extendedconditionsnevent_on_change_campaign',
            ],
            'mautic.plugin.extendedconditions.type.config' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Form\Type\ConfigType',
                'alias' => 'extendedconditionsnevent_config',
            ],
        ],
        'other' => [
            'mautic.plugin.helper.dynamic' => [
                'class' => 'MauticPlugin\MauticExtendedConditionsBundle\Helper\DynamicHelper',
                'arguments' => [
                    'mautic.dynamicContent.model.dynamicContent',
                    'mautic.campaign.model.event',
                    'event_dispatcher',
                    'session',
                ],
            ],
        ],
    ],
    'routes' => [
        'public' => [
            'mautic_api_dynamic_action' => [
                'path' => '/dynamic/{objectAlias}',
                'controller' => 'MauticExtendedConditionsBundle:DynamicApi:process',
            ],
        ],
    ],
    'parameters' => array(
        'lists' => [],
    ),
];
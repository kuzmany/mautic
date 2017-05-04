<?php
return [
    'name' => 'Address Validator',
    'description' => '',
    'author' => 'MadeSimple.shop',
    'version' => '1.0.0',
    'services' => [
        'events' => [
            'mautic.plugin.addressvalidator.formbundle.subscriber' => [
                'class'     => 'MauticPlugin\MauticAddressValidatorBundle\EventListener\FormSubscriber',
                'arguments' => [
                    'mautic.lead.model.lead',
                    'mautic.helper.core_parameters',
                    'mautic.plugin.helper.addressvalidator'
                ],
            ],
            'mautic.plugin.addressvalidator.configbundle.subscriber' => [
                'class' => 'MauticPlugin\MauticAddressValidatorBundle\EventListener\ConfigSubscriber',
                'arguments' => [
                    'mautic.plugin.helper.addressvalidator'
                ]
            ],
        ],
        'forms' => [
            'mautic.plugin.addressvalidator.type.addressvalidator.field' => [
                'class' => 'MauticPlugin\MauticAddressValidatorBundle\Form\Type\FormFieldAddressValidatordType',
                'alias' => 'addressvalidator',
                'arguments' => [
                    'mautic.lead.model.field',

                ],
            ],
            'mautic.plugin.addressvalidator.type.config' => [
                'class' => 'MauticPlugin\MauticAddressValidatorBundle\Form\Type\ConfigType',
                'alias' => 'addressvalidator_config',
            ],
        ],
        'other' => [
            'mautic.plugin.helper.addressvalidator' => [
                'class'     => 'MauticPlugin\MauticAddressValidatorBundle\Helper\AddressValidatorHelper',
                'arguments' => [
                    'mautic.http.connector',
                    'request_stack',
                    'mautic.helper.core_parameters',
                ],
            ],
        ],
    ],
    'routes' => [
        'public' => [
            'mautic_addressvalidator_validation' => [
                'path'       => '/addressvalidation',
                'controller' => 'MauticAddressValidatorBundle:Ajax:validation',
            ],
        ],
    ],
    'parameters' => [
        'validatorApiKey'                       => '6893d607ecaa622daa3d074751ca92bc',
        'validatorUrl'                          => 'http://av-test.ballistix.com/validators',
    ],
];
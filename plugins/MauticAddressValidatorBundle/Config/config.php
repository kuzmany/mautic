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
                    'mautic.helper.integration',
                ],
            ],
            'mautic.plugin.validator.addressvalidator' => [
                'class'     => 'MauticPlugin\MauticAddressValidatorBundle\Form\Validator\Constraints\AddressValidatorAccessValidator',
                'arguments' => [
                    'mautic.plugin.helper.addressvalidator'
                ],
                'tag'       => 'validator.constraint_validator',
                'alias'     => 'addressvalidator_access',
            ],
        ],
    ],
    'routes' => [
        'public' => [
            'mautic_addressvalidator_validation' => [
                'path'       => '/addressvalidation',
                'controller' => 'MauticAddressValidatorBundle:Ajax:validation',
            ],
            'mautic_addressvalidator_generate' => [
                'path'       => '/addressvalidation/generate.js',
                'controller' => 'MauticAddressValidatorBundle:Ajax:generate',
            ],
        ],
    ],
    'parameters' => [
        'validatorApiKey'                       => '6893d607ecaa622daa3d074751ca92bc',
        'validatorUrl'                          => 'http://api.ballistix.com/validators',
    ],
];
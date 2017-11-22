<?php

return [
    'name'        => 'Word to vocative',
    'description' => 'Modifier to convert a name or given gender-dependent alias to its vocative form, useful for email opening salutation.',
    'author'      => 'Friends of Mautic',
    'version'     => '1.3.0.',

    'services' => [
        'events' => [
            'plugin.vocative.emailNameToVocative.subscriber' => [
                'class' => 'MauticPlugin\MauticVocativeBundle\EventListener\EmailNameToVocativeSubscriber',
            ],
            'plugin.vocative.dynamic.content.subscriber' => [
                'class' => 'MauticPlugin\MauticVocativeBundle\EventListener\DynamicContentSubscriber',
            ],
        ],
        'other' => [
            'plugin.vocative.name_converter' => [
                'class'     => 'MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter',
                'arguments' => ['plugin.vocative.czech_name'],
            ],
            'plugin.vocative.czech_name' => [
                'class'   => 'CzechVocative\CzechName',
                'factory' => ['MauticPlugin\MauticVocativeBundle\Service\NameFactory', 'createCzechName'],
            ],
        ],
    ],
];

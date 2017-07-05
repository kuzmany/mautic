<?php


/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressValidatorBundle\Integration;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use MauticPlugin\MauticAddressValidatorBundle\Form\Validator\Constraints\AddressValidatorAccess;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class OneSignalIntegration.
 */
class AddressValidatorIntegration extends AbstractIntegration
{
    /**
     * @var bool
     */
    protected $coreIntegration = true;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return 'AddressValidator';
    }

    public function getIcon()
    {
        return 'plugins/MauticAddressValidatorBundle/Assets/img/Ballistix_logo_White.png';
    }


    /**
     * @return array
     */
    public function getFormSettings()
    {
        return [
            'requires_callback'      => false,
            'requires_authorization' => false,
        ];
    }
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getAuthenticationType()
    {
        return 'none';
    }

    /**
     * @param \Mautic\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {

        if ($formArea == 'keys') {
            $builder->add(
                'apiUrl',
                TextType::class,
                [
                    'label' => 'plugin.addressvalidator.field.label.apiurl',
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                    ],
                    'required' => true,
                    'constraints' => [
                        new NotBlank(
                            ['message' => 'mautic.core.value.required']
                        ),
                    ],
                ]
            );

            $builder->add(
                $builder->create(
                    'validatorApiKey',
                    'text',
                    [
                        'label' => 'plugin.addressvalidator.field.label.apikey',
                        'label_attr' => ['class' => 'control-label'],
                        'attr' => [
                            'class' => 'form-control',
                        ],
                        'required' => true,
                        'constraints' => [
                            new AddressValidatorAccess(),
                        ],
                    ]
                )
            );
        }
    }
}

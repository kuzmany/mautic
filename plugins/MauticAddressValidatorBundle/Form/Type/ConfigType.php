<?php

/*
 * @copyright   2015 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressValidatorBundle\Form\Type;

use Mautic\CoreBundle\Factory\MauticFactory;
use MauticPlugin\MauticAddressValidatorBundle\Form\Validator\Constraints\AddressValidatorAccess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotEqualTo;

/**
 * Class ConfigType.
 */
class ConfigType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
                    'required' => false,
                    'constraints' => [
                        new AddressValidatorAccess(),
                    ],
                ]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'addressvalidator_config';
    }
}

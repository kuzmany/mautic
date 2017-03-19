<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressValidatorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FormFieldAddressValidatordType.
 */
class FormFieldAddressValidatordType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'address1',
            'text',
            [
                'label' => 'plugin.addressvalidator.field.address1',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                ],
            ]
        );

        $builder->add(
            'leadField',
            'choice',
            [
                'choices'     => $options['leadFields'],
                'choice_attr' => function ($val, $key, $index) use ($options) {
                    if (!empty($options['leadFieldProperties'][$val]) && (in_array($options['leadFieldProperties'][$val]['type'], FormFieldHelper::getListTypes()) || !empty($options['leadFieldProperties'][$val]['properties']['list']) || !empty($options['leadFieldProperties'][$val]['properties']['optionlist']))) {
                        return ['data-list-type' => 1];
                    }

                    return [];
                },
                'label'      => 'mautic.form.field.form.lead_field',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => 'mautic.form.field.help.lead_field',
                ],
                'required' => false,
                'data'     => $data,
            ]
        );

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'addressvalidator';
    }
}

<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ConfigType.
 */
class ConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('background_import_if_more_rows_than', 'number', [
            'label'      => 'mautic.lead.background.import.if.more.rows.than',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class'   => 'form-control',
                'tooltip' => 'mautic.lead.background.import.if.more.rows.than.tooltip',
            ],
        ]);

        $builder->add('contact_columns',
            LeadColumnsType::class, [
            'label'      => 'mautic.config.tab.columns',
            'label_attr' => ['class' => 'control-label'],
            'attr'       => [
                'class'   => 'form-control',
            ],
            'multiple'    => true,
            'required'    => true,
            'constraints' => [
                new NotBlank(
                    ['message' => 'mautic.core.value.required']
                ),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'leadconfig';
    }
}

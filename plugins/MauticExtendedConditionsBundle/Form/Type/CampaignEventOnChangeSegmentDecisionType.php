<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedConditionsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
/**
 * Class CampaignEventOnChangeSegmentActionType.
 */
class CampaignEventOnChangeSegmentDecisionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'addedSegments',
            'leadlist_choices',
            [
                'label'      => 'plugin.extended.conditions.on.change.segment.add',
                'label_attr' => ['class' => 'control-label'],
                'multiple'   => true,
                'required'   => false,
            ]
        );

        $builder->add(
            'removedSegments',
            'leadlist_choices',
            [
                'label'      => 'plugin.extended.conditions.on.change.segment.remove',
                'label_attr' => ['class' => 'control-label'],
                'multiple'   => true,
                'required'   => false,
            ]
        );

    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'extendedconditionsnevent_on_change_segment';
    }
}

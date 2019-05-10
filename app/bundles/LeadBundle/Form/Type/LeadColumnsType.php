<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Form\Type;

use Mautic\LeadBundle\Model\FieldModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class LeadColumnsType extends AbstractType
{
    /**
     * @var FieldModel
     */
    protected $fieldModel;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param FieldModel          $fieldModel
     * @param TranslatorInterface $translator
     */
    public function __construct(FieldModel $fieldModel, TranslatorInterface $translator)
    {
        $this->fieldModel = $fieldModel;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $model= $this->fieldModel;

        $resolver->setDefaults(
          [
              'choices' => function (Options $options) use ($model) {
                  $fieldList = [];
                  $fieldList['name'] = $this->translator->trans('mautic.core.name');
                  $fieldList['email'] = $this->translator->trans('mautic.core.type.email');
                  $fieldList['location'] = $this->translator->trans('mautic.lead.lead.thead.location');
                  $fieldList['stage'] = $this->translator->trans('mautic.lead.stage.label');
                  $fieldList['points'] = $this->translator->trans('mautic.lead.points');
                  $fieldList['last_active'] = $this->translator->trans('mautic.lead.field.last_active');
                  $fieldList['id'] = $this->translator->trans('mautic.core.id');
                  $fieldList = $fieldList + $model->getFieldList(false);

                  return $fieldList;
              },
          ]
        );
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'choice';
    }
}

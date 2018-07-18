<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\CategoryBundle\Form\Type;

use Mautic\CategoryBundle\Model\CategoryModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryBundleListType.
 */
class CategoryBundleListType extends AbstractType
{
    private $model;

    /**
     * CategoryListType constructor.
     *
     * @param CategoryModel $model
     */
    public function __construct(CategoryModel $model)
    {
        $this->model      = $model;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => function (Options $options) {
                $categories = $this->model->getLookupResults($options['bundle'], '', 0);
                $choices = [];
                foreach ($categories as $l) {
                    $choices[$l['id']] = $l['title'];
                }

                return $choices;
            },
            'label'       => 'mautic.core.category',
            'label_attr'  => ['class' => 'control-label'],
            'multiple'    => true,
            'empty_value' => 'mautic.core.form.uncategorized',
            'required'    => false,
        ]);

        $resolver->setRequired(['bundle']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'category_list';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
}

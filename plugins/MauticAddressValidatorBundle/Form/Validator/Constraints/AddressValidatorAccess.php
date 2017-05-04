<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressValidatorBundle\Form\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AddressValidatorAccess extends Constraint
{
    public $message = 'plugin.addressvalidator.field.label.apikey.not.valid';

    public function validatedBy()
    {
        return 'addressvalidator_access';
    }
}

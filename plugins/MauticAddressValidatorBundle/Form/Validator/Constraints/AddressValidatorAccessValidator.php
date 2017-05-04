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
use MauticPlugin\MauticAddressValidatorBundle\Helper\AddressValidatorHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AddressValidatorAccessValidator extends ConstraintValidator
{
    /**
     * @var AddressValidatorHelper $addressValidatorHelper
     */
    private $addressValidatorHelper;

    public function __construct(AddressValidatorHelper $addressValidatorHelper)
    {
        $this->addressValidatorHelper = $addressValidatorHelper;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
       if(!$this->addressValidatorHelper->validation(true, $value)){
            $this->context->addViolation($constraint->message);
        }
    }
}

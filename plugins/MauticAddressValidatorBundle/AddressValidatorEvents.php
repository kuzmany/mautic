<?php

/*
 * @copyright   2016 Mautic, Inc. All rights reserved
 * @author      Mautic, Inc
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressValidatorBundle;

/**
 * Class AddressValidatorEvents.
 *
 * Events available for MauticAddressValidatorBundle
 */
final class AddressValidatorEvents
{
    /**
     * The mautic.on_address_validator_form_validate_action event is dispatched when a form is validated.
     *
     * The event listener receives a Mautic\FormBundle\Event\ValidationEvent instance.
     *
     * @var string
     */
    const ON_FORM_VALIDATE_ACTION = 'mautic.on_address_validator_form_validate_action';
}

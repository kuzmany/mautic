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
 * Class CitrixEvents.
 *
 * Events available for MauticCitrixBundle
 */
final class MauticAddressValidatorEvents
{
    /**
     * The mautic.on_addressvalidator_validate_Action event is dispatched when a form is validated.
     *
     * The event listener receives a Mautic\FormBundle\Event\ValidationEvent instance.
     *
     * @var string
     */
    const ON_FORM_VALIDATE_ACTION = 'mautic.on_addressvalidator_validate_Action';


}

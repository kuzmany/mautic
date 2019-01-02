<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SmsBundle\Helper;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Mautic\LeadBundle\Entity\Lead;

class NumberConvertor
{
    public function toIntNumberFormat(Lead $lead, $defaultRegion = null)
    {
        if (!$number = $lead->getLeadPhoneNumber()) {
            return null;
        }

        $phoneUtil       = PhoneNumberUtil::getInstance();
        $formattedNumber = null;
        echo $number;
        echo '-';
        echo (bool) $phoneUtil->isPossibleNumber($number, 'US');
        echo '|';
        try {
            $phone           = $phoneUtil->parse($number, null);
            $formattedNumber = $phoneUtil->format($phone, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            //echo $e->getMessage();
            $formattedNumber = $number;
            /*try {
                $phone = $phoneUtil->parse($number, 'SK');
                $formattedNumber = $phoneUtil->format($phone, PhoneNumberFormat::E164);
            } catch (NumberParseException $e) {
                $formattedNumber = $number;
            }*/
        }

        return $formattedNumber;
    }
}

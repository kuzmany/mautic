<?php
/*
 * @copyright   2018 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SmsBundle\Tests;

use Mautic\CoreBundle\Test\AbstractMauticTestCase;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\SmsBundle\Helper\NumberConvertor;

class NumberConverterTest extends AbstractMauticTestCase
{
    public function testFormatter()
    {
        $testedNumbers = [
            //'+421221234567' => ,
            '+12015550123'  => ['2015550123'],
            '+441174960123' => ['+44 117 496 0123'],
            '+421221234567' => ['00421221234567', '+421221234567'],
            '+421221234567' => ['221234567'],
            '+421905278030' => ['0905 278 030'],
            '+421948343666' => ['+421948343666'],
            '+421911582301' => ['0911582301'],
            '+420222064500' => ['222 064 500'],
            '+420222064500' => ['222 064 500'],
        ];
        $numberConvertor = new NumberConvertor();
        foreach ($testedNumbers as $result=>$testedNumber) {
            foreach ($testedNumber as $number) {
                $lead = new Lead();
                $lead->setMobile($number);
                $numberConvertor->toIntNumberFormat($lead);

                //$this->assertSame($result, $numberConvertor->toIntNumberFormat($lead));
            }
        }
    }
}

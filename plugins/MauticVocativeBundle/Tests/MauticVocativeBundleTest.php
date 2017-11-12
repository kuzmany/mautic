<?php
namespace MauticPlugin\MauticVocativeBundle\Tests;

use MauticPlugin\MauticVocativeBundle\MauticVocativeBundle;

class MauticVocativeBundleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function I_can_boot_bundle()
    {
        $bundle = new MauticVocativeBundle();
        self::assertFalse(class_exists('\MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName'));
        $bundle->boot();
        self::assertTrue(class_exists('\MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName'));
    }
}

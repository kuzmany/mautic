<?php
namespace MauticPlugin\MauticVocativeBundle\Service;

use MauticPlugin\MauticVocativeBundle\CzechVocative\CzechName;

class NameFactory
{
    /**
     * @return CzechName
     */
    public static function createCzechName()
    {
        return new CzechName();
    }
}

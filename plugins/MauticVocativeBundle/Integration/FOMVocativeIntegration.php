<?php
namespace MauticPlugin\MauticVocativeBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class FOMVocativeIntegration extends AbstractIntegration
{
    public function getName()
    {
        return 'FOMVocative';
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

}

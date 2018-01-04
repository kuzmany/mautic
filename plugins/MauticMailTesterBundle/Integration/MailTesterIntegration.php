<?php

namespace MauticPlugin\MauticMailTesterBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class MailTesterIntegration extends AbstractIntegration
{
    public function getName()
    {
        // should be the name of the integration
        return 'MailTester';
    }

    public function getAuthenticationType()
    {
        /* @see \Mautic\PluginBundle\Integration\AbstractIntegration::getAuthenticationType */
        return 'none';
    }
}

<?php

namespace Mautic\FormBundle\Event;

use Mautic\CoreBundle\Event\CommonEvent;
use Mautic\FormBundle\Entity\FormAbandonment;

class FormAbandonmentEvent extends CommonEvent
{
    public function __construct(FormAbandonment $abandonment)
    {
        $this->entity = $abandonment;
    }

    /**
     * Get the form abandonment entity.
     *
     * @return FormAbandonment
     */
    public function getFormAbandonment()
    {
        return $this->entity;
    }

    /**
     * Get the form.
     *
     * @return \Mautic\FormBundle\Entity\Form
     */
    public function getForm()
    {
        return $this->entity->getForm();
    }

    /**
     * Get the lead.
     *
     * @return \Mautic\LeadBundle\Entity\Lead
     */
    public function getLead()
    {
        return $this->entity->getLead();
    }
}

<?php

namespace Mautic\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\IpAddress;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PageBundle\Entity\Page;

class FormAbandonment
{
    public const TABLE_NAME = 'form_abandonments';

    /**
     * @var string
     */
    private $id;

    /**
     * @var Form
     **/
    private $form;

    /**
     * @var IpAddress|null
     */
    private $ipAddress;

    /**
     * @var Lead|null
     */
    private $lead;

    /**
     * @var string|null
     */
    private $trackingId;

    /**
     * @var \DateTimeInterface
     */
    private $dateAbandoned;

    /**
     * @var string
     */
    private $referer;

    /**
     * @var Page|null
     */
    private $page;

    /**
     * @var array
     */
    private $filledFields = [];

    /**
     * @var int
     */
    private $abandonmentPercentage = 0;

    /**
     * @var int
     */
    private $timeSpentSeconds = 0;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable(self::TABLE_NAME)
            ->setCustomRepositoryClass(FormAbandonmentRepository::class)
            ->addIndex(['tracking_id'], 'form_abandonment_tracking_search')
            ->addIndex(['date_abandoned'], 'form_abandonment_date')
            ->addIndex(['form_id', 'lead_id'], 'form_abandonment_form_lead');

        $builder->addBigIntIdField();

        $builder->createManyToOne('form', 'Form')
            ->addJoinColumn('form_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->addIpAddress(true);

        $builder->addLead(true, 'SET NULL');

        $builder->createField('trackingId', 'string')
            ->columnName('tracking_id')
            ->nullable()
            ->build();

        $builder->createField('dateAbandoned', 'datetime')
            ->columnName('date_abandoned')
            ->build();

        $builder->addField('referer', 'text');

        $builder->createManyToOne('page', Page::class)
            ->addJoinColumn('page_id', 'id', true, false, 'SET NULL')
            ->fetchExtraLazy()
            ->build();

        $builder->createField('filledFields', 'json')
            ->columnName('filled_fields')
            ->nullable()
            ->build();

        $builder->createField('abandonmentPercentage', 'integer')
            ->columnName('abandonment_percentage')
            ->build();

        $builder->createField('timeSpentSeconds', 'integer')
            ->columnName('time_spent_seconds')
            ->build();
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * Set dateAbandoned.
     *
     * @param \DateTime $dateAbandoned
     *
     * @return FormAbandonment
     */
    public function setDateAbandoned($dateAbandoned)
    {
        $this->dateAbandoned = $dateAbandoned;

        return $this;
    }

    /**
     * Get dateAbandoned.
     *
     * @return \DateTimeInterface
     */
    public function getDateAbandoned()
    {
        return $this->dateAbandoned;
    }

    /**
     * Set referer.
     *
     * @param string $referer
     *
     * @return FormAbandonment
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get referer.
     *
     * @return string
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Set form.
     *
     * @return FormAbandonment
     */
    public function setForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form.
     *
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set ipAddress.
     *
     * @return FormAbandonment
     */
    public function setIpAddress(?IpAddress $ipAddress = null)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress.
     *
     * @return IpAddress
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Get lead.
     *
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * Set lead.
     *
     * @return $this
     */
    public function setLead(?Lead $lead = null)
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * Get tracking ID.
     *
     * @return mixed
     */
    public function getTrackingId()
    {
        return $this->trackingId;
    }

    /**
     * Set tracking ID.
     *
     * @return $this
     */
    public function setTrackingId($trackingId)
    {
        $this->trackingId = $trackingId;

        return $this;
    }

    /**
     * Set page.
     *
     * @return FormAbandonment
     */
    public function setPage(?Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page.
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get filled fields.
     *
     * @return array
     */
    public function getFilledFields()
    {
        return $this->filledFields ?? [];
    }

    /**
     * Set filled fields.
     *
     * @return FormAbandonment
     */
    public function setFilledFields(array $filledFields)
    {
        $this->filledFields = $filledFields;

        return $this;
    }

    /**
     * Get abandonment percentage.
     *
     * @return int
     */
    public function getAbandonmentPercentage()
    {
        return $this->abandonmentPercentage;
    }

    /**
     * Set abandonment percentage.
     *
     * @return FormAbandonment
     */
    public function setAbandonmentPercentage($abandonmentPercentage)
    {
        $this->abandonmentPercentage = (int) $abandonmentPercentage;

        return $this;
    }

    /**
     * Get time spent in seconds.
     *
     * @return int
     */
    public function getTimeSpentSeconds()
    {
        return $this->timeSpentSeconds;
    }

    /**
     * Set time spent in seconds.
     *
     * @return FormAbandonment
     */
    public function setTimeSpentSeconds($timeSpentSeconds)
    {
        $this->timeSpentSeconds = (int) $timeSpentSeconds;

        return $this;
    }
}

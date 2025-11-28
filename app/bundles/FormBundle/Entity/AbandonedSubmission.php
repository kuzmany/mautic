<?php

namespace Mautic\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\IpAddress;
use Mautic\LeadBundle\Entity\Lead;

class AbandonedSubmission
{
    public const TABLE_NAME = 'form_abandoned_submissions';

    /**
     * @var int
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
     * @var \DateTimeInterface
     */
    private $dateModified;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string|null
     */
    private $sessionId;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable(self::TABLE_NAME)
            ->setCustomRepositoryClass(AbandonedSubmissionRepository::class)
            ->addIndex(['date_modified'], 'form_abandoned_date_modified')
            ->addIndex(['session_id'], 'form_abandoned_session_id');

        $builder->addBigIntIdField();

        $builder->createManyToOne('form', 'Form')
            ->addJoinColumn('form_id', 'id', false, false, 'CASCADE')
            ->build();

        $builder->addIpAddress(true);

        $builder->addLead(true, 'SET NULL');

        $builder->createField('dateModified', 'datetime')
            ->columnName('date_modified')
            ->build();

        $builder->createField('data', 'array')
            ->columnName('data')
            ->build();

        $builder->createField('sessionId', 'string')
            ->columnName('session_id')
            ->length(191)
            ->nullable()
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
     * Set dateModified.
     *
     * @param \DateTime $dateModified
     *
     * @return AbandonedSubmission
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified.
     *
     * @return \DateTimeInterface
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set form.
     *
     * @return AbandonedSubmission
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
     * @return AbandonedSubmission
     */
    public function setIpAddress(?IpAddress $ipAddress = null)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * @return IpAddress
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data.
     *
     * @return AbandonedSubmission
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return Lead
     */
    public function getLead()
    {
        return $this->lead;
    }

    /**
     * @return $this
     */
    public function setLead(?Lead $lead = null)
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param string|null $sessionId
     *
     * @return AbandonedSubmission
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }
}

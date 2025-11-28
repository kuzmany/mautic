<?php

namespace Mautic\FormBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

class FormAbandonmentRepository extends CommonRepository
{
    /**
     * Get abandonments for a specific form.
     *
     * @return array
     */
    public function getFormAbandonments($formId, $limit = 20, $offset = 0)
    {
        $qb = $this->createQueryBuilder('fa')
            ->select('fa')
            ->where('fa.form = :formId')
            ->setParameter('formId', $formId)
            ->orderBy('fa.dateAbandoned', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get abandonment count for a specific form.
     *
     * @return int
     */
    public function getFormAbandonmentCount($formId)
    {
        $qb = $this->createQueryBuilder('fa')
            ->select('COUNT(fa.id)')
            ->where('fa.form = :formId')
            ->setParameter('formId', $formId);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Get abandonments for a specific lead.
     *
     * @return array
     */
    public function getLeadAbandonments($leadId, $limit = 50)
    {
        $qb = $this->createQueryBuilder('fa')
            ->select('fa')
            ->where('fa.lead = :leadId')
            ->setParameter('leadId', $leadId)
            ->orderBy('fa.dateAbandoned', 'DESC')
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Check if there's an existing abandonment for this tracking ID.
     *
     * @return FormAbandonment|null
     */
    public function findByTrackingId($trackingId)
    {
        return $this->findOneBy(['trackingId' => $trackingId]);
    }

    /**
     * Get abandonment statistics for a form.
     *
     * @return array
     */
    public function getStatistics($formId)
    {
        $qb = $this->createQueryBuilder('fa')
            ->select([
                'COUNT(fa.id) as total',
                'AVG(fa.abandonmentPercentage) as avg_percentage',
                'AVG(fa.timeSpentSeconds) as avg_time',
            ])
            ->where('fa.form = :formId')
            ->setParameter('formId', $formId);

        return $qb->getQuery()->getSingleResult();
    }
}

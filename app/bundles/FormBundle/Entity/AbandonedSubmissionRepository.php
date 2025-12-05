<?php

namespace Mautic\FormBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;
use Mautic\LeadBundle\Entity\Lead;

/**
 * @extends CommonRepository<AbandonedSubmission>
 */
class AbandonedSubmissionRepository extends CommonRepository
{
    public function findExisting(Form $form, ?Lead $lead, ?string $sessionId): ?AbandonedSubmission
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.form = :form')
            ->setParameter('form', $form)
            ->setMaxResults(1);

        if (null !== $lead && $lead->getId()) {
            $qb->andWhere('a.lead = :lead')
                ->setParameter('lead', $lead);

            return $qb->getQuery()->getOneOrNullResult();
        }

        if ($sessionId) {
            $qb->andWhere('a.sessionId = :sessionId')
                ->setParameter('sessionId', $sessionId);

            return $qb->getQuery()->getOneOrNullResult();
        }

        return null;
    }

    public function deleteForForm(Form $form, ?Lead $lead, ?string $sessionId): void
    {
        $conditions = [];

        if (null !== $lead && $lead->getId()) {
            $conditions[] = 'a.lead = :lead';
        }

        if ($sessionId) {
            $conditions[] = 'a.sessionId = :sessionId';
        }

        if (empty($conditions)) {
            return;
        }

        $qb = $this->createQueryBuilder('a')
            ->delete()
            ->where('a.form = :form')
            ->setParameter('form', $form);

        $qb->andWhere($qb->expr()->orX(...$conditions));

        if (null !== $lead && $lead->getId()) {
            $qb->setParameter('lead', $lead);
        }

        if ($sessionId) {
            $qb->setParameter('sessionId', $sessionId);
        }

        $qb->getQuery()->execute();
    }
}

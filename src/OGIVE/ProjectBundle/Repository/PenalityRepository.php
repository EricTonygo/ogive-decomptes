<?php

namespace OGIVE\ProjectBundle\Repository;


use Doctrine\ORM\EntityRepository;
/**
 * PenalityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PenalityRepository extends EntityRepository
{
    public function deletePenality(\OGIVE\ProjectBundle\Entity\Penality $penality) {
        $em= $this->_em;
        $penality->setStatus(0);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($penality);
            $em->flush();
            $em->getConnection()->commit();
            return $penality;
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
    }


    public function savePenality(\OGIVE\ProjectBundle\Entity\Penality $penality) {
        $em= $this->_em;
        $penality->setStatus(1);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($penality);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $penality;
    }

    public function updatePenality(\OGIVE\ProjectBundle\Entity\Penality $penality) {
        $em= $this->_em;
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($penality);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $penality;
    }
    
    public function getPenalityQueryBuilder() {
         return $this
          ->createQueryBuilder('e')
          ->where('e.status = :status')
          ->andWhere('e.state = :state')
          ->orderBy('e.name', 'ASC')
          ->setParameter('status', 1)
         ->setParameter('state', 1);

    }
}
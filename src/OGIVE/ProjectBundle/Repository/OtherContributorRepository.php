<?php

namespace OGIVE\ProjectBundle\Repository;


use Doctrine\ORM\EntityRepository;
/**
 * OtherContributorRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OtherContributorRepository extends EntityRepository
{
    public function deleteOtherContributor(\OGIVE\ProjectBundle\Entity\OtherContributor $otherContributor) {
        $em= $this->_em;
        $otherContributor->setStatus(0);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($otherContributor);
            $em->flush();
            $em->getConnection()->commit();
            return $otherContributor;
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
    }


    public function saveOtherContributor(\OGIVE\ProjectBundle\Entity\OtherContributor $otherContributor) {
        $em= $this->_em;
        $otherContributor->setStatus(1);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($otherContributor);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $otherContributor;
    }

    public function updateOtherContributor(\OGIVE\ProjectBundle\Entity\OtherContributor $otherContributor) {
        $em= $this->_em;
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($otherContributor);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $otherContributor;
    }
    public function getAll($offset = null, $limit = null, $search_query = null, $project = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
        if ($search_query) {
            $qb->andWhere(
                    $qb->expr()->orX(
                            $qb->expr()->like('lower(e.nom)', ':search_query'), $qb->expr()->like('lower(e.searchData)', ':search_query')
            ));
            $search_query = strtolower($search_query);
            $qb->setParameter('search_query', '%' . strtolower($search_query) . '%');
        }
        if ($project && $project != "0") {
            $qb->join("e.project", 'u');
            $qb->andWhere('u.id = :project');
            $qb->setParameter("project", intval($project));
        }
        $qb->orderBy('e.createDate', 'DESC');
        
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()->getResult();
    }
    
    public function getOtherContributorQueryBuilder() {
         return $this
          ->createQueryBuilder('e')
          ->where('e.status = :status')
          ->andWhere('e.state = :state')
          ->orderBy('e.reference', 'ASC')
          ->setParameter('status', 1)
         ->setParameter('state', 1);

    }
}
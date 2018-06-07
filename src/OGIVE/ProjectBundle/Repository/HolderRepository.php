<?php

namespace OGIVE\ProjectBundle\Repository;


use Doctrine\ORM\EntityRepository;
/**
 * HolderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HolderRepository extends EntityRepository
{
    public function deleteHolder(\OGIVE\ProjectBundle\Entity\Holder $holder) {
        $em= $this->_em;
        $holder->setStatus(0);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($holder);
            $em->flush();
            $em->getConnection()->commit();
            return $holder;
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
    }


    public function saveHolder(\OGIVE\ProjectBundle\Entity\Holder $holder) {
        $em= $this->_em;
        $holder->setStatus(1);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($holder);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $holder;
    }

    public function updateHolder(\OGIVE\ProjectBundle\Entity\Holder $holder) {
        $em= $this->_em;
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($holder);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $holder;
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
    
    public function getAllByUser($offset = null, $limit = null, $user = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
        
        if ($user && $user != "0") {
            $qb->join("e.user", 'u');
            $qb->andWhere('u.id = :user');
            $qb->setParameter("user", intval($user));
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
    
    public function getHolderByUser($user = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
        if ($user && $user != "0") {
            $qb->join("e.user", 'u');
            $qb->andWhere('u.id = :user');
            $qb->setParameter("user", intval($user));
        }
        try{
            return $qb->getQuery()->getSingleResult();
        } catch(\Exception $ex){
            return null;
        }
    }
    
    public function getHolderQueryBuilder() {
         return $this
          ->createQueryBuilder('e')
          ->where('e.status = :status')
          ->andWhere('e.state = :state')
          ->orderBy('e.reference', 'ASC')
          ->setParameter('status', 1)
         ->setParameter('state', 1);

    }
}

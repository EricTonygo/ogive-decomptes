<?php

namespace OGIVE\ProjectBundle\Repository;


use Doctrine\ORM\EntityRepository;
/**
 * ServiceProviderRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ServiceProviderRepository extends EntityRepository
{
    public function deleteServiceProvider(\OGIVE\ProjectBundle\Entity\ServiceProvider $serviceProvider) {
        $em= $this->_em;
        $serviceProvider->setStatus(0);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($serviceProvider);
            $em->flush();
            $em->getConnection()->commit();
            return $serviceProvider;
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
    }


    public function saveServiceProvider(\OGIVE\ProjectBundle\Entity\ServiceProvider $serviceProvider) {
        $em= $this->_em;
        $serviceProvider->setStatus(1);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($serviceProvider);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $serviceProvider;
    }

    public function updateServiceProvider(\OGIVE\ProjectBundle\Entity\ServiceProvider $serviceProvider) {
        $em= $this->_em;
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($serviceProvider);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $serviceProvider;
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
    
    public function getServiceProviderQueryBuilder() {
         return $this
          ->createQueryBuilder('e')
          ->where('e.status = :status')
          ->andWhere('e.state = :state')
          ->orderBy('e.reference', 'ASC')
          ->setParameter('status', 1)
         ->setParameter('state', 1);

    }
}

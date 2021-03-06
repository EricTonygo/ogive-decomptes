<?php

namespace OGIVE\ProjectBundle\Repository;


use Doctrine\ORM\EntityRepository;
/**
 * DecompteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DecompteRepository extends EntityRepository
{
    public function deleteDecompte(\OGIVE\ProjectBundle\Entity\Decompte $decompte) {
        $em= $this->_em;
        $em->getConnection()->beginTransaction();
        $repositoryDecompteTask = $em->getRepository("OGIVEProjectBundle:DecompteTask");
        $repositoryDecompteValidation = $em->getRepository("OGIVEProjectBundle:DecompteValidation");
        try{
            $decompteTasks = $decompte->getDecompteTasks();
            foreach ($decompteTasks as $decompteTask) {
                $repositoryDecompteTask->deleteDecompteTask($decompteTask);
            }
            $decompteValidations = $decompte->getDecompteValidations();
            foreach ($decompteValidations as $decompteValidation) {
                $repositoryDecompteValidation->deleteDecompteValidation($decompteValidation);
            }
            $em->remove($decompte);
            $em->flush();
            $em->getConnection()->commit();
            return true;
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
    }


    public function saveDecompte(\OGIVE\ProjectBundle\Entity\Decompte $decompte) {
        $em= $this->_em;
        $decompte->setStatus(1);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($decompte);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $decompte;
    }

    public function updateDecompte(\OGIVE\ProjectBundle\Entity\Decompte $decompte) {
        $em= $this->_em;
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($decompte);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $decompte;
    }
    public function getAll($offset = null, $limit = null, $search_query = null, $project = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
        if ($search_query) {
            $qb->andWhere(
                    $qb->expr()->orX(
                            $qb->expr()->like('lower(e.monthName)', ':search_query'), $qb->expr()->like('lower(e.monthNumber)', ':search_query'), $qb->expr()->like('lower(e.searchData)', ':search_query')
            ));
            $search_query = strtolower($search_query);
            $qb->setParameter('search_query', '%' . strtolower($search_query) . '%');
        }
        
        if ($project && $project != "0") {
            $qb->join("e.project", 'u');
            $qb->andWhere('u.id = :project');
            $qb->setParameter("project", intval($project));
        }
        $qb->orderBy('e.monthNumber', 'ASC');
        
        if ($offset) {
            $qb->setFirstResult($offset);
        }
        if ($limit) {
            $qb->setMaxResults($limit);
        }
        return $qb->getQuery()->getResult();
    }
    
    public function getDecompteByMonthAndProject($month = null, $project = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
//        $qb->andWhere('e.monthNumber = 1');
        if ($month >= 0) {
            $qb->andWhere('e.monthNumber = :month');
            $qb->setParameter("month", intval($month));
        }
        if ($project >= 0) {
            $qb->join("e.project", 'u');
            $qb->andWhere('u.id = :project');
            $qb->setParameter("project", intval($project));
        }       
        try{
            return $qb->getQuery()->getSingleResult();
        } catch(\Exception $ex){
            return null;
        }
    }
    
    public function getDecomptePrecByMonthAndProject($month = null, $project = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
        if ($month >= 0) {
            $qb->andWhere('e.monthNumber < :month');
            $qb->setParameter("month", $month);
        }
        if ($project >= 0) {
            $qb->join("e.project", 'u');
            $qb->andWhere('u.id = :project');
            $qb->setParameter("project", $project);
        }
        $qb->orderBy('e.monthNumber', 'DESC');
        try{
            $decomptes = $qb->getQuery()->getResult();
            if($decomptes){
                return $decomptes[0];
            }else{
                return null;
            }
        } catch(\Exception $ex){
            return null;
        }
    }
    
    public function getDecompteNextByMonthAndProject($month = null, $project = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
        if ($month >= 0) {
            $qb->andWhere('e.monthNumber > :month');
            $qb->setParameter("month", $month);
        }
        if ($project >= 0) {
            $qb->join("e.project", 'u');
            $qb->andWhere('u.id = :project');
            $qb->setParameter("project", $project);
        }
        $qb->orderBy('e.monthNumber', 'ASC');
        try{
            $decomptes = $qb->getQuery()->getResult();
            if($decomptes){
                return $decomptes[0];
            }else{
                return null;
            }
        } catch(\Exception $ex){
            return null;
        }
    }
    
    public function getNextDecomptesOfProject($month = null, $project = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
        if ($month >= 0) {
            $qb->andWhere('e.monthNumber > :month');
            $qb->setParameter("month", $month);
        }
        if ($project >= 0) {
            $qb->join("e.project", 'u');
            $qb->andWhere('u.id = :project');
            $qb->setParameter("project", $project);
        }
        $qb->orderBy('e.monthNumber', 'ASC');
        try{
            $decomptes = $qb->getQuery()->getResult();
            if($decomptes){
                return $decomptes;
            }else{
                return null;
            }
        } catch(\Exception $ex){
            return null;
        }
    }
    
    public function getDecompteQueryBuilder() {
         return $this
          ->createQueryBuilder('e')
          ->where('e.status = :status')
          ->andWhere('e.state = :state')
          ->orderBy('e.reference', 'ASC')
          ->setParameter('status', 1)
         ->setParameter('state', 1);

    }
}

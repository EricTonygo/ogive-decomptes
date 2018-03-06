<?php

namespace OGIVE\ProjectBundle\Repository;


use Doctrine\ORM\EntityRepository;
/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends EntityRepository
{
    public function deleteProject(\OGIVE\ProjectBundle\Entity\Project $project) {
        $em= $this->_em;
        $repositoryTask = $em->getRepository("OGIVEProjectBundle:Task");
        $repositoryHolder = $em->getRepository("OGIVEProjectBundle:Holder");
        $repositoryOwner = $em->getRepository("OGIVEProjectBundle:Owner");
        $repositoryProjectManager = $em->getRepository("OGIVEProjectBundle:ProjectManager");
        $repositoryServiceProvider = $em->getRepository("OGIVEProjectBundle:ServiceProvider");
        $repositoryOtherContributor = $em->getRepository("OGIVEProjectBundle:OtherContributor");
        $repositoryDecompte = $em->getRepository("OGIVEProjectBundle:Decompte");
        $em->getConnection()->beginTransaction();
        try{
            $decomptes = $project->getDecomptes();
            foreach ($decomptes as $decompte) {
                $repositoryDecompte->removeDecompte($decompte);
            }
            $tasks = $project->getTasks();
            foreach ($tasks as $task) {
                $repositoryTask->deleteTask($task);
            }            
            $holders = $project->getHolders();
            foreach ($holders as $holder) {
                $repositoryHolder->deleteHolder($holder);
            }
            $owners = $project->getOwners();
            foreach ($owners as $owner) {
                $repositoryOwner->deleteOwner($owner);
            }
            $projectManagers = $project->getProjectManagers();
            foreach ($projectManagers as $projectManager) {
                $repositoryProjectManager->deleteProjectManager($projectManager);
            }
            $servicePrroviders = $project->getServiceProviders();
            foreach ($servicePrroviders as $servicePrrovider) {
                $repositoryServiceProvider->deleteServiceProvider($servicePrrovider);
            }
            $otherContributors = $project->getOtherContributors();
            foreach ($otherContributors as $otherContributor) {
                $repositoryOtherContributor->deleteOtherContributor($otherContributor);
            }
            $em->remove($project);
            $em->flush();
            $em->getConnection()->commit();
            return true;
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
    }


    public function saveProject(\OGIVE\ProjectBundle\Entity\Project $project) {
        $em= $this->_em;
        $project->setStatus(1);
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($project);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $project;
    }

    public function updateProject(\OGIVE\ProjectBundle\Entity\Project $project) {
        $em= $this->_em;
        $em->getConnection()->beginTransaction();
        try{
            $em->persist($project);
            $em->flush();
            $em->getConnection()->commit();
        } catch (Exception $ex) {
            $em->getConnection()->rollback();
            $em->close();
            throw $ex;
        }
        return $project;
    }
    public function getAll($offset = null, $limit = null, $search_query = null, $iduser = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = 1');
        if ($search_query) {
            $qb->andWhere(
                    $qb->expr()->orX(
                            $qb->expr()->like('lower(e.numeroMarche)', ':search_query'), $qb->expr()->like('lower(e.subject)', ':search_query'), $qb->expr()->like('lower(e.searchData)', ':search_query')
            ));
            $search_query = strtolower($search_query);
            $qb->setParameter('search_query', '%' . strtolower($search_query) . '%');
        }
        
        if ($iduser && $iduser != "0") {
            $qb->join("e.createdUser", 'u');
            $qb->andWhere('u.id = :user');
            $qb->setParameter("user", intval($iduser));
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
    
    public function getProjectQueryBuilder() {
         return $this
          ->createQueryBuilder('e')
          ->where('e.status = :status')
          ->andWhere('e.state = :state')
          ->orderBy('e.reference', 'ASC')
          ->setParameter('status', 1)
         ->setParameter('state', 1);

    }
}

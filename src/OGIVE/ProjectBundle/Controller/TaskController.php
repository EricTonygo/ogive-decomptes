<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\Task;
use OGIVE\ProjectBundle\Entity\Project;
use OGIVE\ProjectBundle\Entity\Owner;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;

/**
 * Task controller.
 *
 */
class TaskController extends Controller {

    /**
     * @Rest\View()
     * @Rest\Get("projects/{id}/tasks" , name="lot_index", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
    */
    public function getTasksAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $task = new Task();
        $em = $this->getDoctrine()->getManager();
        $page = 1;
        $maxResults = 6;
        $route_param_page = array();
        $route_param_search_query = array();
        $search_query = null;
        $start_date = null;
        $end_date = null;
        $owner = null;
        $domain = null;
        $placeholder = "Rechercher un appel d'offre...";
        if ($request->get('page')) {
            $page = intval(htmlspecialchars(trim($request->get('page'))));
            $route_param_page['page'] = $page;
        }
        if ($request->get('search_query')) {
            $search_query = htmlspecialchars(trim($request->get('search_query')));
            $route_param_search_query['search_query'] = $search_query;
        }
        if ($request->get('start-date')) {
            $start_date = htmlspecialchars(trim($request->get('start-date')));
            $route_param_search_query['start-date'] = $start_date;
        }
        if ($request->get('end-date')) {
            $end_date = htmlspecialchars(trim($request->get('end-date')));
            $route_param_search_query['end-date'] = $end_date;
        }
        if ($request->get('owner')) {
            $owner = htmlspecialchars(trim($request->get('owner')));
            $route_param_search_query['owner'] = $owner;
        }
        if ($request->get('domain')) {
            $domain = htmlspecialchars(trim($request->get('domain')));
            $route_param_search_query['domain'] = $domain;
        }
        $start_from = ($page - 1) * $maxResults >= 0 ? ($page - 1) * $maxResults : 0;
        $total_procedures = count($em->getRepository('OGIVEProjectBundle:Task')->getAllByQueriedParameters($search_query, $start_date, $end_date, $owner, $domain));
        $total_pages = ceil($total_procedures / $maxResults);
        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task);
        $lots = $em->getRepository('OGIVEProjectBundle:Task')->getAll($start_from, $maxResults, $search_query, $start_date, $end_date, $owner, $domain);
        $owners = $em->getRepository('OGIVEProjectBundle:Owner')->findBy(array("state" => 1, "status" => 1));
        $domains = $em->getRepository('OGIVEProjectBundle:Domain')->findBy(array("state" => 1, "status" => 1));
        if ($start_date && $end_date) {
            //$this->get('common_service')->getStatisticsOfProceduresByOwner($start_date, $end_date);
            $this->get('common_service')->getStatisticsOfProceduresByMonth($start_date, $end_date);
        }
        return $this->render('OGIVEProjectBundle:lot:index.html.twig', array(
                    'lots' => $lots,
                    'total_procedures' => $total_procedures,
                    'total_pages' => $total_pages,
                    'page' => $page,
                    'form' => $form->createView(),
                    'route_param_page' => $route_param_page,
                    'route_param_search_query' => $route_param_search_query,
                    'search_query' => $search_query,
                    'placeholder' => $placeholder,
                    'owners' => $owners,
                    'domains' => $domains,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'queried_owner' => $owner,
                    'queried_domain' => $domain,
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/tasks/new", name="task_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function addTaskAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $task = new Task();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task);
        return $this->render('OGIVEProjectBundle:task:add.html.twig', array(
                    'form' => $form->createView(),
                    'tab' => 3,
                    'project' => $project
        ));
    }
    
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{idProject}/tasks/{id}/tasks/new", name="sub_task_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function addSubTaskAction(Request $request, Task $parentTask) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $task = new Task();
        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task);
        return $this->render('OGIVEProjectBundle:task:add.html.twig', array(
                    'form' => $form->createView(),
                    'tab' => 3,
                    'parentTask' => $parentTask,
                    'project' => $parentTask->getProjectTask()
        ));
    }

    /**
     * @Rest\View()
     * @Rest\Get("/projects/{idProject}/tasks/{id}/update" , name="task_update_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function getUpdateTaskByIdAction(Task $task) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task, array('method' => 'PUT'));
        return $this->render('OGIVEProjectBundle:task:update.html.twig', array(
                    'task' => $task,
                    'project' => $task->getProjectTask(),
                    'tab' => 3,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/{id}/tasks/new", name="task_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postTaskAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $task = new Task();
        $repositoryTask = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Task');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setProject($project);
            $task->setParentTask(null);

            //***************gestion des sub tasks du projet ************************** */
            $subTasks = $task->getSubTasks();
            foreach ($subTasks as $subTask) {
                $subTask->setProject($project);
                $subTask->setParentTask($task);
            }
            $task = $repositoryTask->saveTask($task);
            return $this->redirect($this->generateUrl('project_tasks_get', array('id' => $task->getProject()->getId())));
        } else {

            $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task, array('method' => 'PUT'));
            return $this->render('OGIVEProjectBundle:task:add.html.twig', array(
                        'project' => $project,
                        'tab' => 3,
                        'form' => $form->createView()
            ));
        }
    }
    
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/projects/{idProject}/tasks/{id}/tasks/new", name="sub_task_add_post", options={ "method_prefix" = false, "expose" = true })
     */
    public function postSubTaskAction(Request $request, Task $parentTask) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $task = new Task();
        $repositoryTask = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Task');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setProject($parentTask->getProject());
            $task->setParentTask($parentTask);

            //***************gestion des sub tasks du projet ************************** */
            $subTasks = $task->getSubTasks();
            foreach ($subTasks as $subTask) {
                //$subTask->setProject($task->getProject());
                $subTask->setProjectTask($task->getProjectTask());
                $subTask->setParentTask($task);
            }
            $task = $repositoryTask->saveTask($task);
            return $this->redirect($this->generateUrl('project_tasks_get', array('id' => $task->getProject()->getId())));
        } else {

            $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task, array('method' => 'PUT'));
            return $this->render('OGIVEProjectBundle:task:add.html.twig', array(
                        'parentTask' => $parentTask,
                        'project' => $parentTask->getProjectTask(),
                        'tab' => 3,
                        'form' => $form->createView()
            ));
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Get("/projects/{idProject}/tasks/{id}/remove", name="task_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeTaskAction(Task $task) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $repositoryTask = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Task');
        if ($task) {
            $repositoryTask->deleteTask($task);
            return $this->redirect($this->generateUrl('project_tasks_get', array('id' => $task->getProject()->getId())));
        } else {
            return $this->redirect($this->generateUrl('project_tasks_get', array('id' => $task->getProject()->getId())));
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/projects/{idProject}/tasks/{id}/update", name="task_update_post", options={ "method_prefix" = false, "expose" = true })
     * @param Request $request
     */
    public function putTaskAction(Request $request, Task $task) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        return $this->updateTaskAction($request, $task);
    }

    public function updateTaskAction(Request $request, Task $task) {
        $repositoryTask = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Task');

        $originalSubTasks = new \Doctrine\Common\Collections\ArrayCollection();
        
        
        foreach ($task->getSubTasks() as $subTask) {
            $originalSubTasks->add($subTask);
        }
        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task, array('method' => 'PUT'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $task->setUpdatedUser($user);
            
            //***************gestion des tasks du project ************************** */
            // remove the relationship between the project and the tasks
            foreach ($originalSubTasks as $subTask) {
                if (false === $task->getSubTasks()->contains($subTask)) {
                    // remove the project from the projectManagers
                    $task->getSubTasks()->getTasks()->removeElement($task);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $repositoryTask->removeTask($subTask);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $subTasks = $task->getSubTasks();
            foreach ($subTasks as $subTask) {
                $subTask->setProjectTask($task->getProjectTask());
                //if($subTask->getProject() == null){
                    $subTask->setProject(null);
                //}
                if($subTask->getParentTask() == null){
                    $subTask->setParentTask($task);
                }
            }
            $task = $repositoryTask->updateTask($task);
            return $this->redirect($this->generateUrl('project_tasks_get', array('id' => $task->getProject()->getId())));
        } else {
            $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task, array('method' => 'PUT'));
            return $this->render('OGIVEProjectBundle:task:update.html.twig', array(
                        'task' => $task,
                        'project' => $task->getProjectTask(),
                        'tab' => 3,
                        'form' => $form->createView()
            ));
        }
    }

}

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
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/tasks/new", name="task_add_get", options={ "method_prefix" = false, "expose" = true })
     */
    public function addTaskAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $user = $this->getUser();
        if ($user->getId() != $project->getCreatedUser()->getId()) {
            return $this->generateUrl("project_tasks_get", array("id" => $project->getId()));
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
        $user = $this->getUser();
        if ($user->getId() != $parentTask->getProjectTask()->getCreatedUser()->getId()) {
            return $this->generateUrl("project_tasks_get", array("id" => $parentTask->getProjectTask()->getId()));
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
        $user = $this->getUser();
        if ($user->getId() != $task->getProjectTask()->getCreatedUser()->getId()) {
            return $this->generateUrl("project_tasks_get", array("id" => $task->getProjectTask()->getId()));
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
        $user = $this->getUser();
        if ($user->getId() != $project->getCreatedUser()->getId()) {
            return $this->generateUrl("project_tasks_get", array("id" => $project->getId()));
        }
        $task = new Task();
        $repositoryTask = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Task');
        $decompte_manager = $this->get('app.decompte_manager');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($task->getNumero() == null || $task->getNumero() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre tâche est sans numéro. Vueillez le remplir."], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getNom() == null || $task->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre tâche est sans désignation. Vueillez la remplir."], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getStartDate() && ($task->getEndDate() == null || $task->getEndDate() == "")) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de fin"], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getEndDate() && ($task->getStartDate() == null || $task->getStartDate() == "")) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de début"], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryTask->findOneBy(array('numero' => $task->getNumero(), "parentTask" => null, "project" => $project))) {
                return new JsonResponse(["success" => false, 'message' => 'Une tâche avec ce numéro existe déjà'], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getStartDate() && $task->getEndDate()) {
                if ($project->getStartDate() == null || $project->getStartDate() == "") {
                    return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de début du projet"], Response::HTTP_BAD_REQUEST);
                } elseif ($project->getEndDate() == null || $project->getEndDate() == "") {
                    return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de fin du projet"], Response::HTTP_BAD_REQUEST);
                } else {
                    $startDateTask = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $task->getStartDate()))));
                    $endDateTask = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $task->getEndDate()))));
                    $startDateProject = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $project->getStartDate()))));
                    $endDateProject = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $project->getEndDate()))));
                    if ($startDateProject == null) {
                        return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de début du projet"], Response::HTTP_BAD_REQUEST);
                    } elseif ($endDateProject == null) {
                        return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de fin du projet"], Response::HTTP_BAD_REQUEST);
                    } else {
                        if ($startDateTask < $startDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de début de cette tâche ne peut pas être inférieur à la date de début du projet " . $project->getStartDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($startDateTask > $endDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de début de cette tâche ne peut pas être supérieur à la date de fin du projet " . $project->getEndDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($endDateTask < $startDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de fin de cette tâche ne peut pas être inférieur à la date de début du projet " . $project->getStartDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($endDateTask > $endDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de fin de cette tâche ne peut pas être supérieur à la date de fin du projet " . $project->getEndDate()], Response::HTTP_BAD_REQUEST);
                        }
                    }
                }
            }
            $user = $this->getUser();
            $task->setProject($project);
            $task->setProjectTask($project);
            $task->setParentTask(null);

            //***************gestion des sub tasks du projet ************************** */
            $subTasks = $task->getSubTasks();
            foreach ($subTasks as $subTask) {
                if ($subTask->getNumero() == null || $subTask->getNumero() == "") {
                    return new JsonResponse(["success" => false, 'message' => "Vous avez des sous tache sans numéros. Vueillez les remplir. "], Response::HTTP_BAD_REQUEST);
                } else {
//                    if ($repositoryTask->findOneBy(array('numero' => $subTask->getNumero()))) {
//                        return new JsonResponse(["success" => false, 'message' => 'Une sous-tâche avec ce numéro existe déjà'], Response::HTTP_BAD_REQUEST);
//                    } #Il faudra recherche les tâches de même numero dans la liste des sous-tâche $subTasks
                    if ($subTask->getNom() == null || $subTask->getNom() == "") {
                        return new JsonResponse(["success" => false, 'message' => "La sous tache de numéro " . $subTask->getNumero() . " n'a pas de désignation" . "Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
                    }
                }
                $subTask->setProject(null);
                $subTask->setProjectTask($project);
                $subTask->setParentTask($task);
                $task->setCreatedUser($user);
            }
            $task->setCreatedUser($user);
            $task = $repositoryTask->saveTask($task);
            $decompte_manager->updateDecomptesDuringTaskUpdatingOrAdding($task);
            //return $this->redirect($this->generateUrl('project_tasks_get', array('id' => $task->getProjectTask()->getId())));
            $view = View::create(["message" => 'Tâche créée avec succès. Vous serez redirigé dans bientôt!', 'id_project' => $task->getProjectTask()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes'], Response::HTTP_BAD_REQUEST);
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
        $user = $this->getUser();
        if ($user->getId() != $parentTask->getProjectTask()->getCreatedUser()->getId()) {
            return $this->generateUrl("project_tasks_get", array("id" => $parentTask->getProjectTask()->getId()));
        }
        $task = new Task();
        $repositoryTask = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Task');

        $decompte_manager = $this->get('app.decompte_manager');

        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($task->getNumero() == null || $task->getNumero() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre tâche est sans numéro. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getNom() == null || $task->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre tâche est sans désignation. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getStartDate() && ($task->getEndDate() == null || $task->getEndDate() == "")) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de fin"], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getEndDate() && ($task->getStartDate() == null || $task->getStartDate() == "")) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de début"], Response::HTTP_BAD_REQUEST);
            }
            if ($repositoryTask->findOneBy(array('numero' => $task->getNumero(), 'parentTask' => $parentTask))) {
                return new JsonResponse(["success" => false, 'message' => 'Une tâche avec ce numéro existe déjà'], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getStartDate() && $task->getEndDate()) {
                $project = $task->getProjectTask();
                if ($project->getStartDate() == null || $project->getStartDate() == "") {
                    return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de début du projet"], Response::HTTP_BAD_REQUEST);
                } elseif ($project->getEndDate() == null || $project->getEndDate() == "") {
                    return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de fin du projet"], Response::HTTP_BAD_REQUEST);
                } else {
                    $startDateTask = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $task->getStartDate()))));
                    $endDateTask = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $task->getEndDate()))));
                    $startDateProject = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $project->getStartDate()))));
                    $endDateProject = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $project->getEndDate()))));
                    if ($startDateProject == null) {
                        return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de début du projet"], Response::HTTP_BAD_REQUEST);
                    } elseif ($endDateProject == null) {
                        return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de fin du projet"], Response::HTTP_BAD_REQUEST);
                    } else {
                        if ($startDateTask < $startDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de début de cette tâche ne peut pas être inférieur à la date de début du projet " . $project->getStartDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($startDateTask > $endDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de début de cette tâche ne peut pas être supérieur à la date de fin du projet " . $project->getEndDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($endDateTask < $startDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de fin de cette tâche ne peut pas être inférieur à la date de début du projet " . $project->getStartDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($endDateTask > $endDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de fin de cette tâche ne peut pas être supérieur à la date de fin du projet " . $project->getEndDate()], Response::HTTP_BAD_REQUEST);
                        }
                    }
                }
            }
            $user = $this->getUser();
            $task->setProject(null);
            $task->setProjectTask($parentTask->getProjectTask());
            $task->setParentTask($parentTask);

            //***************gestion des sub tasks du projet ************************** */
            $subTasks = $task->getSubTasks();
            foreach ($subTasks as $subTask) {
                if ($subTask->getNumero() == null || $subTask->getNumero() == "") {
                    return new JsonResponse(["success" => false, 'message' => "Vous avez des sous-tâches sans numéros. Vueillez les remplir. "], Response::HTTP_BAD_REQUEST);
                } else {
//                    if ($repositoryTask->findOneBy(array('numero' => $subTask->getNumero()))) {
//                        return new JsonResponse(["success" => false, 'message' => 'Une sous-tâche avec ce numéro existe déjà'], Response::HTTP_BAD_REQUEST);
//                    } #Il faudra recherche les tâches de même numero dans la liste des sous-tâche $subTasks
                    if ($subTask->getNom() == null || $subTask->getNom() == "") {
                        return new JsonResponse(["success" => false, 'message' => "La sous-tâche de numéro " . $subTask->getNumero() . " n'a pas de désignation" . "Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
                    }
                }
                $subTask->setProject(null);
                $subTask->setProjectTask($task->getProjectTask());
                $subTask->setParentTask($task);
                $subTask->setCreatedUser($user);
            }


            $task->setCreatedUser($user);
            $task = $repositoryTask->saveTask($task);
            $decompte_manager->updateDecomptesDuringTaskUpdatingOrAdding($task);
            $view = View::create(["message" => 'Tâche créée avec succès. Vous serez redirigé dans bientôt!', 'id_project' => $task->getProjectTask()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes'], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_OK)
     * @Rest\Delete("/projects/{idProject}/tasks/{id}/remove", name="task_delete", options={ "method_prefix" = false, "expose" = true })
     */
    public function removeTaskAction(Task $task) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $decompte_manager = $this->get('app.decompte_manager');
        $repositoryTask = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Task');
        if ($task) {
            $user = $this->getUser();
            if ($user->getId() != $task->getProjectTask()->getCreatedUser()->getId()) {
                return $this->generateUrl("project_tasks_get", array("id" => $task->getProjectTask()->getId()));
            }
            $repositoryTask->deleteTask($task);
            $decompte_manager->updateAllDecomptes($task->getProjectTask());
            $view = View::create(["message" => "Tâche supprimée avec succès !"]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["message" => "Tâche introuvable !"], Response::HTTP_NOT_FOUND);
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
        $user = $this->getUser();
        if ($user->getId() != $task->getProjectTask()->getCreatedUser()->getId()) {
            return $this->generateUrl("project_tasks_get", array("id" => $task->getProjectTask()->getId()));
        }
        return $this->updateTaskAction($request, $task);
    }

    public function updateTaskAction(Request $request, Task $task) {
        $repositoryTask = $this->getDoctrine()->getManager()->getRepository('OGIVEProjectBundle:Task');
        $decompte_manager = $this->get('app.decompte_manager');
        $originalSubTasks = new \Doctrine\Common\Collections\ArrayCollection();

        foreach ($task->getSubTasks() as $subTask) {
            $originalSubTasks->add($subTask);
        }
        $form = $this->createForm('OGIVE\ProjectBundle\Form\TaskType', $task, array('method' => 'PUT'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($task->getNumero() == null || $task->getNumero() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre tâche est sans numéro. Vueillez le remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getNom() == null || $task->getNom() == "") {
                return new JsonResponse(["success" => false, 'message' => "Vôtre tâche est sans désignation. Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getStartDate() && ($task->getEndDate() == null || $task->getEndDate() == "")) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de fin"], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getEndDate() && ($task->getStartDate() == null || $task->getStartDate() == "")) {
                return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de début"], Response::HTTP_BAD_REQUEST);
            }
            $taskEdit = $repositoryTask->findOneBy(array('numero' => $task->getNumero(), 'parentTask' => $task->getParentTask()));
            if ($taskEdit && $taskEdit->getId() != $task->getId()) {
                return new JsonResponse(["success" => false, 'message' => 'Une tâche avec ce numéro existe déjà'], Response::HTTP_BAD_REQUEST);
            }
            if ($task->getStartDate() && $task->getEndDate()) {
                $project = $task->getProjectTask();
                if ($project) {
                    $startDateTask = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $task->getStartDate()))));
                    $endDateTask = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $task->getEndDate()))));
                    $startDateProject = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $project->getStartDate()))));
                    $endDateProject = new \DateTime(date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $project->getEndDate()))));
                    if ($startDateProject == null) {
                        return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de début du projet"], Response::HTTP_BAD_REQUEST);
                    } elseif ($endDateProject == null) {
                        return new JsonResponse(["success" => false, 'message' => "Vueillez renseigner la date de fin du projet"], Response::HTTP_BAD_REQUEST);
                    } else {
                        if ($startDateTask < $startDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de début de cette tâche ne peut pas être inférieur à la date de début du projet " . $project->getStartDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($startDateTask > $endDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de début de cette tâche ne peut pas être supérieur à la date de fin du projet " . $project->getEndDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($endDateTask < $startDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de fin de cette tâche ne peut pas être inférieur à la date de début du projet " . $project->getStartDate()], Response::HTTP_BAD_REQUEST);
                        } elseif ($endDateTask > $endDateProject) {
                            return new JsonResponse(["success" => false, 'message' => "La date de fin de cette tâche ne peut pas être supérieur à la date de fin du projet " . $project->getEndDate()], Response::HTTP_BAD_REQUEST);
                        }
                    }
                }
            }

            $user = $this->getUser();
            $task->setUpdatedUser($user);

            //***************gestion des tasks du project ************************** */
            // remove the relationship between the project and the tasks
            foreach ($originalSubTasks as $subTask) {
                if (false === $task->getSubTasks()->contains($subTask)) {
                    // remove the project from the projectManagers
                    $task->getSubTasks()->removeElement($task);
                    // if it was a many-to-one relationship, remove the relationship like this
                    $repositoryTask->deleteTask($subTask);
                    // if you wanted to delete the Subscriber entirely, you can also do that
                    // $em->remove($domain);
                }
            }
            $subTasks = $task->getSubTasks();
            foreach ($subTasks as $subTask) {
                if ($subTask->getNumero() == null || $subTask->getNumero() == "") {
                    return new JsonResponse(["success" => false, 'message' => "Vous avez des sous tache sans numéros. Vueillez les remplir. "], Response::HTTP_BAD_REQUEST);
                } else {
                    $subTaskEdit = $repositoryTask->findOneBy(array('numero' => $subTask->getNumero(), 'parentTask' => $task));
                    if ($subTaskEdit && $subTaskEdit->getId() != $subTask->getId()) {
                        return new JsonResponse(["success" => false, 'message' => 'Une sous-tâche avec ce numéro existe déjà'], Response::HTTP_BAD_REQUEST);
                    }//#Il faudra recherche les tâches de même numero dans la liste des sous-tâche $subTasks
                    if ($subTask->getNom() == null || $subTask->getNom() == "") {
                        return new JsonResponse(["success" => false, 'message' => "La sous tache de numéro " . $subTask->getNumero() . " n'a pas de désignation" . "Vueillez la remplir. "], Response::HTTP_BAD_REQUEST);
                    }
                }
                $subTask->setProjectTask($task->getProjectTask());
                $subTask->setProject(null);
                if ($subTask->getParentTask() == null) {
                    $subTask->setParentTask($task);
                }
                $subTask->setUpdatedUser($user);
            }
            $task = $repositoryTask->updateTask($task);
            $decompte_manager->updateDecomptesDuringTaskUpdatingOrAdding($task);
            //return $this->redirect($this->generateUrl('project_tasks_get', array('id' => $task->getProjectTask()->getId())));
            $view = View::create(["message" => 'Tâche modifée avec succès. Vous serez redirigé dans bientôt!', 'id_project' => $task->getProjectTask()->getId()]);
            $view->setFormat('json');
            return $view;
        } else {
            return new JsonResponse(["success" => false, 'message' => 'Le formulaire a été soumis avec les données incorrectes'], Response::HTTP_BAD_REQUEST);
        }
    }

}

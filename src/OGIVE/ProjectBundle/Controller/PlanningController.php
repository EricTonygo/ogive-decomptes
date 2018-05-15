<?php

namespace OGIVE\ProjectBundle\Controller;

use OGIVE\ProjectBundle\Entity\Project;
use OGIVE\ProjectBundle\Entity\Task;
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
 * Planning controller.
 *
 */
class PlanningController extends Controller {

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/work-schedule", name="project_work_schedule", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectWorkScheduleAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $day_project_list = array();
        $day_project_list_with_month = array();
        if ($project->getStartDate() && $project->getEndDate()) {
            $project_start_date = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $project->getStartDate()))));
            $project_start_time = strtotime(str_replace('/', '-', $project->getStartDate()));
            $project_end_date = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $project->getEndDate()))));
            $project_end_time = strtotime(str_replace('/', '-', $project->getEndDate()));
            $project_next_day_date = $project_start_date;
            $project_next_day_time = $project_start_time;

            while ($project_next_day_date <= $project_end_date) {
                $day_project_list[] = $project_next_day_date;
                $day_project_list_with_month[date('M/Y', $project_next_day_time)][] = date('d', $project_next_day_time);
                $project_next_day_time = mktime(0, 0, 0, date("n", $project_next_day_time), date("j", $project_next_day_time) + 1, date("Y", $project_next_day_time));
                $project_next_day_date = new \DateTime(date("Y-m-d", $project_next_day_time));
            }
        }
        $tasks_list = $this->getListOfTasksAndWorkPlannification($project, $day_project_list);
        return $this->render('OGIVEProjectBundle:work-schedule:work-schedule.html.twig', array(
                    'tab' => 7,
                    'tasks' => $project->getTasks(),
                    'tasks_list' => $tasks_list,
                    'project' => $project,
                    'day_project_list' => $day_project_list,
                    'day_project_list_with_month' => $day_project_list_with_month
        ));
    }

    public function getListOfTasksAndWorkPlannification(Project $project, $day_project_list) {
        $tasks = $project->getTasks();
        $tasks_list = array();
        foreach ($tasks as $task) {
            $tasks_list = $this->createWorkPlanningTask($task, $tasks_list, $day_project_list);
        }
        return $tasks_list;
    }

    public function createWorkPlanningTask(Task $task, $tasks_list, $day_project_list) {
        $task_planning = array_fill(0, count($day_project_list), 0);
        $subTasks = $task->getSubTasks();
        if ($subTasks && count($subTasks) == 0 && $task->getStartDate() && $task->getEndDate()) {
            $task_start_date = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $task->getStartDate()))));
            $task_start_time = strtotime(str_replace('/', '-', $task->getStartDate()));
            $task_end_date = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $task->getEndDate()))));
            $task_end_time = strtotime(str_replace('/', '-', $task->getEndDate()));
            $task_next_day_date = $task_start_date;
            $task_next_day_time = $task_start_time;

            while ($task_next_day_date <= $task_end_date) {
                if (in_array($task_next_day_date, $day_project_list)) {
                    $task_planning[array_search($task_next_day_date, $day_project_list)] = 1;
                }
                $task_next_day_time = mktime(0, 0, 0, date("n", $task_next_day_time), date("j", $task_next_day_time) + 1, date("Y", $task_next_day_time));
                $task_next_day_date = new \DateTime(date("Y-m-d", $task_next_day_time));
            }
        }
        
        $tasks_list[] = array(
            "id" => $task->getId(),
            "numero" => $task->getNumero(),
            "nom" => $task->getNom(),
            "color" => $this->getTaskColor(rand(0, 9)),
            "sub_task_length" => count($subTasks),
            "task_planning" => $task_planning
        );
        
        if($subTasks && count($subTasks) > 0){
            foreach ($subTasks as $subTask) {
                $tasks_list = $this->createWorkPlanningTask($subTask, $tasks_list, $day_project_list);
            }
        }
        return $tasks_list;
    }
    
    public function getTaskColor($index){
        $color_list = array("#F0C300", "#74D0F1", "#FF0000", "#2D241E", "#16B84E", "#FF0921", "#8E5434", "#FF7F00", "#8E5434", "#6E0B14");
        return $color_list[$index];
    }


    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Get("/projects/{id}/financial-schedule", name="project_financial_schedule", options={ "method_prefix" = false, "expose" = true })
     */
    public function getProjectFinancialScheduleAction(Request $request, Project $project) {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
        $month_project_list = array();
        if ($project->getStartDate() && $project->getEndDate()) {
            $project_start_date = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $project->getStartDate()))));
            $project_start_time = strtotime(str_replace('/', '-', $project->getStartDate()));
            $project_end_date = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $project->getEndDate()))));
            $project_end_time = strtotime(str_replace('/', '-', $project->getEndDate()));
            $project_next_day_date = $project_start_date;
            $project_next_day_time = $project_start_time;

            while ($project_next_day_date <= $project_end_date) {
                if(!in_array(date('M/Y', $project_next_day_time), $month_project_list)){
                    $month_project_list[] = date('M/Y', $project_next_day_time);
                }
                $project_next_day_time = mktime(0, 0, 0, date("n", $project_next_day_time), date("j", $project_next_day_time) + 1, date("Y", $project_next_day_time));
                $project_next_day_date = new \DateTime(date("Y-m-d", $project_next_day_time));
            }
        }
        $tasks_list = $this->getListOfTasksAndFinancialPlannification($project, $month_project_list);
        return $this->render('OGIVEProjectBundle:financial-schedule:financial-schedule.html.twig', array(
                    'tab' => 8,
                    'tasks' => $project->getTasks(),
                    'tasks_list' => $tasks_list,
                    'project' => $project,
                    'month_project_list' => $month_project_list
        ));
    }
    
    public function getListOfTasksAndFinancialPlannification(Project $project, $day_project_list) {
        $tasks = $project->getTasks();
        $tasks_list = array();
        foreach ($tasks as $task) {
            $tasks_list = $this->createFinancialPlanningTask($task, $tasks_list, $day_project_list);
        }
        return $tasks_list;
    }

    public function createFinancialPlanningTask(Task $task, $tasks_list, $month_project_list) {
        $task_planning = array_fill(0, count($month_project_list), 0);
        $month_task_list = array();
        $subTasks = $task->getSubTasks();
        if ($subTasks && count($subTasks) == 0 && $task->getStartDate() && $task->getEndDate()) {
            $task_start_date = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $task->getStartDate()))));
            $task_start_time = strtotime(str_replace('/', '-', $task->getStartDate()));
            $task_end_date = new \DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $task->getEndDate()))));
            $task_end_time = strtotime(str_replace('/', '-', $task->getEndDate()));
            $task_next_day_date = $task_start_date;
            $task_next_day_time = $task_start_time;

            while ($task_next_day_date <= $task_end_date) {
                if (!in_array(date('M/Y', $task_next_day_time), $month_task_list)) {
                    $month_task_list[] = date('M/Y', $task_next_day_time);
                }
                $task_next_day_time = mktime(0, 0, 0, date("n", $task_next_day_time), date("j", $task_next_day_time) + 1, date("Y", $task_next_day_time));
                $task_next_day_date = new \DateTime(date("Y-m-d", $task_next_day_time));
            }
            foreach ($month_task_list as $month_task) {
                $task_planning[array_search($month_task, $month_project_list)] = round($task->getQtePrevueProjetExec()*$task->getPrixUnitaire()/count($month_task_list));
            }
        }
        $tasks_list[] = array(
            "id" => $task->getId(),
            "numero" => $task->getNumero(),
            "nom" => $task->getNom(),
            "cost_currency" => $task->getProjectTask()->getProjectCostCurrency(),
            "sub_task_length" => count($subTasks),
            "task_planning" => $task_planning
        );
        
        if($subTasks && count($subTasks) > 0){
            foreach ($subTasks as $subTask) {
                $tasks_list = $this->createFinancialPlanningTask($subTask, $tasks_list, $month_project_list);
            }
        }
        return $tasks_list;
    }

}

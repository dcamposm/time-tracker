<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Repository\TaskRepository;
use App\Entity\Task;

class TaskController extends AbstractController
{
    #[Route('/', name: 'app_task')]
    public function index(TaskRepository $taskRepository): Response
    {
    	$task = $taskRepository->findOneBy(
                    ['time_end' => null],
                );
    	

    	$currentTask = false;
    	$time = "00:00:00";

    	if (!empty($task)) {
    		$currentTask = $task;

    		$interval = date_diff(new \DateTime(), $task->getTimeStart());
    		$time = $interval->format('%H:%i:%s');
    	}
		//dump($currentTask );
    	//die;
        return $this->render('task/index.html.twig', [
        	'time' => $time,
			'currentTask' => $currentTask,
            'taskList' => $taskRepository->findFormatedSumHours(),
        ]);
    }
	
	#[Route('/start', name: 'start_task', methods: ['GET'])]
	public function start(TaskRepository $taskRepository, Request $request): Response
    {
    	//dump($request->get('task'));
    	//die;
		if ($request->get('task')['description']  == null) {
			return $this->redirectToRoute('app_task');
		}
		
		$description = $request->get('task')['description'];

		//dump($task);
    	//die;
		
		$task = new Task($description);
		$task->setTimeStart(new \DateTime());
		
		$taskRepository->add($task,true);

		return $this->redirectToRoute('app_task');
    }

    #[Route('/stop', name: 'stop_task', methods: ['GET'])]
	public function stop(TaskRepository $taskRepository, Request $request): Response
    {
		if ($request->get('task')['description']  == null) {
			return $this->redirectToRoute('app_task');
		}
		
		$description = $request->get('task')['description'];
		
		$task = $taskRepository->findOneBy(
                    ['description' => $description,
                    'time_end' => null],
                );
		
		//dump($task);
    	//die;

		if (!empty($task)) {
	    	$task->setTimeEnd(new \DateTime());

	    	$taskRepository->add($task,true);	
		}


		return $this->redirectToRoute('app_task');
    }
}

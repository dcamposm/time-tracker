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
        return $this->render('task/index.html.twig', [
            'taskList' => $taskRepository->findAll(),
        ]);
    }
	
	#[Route('/start', name: 'start_task', methods: ['GET'])]
	public function new(TaskRepository $taskRepository, Request $request): Response
    {
    	//dump($request->get('task'));
    	//die;
		if ($request->get('task')['description']  == null) {
			return $this->redirectToRoute('app_task');
		}
		
		$description = $request->get('task')['description'];
		
		$task = $taskRepository->findBy(
                    ['description' => $description],
                );
		//dump($task);
    	//die;
		if (!isset($task->id)) {
			$task = new Task($description);
			$task->setTimeStart(new \DateTime());
			
			$taskRepository->add($task,true);
		}

		return $this->render('task/index.html.twig', [
            'taskList' => $taskRepository->findAll(),
        ]);
    }
}

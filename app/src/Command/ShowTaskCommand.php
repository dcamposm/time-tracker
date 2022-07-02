<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\Table;
use App\Entity\Task;
use App\Repository\TaskRepository;


#[AsCommand(
    name: 'app:show-task',
    description: 'Show a list of all the tasks',
)]
class ShowTaskCommand extends Command
{
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command show a list of all the tasks...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $table = new Table($output);
        
        $taskList = [];

        $tasks =  $this->taskRepository->findAll();

        foreach ($tasks as $task) {
            $array = [$task->getDescription(), $task->getTimeStart()->format('Y-m-d H:i:s'), $task->getTimeEnd()->format('Y-m-d H:i:s'), $task->calcHours()];

            array_push($taskList, $array);
        }

        $table->setHeaderTitle('TASK List')
            ->setHeaders(['DESCRIPTION', 'START TIME', 'END TIME', 'TOTAL TIME'])
            ->setRows($taskList);

          $table->render();

        return Command::SUCCESS;
    }
}

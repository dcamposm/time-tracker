<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use App\Entity\Task;
use App\Repository\TaskRepository;

#[AsCommand(
    name: 'app:create-task',
    description: 'Start/End a Task',
)]
class CreateTaskCommand extends Command
{
    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to strat or end a task...')
            ->addArgument('option', InputArgument::REQUIRED, 'Start or end input')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $option = $input->getArgument('option');

        $io->note(sprintf('You passed an argument: %s', $option));

        $helper = $this->getHelper('question');
        $question = new Question("Enter your task: ", "guest");

        $description = $helper->ask($input, $output, $question);
        $message = sprintf("Your task is %s", $description);

        $output->writeln($message);

        $task = $this->taskRepository->findOneBy(
                ['description' => $description,
                'time_end' => null],
            );

        if ($option == "start") {
            if (empty($task)) {
                $task = new Task($description);
                $task->setTimeStart(new \DateTime());
                
                $this->taskRepository->add($task,true);

                $output->writeln("Task started");
            } 
            else $io->warning(sprintf('Task "%s" is already started!', $description));
        } 
        elseif ($option == "end") {
            if (!empty($task)) {
                $task->setTimeEnd(new \DateTime());

                $this->taskRepository->add($task,true);   

                $output->writeln("Task ended");
            } 
            else $io->warning(sprintf('Task "%s" is not already started!', $description));
        } 
        else {
            $io->warning(sprintf('You passed invalid argument: %s', $option));
            return Command::INVALID;
        }

        return Command::SUCCESS;
    }
}

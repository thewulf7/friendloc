<?php
namespace thewulf7\friendloc\command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Create new user.')
            ->addArgument(
                'email',
                InputArgument::OPTIONAL,
                'Email:'
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Name:'
            );

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $email    = '';
        $name     = '';
        $password = '';

        $helper = $this->getHelper('question');

        $fields = ['email', 'name'];

        foreach ($fields as $field)
        {
            $$field = $input->getArgument($field);

            if (!$$field)
            {
                $question = new Question(ucfirst($field) . ':', false);
                $$field   = $helper->ask($input, $output, $question);
            }
        }

        $output->writeln($email);
    }
}
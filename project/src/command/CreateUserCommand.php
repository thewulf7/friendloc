<?php
namespace thewulf7\friendloc\command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use thewulf7\friendloc\components\Auth;
use thewulf7\friendloc\components\ElasticSearch;
use thewulf7\friendloc\models\User;

/**
 * Class CreateUserCommand
 *
 * @package thewulf7\friendloc\command
 */
class CreateUserCommand extends Command
{

    /**
     * @var ElasticSearch
     */
    private $elastic;

    /**
     * CreateUserCommand constructor.
     *
     * @param ElasticSearch $e
     */
    public function __construct(ElasticSearch $e)
    {
        $this->elastic = $e;
        parent::__construct();
    }

    /**
     *
     */
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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $email = '';
        $name  = '';

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

        $password = Auth::generatePassword();
        $salt     = Auth::generateSalt();

        $output->writeln('Your password: ' . $password);

        $em = $this->getHelper('em')->getEntityManager();

        $model = new User();
        $model
            ->setEmail($email)
            ->setName($name)
            ->setPasswd(Auth::createPassword($password, $salt))
            ->setApproved(1)
            ->setCreated(new \DateTime('now'))
            ->setSalt($salt)
            ->setLatlng([56, 30])
            ->setLocationName('Saint-P');

        $em->persist($model);
        $em->flush();

        $this->elastic->persist($model);
    }
}
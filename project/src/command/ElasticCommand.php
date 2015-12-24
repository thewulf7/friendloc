<?php
namespace thewulf7\friendloc\command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticCommand extends Command
{

    private $_elastic;

    public function __construct(\thewulf7\friendloc\components\ElasticSearch $elastic)
    {
        $this->_elastic = $elastic;

        parent::__construct();
    }

    /**
     * Get Elastic
     *
     * @return \thewulf7\friendloc\components\ElasticSearch
     */
    public function getElastic()
    {
        return $this->_elastic;
    }

    protected function configure()
    {
        $this
            ->setName('elastic:update')
            ->setDescription('Update all indexes');

        parent::configure();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $ent = $this->getElastic()->getMappings();

        $i = 0;

        foreach ($ent as $entity)
        {
            if($this->getElastic()->createIndex($entity)){
                $i++;
            }
        }

        $output->writeln('Created indexes:' . $i);
    }
}
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

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $ent = $this->getElastic()->getMappings();

        $i = 0;

        foreach ($ent as $entity)
        {
            if ($entity['body']['settings']['autocomplete'])
            {
                $entity['body']['settings']['analysis'] = [
                    'filter'   => [
                        'autocomplete_filter' => [
                            'type'        => "nGram",
                            'min_gram'    => 2,
                            'max_gram'    => 10,
                            'token_chars' => [
                                'letter',
                                'digit',
                                'punctuation',
                                'symbol',
                            ],
                        ],
                    ],
                    'analyzer' => [
                        'autocomplete' => [
                            'type'      => 'custom',
                            'tokenizer' => 'whitespace',
                            'filter'    => [
                                'lowercase',
                                'autocomplete_filter',
                                'asciifolding',
                            ],
                        ],
                    ],
                ];
            }

            unset($entity['body']['settings']['autocomplete']);
            if ($this->getElastic()->createIndex($entity))
            {
                $i++;
            }
        }

        $output->writeln('Created indexes:' . $i);
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
}
<?php
namespace Ibrows\RestBundle\Debug\Converter;

use Doctrine\Bundle\DoctrineBundle\DataCollector\DoctrineDataCollector;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class Db implements ConverterInterface
{
    /**
     * {@inheritdoc}
     * @param DoctrineDataCollector $dataCollector
     */
    public function convert(DataCollectorInterface $dataCollector)
    {
        return [
            'mapping_errors' => $dataCollector->getMappingErrors(),
            'invalid_entities' => $dataCollector->getInvalidEntityCount(),
            'query_count' => $dataCollector->getQueryCount(),
            'query_time' => round($dataCollector->getTime()) . ' ms',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(DataCollectorInterface $dataCollector)
    {
        return $dataCollector instanceof DoctrineDataCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'db';
    }
}

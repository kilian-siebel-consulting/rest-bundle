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
            'mappingErrors' => $dataCollector->getMappingErrors(),
            'invalidEntities' => $dataCollector->getInvalidEntityCount(),
            'queryCount' => $dataCollector->getQueryCount(),
            'queryTime' => round($dataCollector->getTime()) . ' ms',
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

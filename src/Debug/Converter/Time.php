<?php
namespace Ibrows\RestBundle\Debug\Converter;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;

class Time implements ConverterInterface
{
    /**
     * {@inheritdoc}
     * @param TimeDataCollector $dataCollector
     */
    public function convert(DataCollectorInterface $dataCollector)
    {
        return round((microtime(true) * 1000) - $dataCollector->getStartTime()) . ' ms';
    }

    /**
     * {@inheritdoc}
     */
    public function supports(DataCollectorInterface $dataCollector)
    {
        return $dataCollector instanceof TimeDataCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'time_elapsed';
    }
}
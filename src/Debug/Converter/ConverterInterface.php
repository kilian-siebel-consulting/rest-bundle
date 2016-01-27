<?php
namespace Ibrows\RestBundle\Debug\Converter;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

interface ConverterInterface
{
    /**
     * @param DataCollectorInterface $dataCollector
     * @return mixed|null
     */
    public function convert(DataCollectorInterface $dataCollector);

    /**
     * @param DataCollectorInterface $dataCollector
     * @return boolean
     */
    public function supports(DataCollectorInterface $dataCollector);

    /**
     * @return string
     */
    public function getName();
}

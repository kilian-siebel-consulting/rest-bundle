<?php
namespace Ibrows\RestBundle\Debug\Converter;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;

class Memory implements ConverterInterface
{
    /**
     * {@inheritdoc}
     * @param MemoryDataCollector $dataCollector
     */
    public function convert(DataCollectorInterface $dataCollector)
    {
        return $this->formatBytes($dataCollector->getMemory());
    }

    /**
     * {@inheritdoc}
     */
    public function supports(DataCollectorInterface $dataCollector)
    {
        return $dataCollector instanceof MemoryDataCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'memory';
    }

    /**
     * @param int $bytes
     * @return string
     */
    protected function formatBytes($bytes)
    {
        $precision = 2;

        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

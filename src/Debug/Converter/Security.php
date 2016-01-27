<?php
namespace Ibrows\RestBundle\Debug\Converter;

use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class Security implements ConverterInterface
{
    /**
     * {@inheritdoc}
     * @param SecurityDataCollector $dataCollector
     */
    public function convert(DataCollectorInterface $dataCollector)
    {
        return [
            'enabled'       => $dataCollector->isEnabled(),
            'user'          => (string)$dataCollector->getUser(),
            'roles'         => $dataCollector->getRoles(),
            'authenticated' => $dataCollector->isAuthenticated(),
            'token'         => $dataCollector->getTokenClass(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(DataCollectorInterface $dataCollector)
    {
        return $dataCollector instanceof SecurityDataCollector;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'security';
    }
}

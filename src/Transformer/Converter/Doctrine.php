<?php
namespace Ibrows\RestBundle\Transformer\Converter;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class Doctrine implements ConverterInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * Doctrine constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource($className, $identifier)
    {
        return $this->objectManager->find($className, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceProxy($className, $identifier)
    {
        if ($this->objectManager instanceof EntityManagerInterface) {
            return $this->objectManager->getReference($className, $identifier);
        }
        return $this->getResource($className, $identifier);
    }
}

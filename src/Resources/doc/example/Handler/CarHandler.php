<?php
namespace Ibrows\ExampleBundle\Handler;

use Doctrine\Common\Collections\Collection;
use Ibrows\AppBundle\Entity\Car;
use Ibrows\AppBundle\Entity\Wheel;
use Ibrows\AppBundle\Repository\CarRepository;
use Ibrows\RestBundle\Pagination\PaginationConfig;

class CarHandler
{
    /**
     * @var CarRepository
     */
    private $repository;

    /**
     * CarHandler constructor.
     *
     * @param CarRepository $repository
     */
    public function __construct(
        CarRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @param int $lastId
     *
     * @param     $sortBy
     * @param     $sortDir
     * @return Collection|\Ibrows\AppBundle\Entity\Car[]
     */
    public function getList($limit, $lastId, $sortBy, $sortDir)
    {
        return $this->repository->getSorted($sortBy, $sortDir, $lastId, $limit);
    }

    /**
     * @param  $car
     */
    public function create(Car $car)
    {
        $this->repository->persist($car);
        $this->repository->flush($car);
    }

    /**
     * @param Car $car
     */
    public function update(Car $car)
    {
        $this->repository->flush($car);
    }

    /**
     * @param Car $car
     */
    public function delete(Car $car)
    {
        $this->repository->remove($car);
        $this->repository->flush($car);
    }

    /**
     * @param Wheel $wheel
     */
    public function addWheel(Wheel $wheel)
    {
        $wheel->getCar()->getWheels()->add($wheel);
        $this->update($wheel->getCar());
    }

    /**
     * @param Wheel $wheel
     */
    public function removeWheel(Wheel $wheel)
    {
        $wheel->getCar()->getWheels()->removeElement($wheel);

        $this->update($wheel->getCar());
    }
}
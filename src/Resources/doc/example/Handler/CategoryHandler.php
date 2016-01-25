<?php
namespace Ibrows\ExampleBundle\Handler;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Ibrows\AppBundle\Entity\Car;
use Ibrows\AppBundle\Entity\Category;
use Ibrows\AppBundle\Repository\CategoryRepository;

class CategoryHandler
{
    /**
     * @var CategoryRepository
     */
    private $repository;

    /**
     * CarHandler constructor.
     *
     * @param CategoryRepository $repository
     */
    public function __construct(
        CategoryRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @param int $lastId
     *
     * @return Collection|Car[]
     */
    public function getList($limit, $lastId)
    {
        $criteria = new Criteria();
        $criteria->andWhere($criteria->expr()->gt('id', $lastId));

        return $this->repository
            ->matching($criteria)
            ->slice(0, $limit);
    }

    /**
     * @param Category $car
     */
    public function create(Category $car)
    {
        $this->repository->persist($car);
        $this->repository->flush($car);
    }

    /**
     * @param Category $car
     */
    public function delete(Category $car)
    {
        $this->repository->remove($car);
        $this->repository->flush($car);
    }

    /**
     * @param int $id
     * @return Category|null
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }
}
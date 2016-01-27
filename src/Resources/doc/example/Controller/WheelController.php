<?php
namespace Ibrows\ExampleBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Request\ParamFetcher;
use Ibrows\AppBundle\Entity\Car;
use Ibrows\AppBundle\Entity\Wheel;
use Ibrows\ExampleBundle\Handler\CarHandler;
use Ibrows\RestBundle\Annotation as IbrowsAPI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class WheelController
 *
 * @package Ibrows\ExampleBundle\Controller
 * @FOSRest\Route(service="example.controller.wheel")
 */
class WheelController
{
    /**
     * @var CarHandler
     */
    private $carHandler;

    /**
     * CarController constructor.
     *
     * @param CarHandler $carHandler
     */
    public function __construct(
        CarHandler $carHandler
    ) {
        $this->carHandler = $carHandler;
    }

    /**
     * @ApiDoc(
     *     section="Car",
     *     description="Get list of wheels",
     *     resource=true,
     *     output={
     *         "class" = "Ibrows\AppBundle\Entity\Wheel[]",
     *         "groups" = { "wheel_list" }
     *     }
     * )
     *
     * @FOSRest\Get("/cars/{car}/wheels")
     *
     * @param Car          $car
     * @param ParamFetcher $paramFetcher
     *
     * @ParamConverter(name="car", converter="doctrine.orm", class="IbrowsAppBundle:Car")
     *
     * @FOSRest\QueryParam(name="limit", requirements="\d+", default="10", description="Limit Results")
     * @FOSRest\QueryParam(name="lastId", requirements="\d+", default="0", description="Last Result")
     *
     * @IbrowsAPI\View(
     *     serializerGroups={ "wheel_list" },
     *     routeParams={ "car" },
     * )
     *
     * @return Wheel[]
     */
    public function cgetAction(Car $car, ParamFetcher $paramFetcher)
    {
        $criteria = new Criteria();
        $criteria->andWhere($criteria->expr()->gt('id', $paramFetcher->get('lastId')));
        return $car
            ->getWheels()
            ->matching($criteria)
            ->slice(0, $paramFetcher->get('limit'));
    }

    /**
     * @ApiDoc(
     *     section="Car",
     *     description="Create a car wheel",
     *     input="Ibrows\AppBundle\Entity\Wheel"
     * )
     *
     * @FOSRest\Post("/cars/{car}/wheels")
     *
     * @param Car   $car
     * @param Wheel $wheel
     *
     * @ParamConverter(name="car", converter="doctrine.orm", class="IbrowsAppBundle:Car")
     * @ParamConverter(
     *     "wheel",
     *     converter="ibrows_rest.request_body",
     *     class="Ibrows\AppBundle\Entity\Wheel",
     * )
     *
     * @IbrowsAPI\View(
     *     statusCode=201
     * )
     */
    public function postAction(Car $car, Wheel $wheel)
    {
        $wheel->setCar($car);

        $this->carHandler->addWheel($wheel);
    }

    /**
     * @ApiDoc(
     *     section="Car",
     *     description="Delete a car wheel"
     * )
     *
     * @FOSRest\Delete("/wheels/{wheel}")
     *
     * @param Wheel $wheel
     *
     * @ParamConverter(name="wheel", converter="doctrine.orm", class="IbrowsAppBundle:Wheel")
     *
     * @IbrowsAPI\View(
     *     statusCode=204
     * )
     */
    public function removeAction(Wheel $wheel)
    {
        $this->carHandler->removeWheel($wheel);
    }
}

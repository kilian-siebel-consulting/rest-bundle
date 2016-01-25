<?php
namespace Ibrows\ExampleBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Ibrows\AppBundle\Entity\Category;
use Ibrows\ExampleBundle\Handler\CategoryHandler;
use Ibrows\RestBundle\Annotation as IbrowsAPI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class CategoryController
 *
 * @package Ibrows\ExampleBundle\Controller
 * @FOSRest\Route("/categories", service="example.controller.category")
 * @FOSRest\RouteResource("Category")
 */
class CategoryController implements ClassResourceInterface
{
    /**
     * @var CategoryHandler
     */
    private $categoryHandler;

    /**
     * CategoryController constructor.
     *
     * @param CategoryHandler $categoryHandler
     */
    public function __construct(
        CategoryHandler $categoryHandler
    ) {
        $this->categoryHandler = $categoryHandler;
    }

    /**
     * @ApiDoc(
     *     section="Category",
     *     description="Get list of categories",
     *     resource=true,
     *     output={
     *         "class" = "Ibrows\AppBundle\Entity\Category[]",
     *         "groups" = { "category_list" }
     *     }
     * )
     *
     * @FOSRest\Get()
     *
     * @param ParamFetcher $paramFetcher
     * @FOSRest\QueryParam(name="limit", requirements="\d+", default="10", description="Limit Results")
     * @FOSRest\QueryParam(name="lastId", requirements="\d+", description="Last Result")
     * @FOSRest\QueryParam(name="sortBy", requirements="\w+", description="sortBy")
     *
     * @IbrowsAPI\View(
     *     serializerGroups={ "category_list" }
     * )
     *
     * @return Category[]
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {
        $this->categoryHandler->getList(
            (int)$paramFetcher->get('limit'),
            (int)$paramFetcher->get('lastId')
        );
    }

    /**
     * @ApiDoc(
     *     section="Category",
     *     description="Get details of a category",
     *     resource=true,
     *     output={
     *         "class" = "Ibrows\AppBundle\Entity\Category",
     *         "groups" = { "category_detail" },
     *     },
     * )
     *
     * @FOSRest\Get(path="/{category}")
     *
     * @param Category $category
     * @ParamConverter("category", converter="doctrine.orm", class="IbrowsAppBundle:Category")
     *
     * @IbrowsAPI\View(
     *     serializerGroups={ "category_detail" }
     * )
     * @return Category
     */
    public function getAction(Category $category)
    {
        return $category;
    }

    /**
     * @ApiDoc(
     *     section="Category",
     *     description="Create a category",
     *     input="Ibrows\AppBundle\Entity\Category"
     * )
     *
     * @FOSRest\Post()
     *
     * @param Category $category
     * @ParamConverter(
     *     "category",
     *     converter="ibrows_rest.request_body",
     *     class="Ibrows\AppBundle\Entity\Category",
     * )
     *
     * @IbrowsAPI\View(
     *     statusCode=201
     * )
     */
    public function postAction(Category $category)
    {
        $this->categoryHandler->create($category);
    }

    /**
     * @ApiDoc(
     *     section="Category",
     *     description="Delete a category",
     * )
     *
     * @FOSRest\Delete(path="/{category}")
     *
     * @param Category $category
     * @ParamConverter("category", converter="doctrine.orm", class="IbrowsAppBundle:Category")
     *
     * @IbrowsAPI\View(
     *     statusCode=204,
     * )
     */
    public function deleteAction(Category $category)
    {
        $this->categoryHandler->delete($category);
    }
}
<?php

namespace Ibrows\RestBundle\ParamConverter;

use Ibrows\RestBundle\Transformer\TransformerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResourceUrlParamConverter implements ParamConverterInterface
{
    /** @var  TransformerInterface */
    private $resourceTransformer;

    /**
     * ResourceUrlParamConverter constructor.
     * @param TransformerInterface $resourceTransformer
     */
    public function __construct(TransformerInterface $resourceTransformer)
    {
        $this->resourceTransformer = $resourceTransformer;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        
        $resourceUrl = is_string($request->attributes->get($name)) ?
            $request->attributes->get($name) :
            $request->query->get($name)
        ;
        
        if (!is_string($resourceUrl) || $resourceUrl === '') {
            return false;
        }
        
        try 
        {
            $resourceObject = $this->resourceTransformer->getResource($resourceUrl);

            if(!is_a($resourceObject, $configuration->getClass())) {
                throw new BadRequestHttpException('Link to wrong resource type provided.');
            }
                
            if ($resourceObject) {
                $request->attributes->set($name, $resourceObject);
                return true;
            }
        } catch(\InvalidArgumentException $e) {
            // return false for invalid argument exception since this
            // only happens when the resource url is not valid.
        }
        
        return false;
    }

    public function supports(ParamConverter $configuration)
    {
        return $this->resourceTransformer->isResource($configuration->getClass());
    }
}

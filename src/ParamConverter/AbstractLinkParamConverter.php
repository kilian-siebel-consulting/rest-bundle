<?php
namespace Ibrows\RestBundle\ParamConverter;

use Ibrows\RestBundle\Request\LinkHeader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractLinkParamConverter extends ManipulationParamConverter
{
    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $this->checkConfiguration($configuration);

        $object = $this->getObject($request, $configuration);

        $allowedRelations = $configuration->getOptions()['relations'];

        $links = $request->attributes->get('links');

        array_walk($links, function(LinkHeader $link) use($allowedRelations, $object) {
            $this->applyLink($link, $object, $allowedRelations);
        });

        $this->validate($object, $configuration, $request);

        return true;
    }

    /**
     * @param LinkHeader $link
     * @param object     $object
     * @param array      $allowedRelations
     */
    abstract protected function applyLink(LinkHeader $link, $object, array $allowedRelations);

    /**
     * @param LinkHeader $link
     * @param array      $allowedRelations
     */
    protected function checkRelation(LinkHeader $link, array $allowedRelations)
    {
        if(!in_array($link->getRelation(), $allowedRelations)) {
            throw new BadRequestHttpException('Relation type "' . $link->getRelation() . '" is not allowed."');
        }
    }

    /**
     * @param ParamConverter $configuration
     */
    protected function checkConfiguration(ParamConverter $configuration)
    {
        if(!isset($configuration->getOptions()['relations'])) {
            throw new InvalidConfigurationException('Option relations has to be specified for ParamConverter "link".');
        }
    }
}
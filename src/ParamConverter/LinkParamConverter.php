<?php
namespace Ibrows\RestBundle\ParamConverter;

use Doctrine\Common\Collections\Collection;
use Ibrows\RestBundle\Request\LinkHeader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LinkParamConverter extends ManipulationParamConverter
{
    /**
     * @return string
     */
    protected function getName()
    {
        return 'link';
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request       The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if(!isset($configuration->getOptions()['relations'])) {
            throw new InvalidConfigurationException('Option relations has to be specified for ParamConverter "link".');
        }
        $object = $this->getObject($request, $configuration);

        $allowedRelations = $configuration->getOptions()['relations'];

        /** @var LinkHeader $link */
        foreach($request->attributes->get('links') as $link) {
            if(!in_array($link->getRelation(), $allowedRelations)) {
                throw new BadRequestHttpException('Relation type "' . $link->getRelation() . '" is not allowed."');
            }

            $entityToLink = $link->getResource();
            /** @var Collection $collection */
            $collection = $object->{'get' . ucfirst($link->getRelation())}();

            if(!$entityToLink) {
                throw new NotFoundHttpException;
            }

            if($collection->contains($entityToLink)) {
                throw new ConflictHttpException('Entity is already linked to ' . $entityToLink->getId());
            }

            $collection->add($entityToLink);
        }

        $this->validate($object, $configuration, $request);

        return true;
    }
}
<?php
namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Request\LinkHeader;
use Ibrows\RestBundle\Transformer\ResourceTransformer;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

class LinkHeaderListener
{
    /**
     * @var UrlMatcherInterface
     */
    private $urlMatcher;

    /**
     * @var ResourceTransformer
     */
    private $resourceTransformer;

    /**
     * LinkHeaderListener constructor.
     *
     * @param UrlMatcherInterface $urlMatcher
     * @param ResourceTransformer $resourceTransformer
     */
    public function __construct(
        UrlMatcherInterface $urlMatcher,
        ResourceTransformer $resourceTransformer
    ) {
        $this->urlMatcher = $urlMatcher;
        $this->resourceTransformer = $resourceTransformer;
    }

    public function onKernelRequest(KernelEvent $event)
    {
        if(
            strtoupper($event->getRequest()->getMethod()) !== 'LINK' &&
            strtoupper($event->getRequest()->getMethod()) !== 'UNLINK'
        ) {
            return;
        }

        if(!$event->getRequest()->headers->has('link')) {
            throw new BadRequestHttpException('Please specify at least one Link.');
        }

        $requestMethod = $this->urlMatcher->getContext()->getMethod();
        $this->urlMatcher->getContext()->setMethod('GET');

        $links  = [];
        /*
         * Due to limitations, multiple same-name headers are sent as comma
         * separated values.
         *
         * This breaks those headers into Link headers following the format
         * http://tools.ietf.org/html/rfc2068#section-19.6.2.4
         */
        foreach (explode(',', $event->getRequest()->headers->get('link')) as $header) {
            $header = trim($header);
            $link = new LinkHeader($header);
            try {
                if($urlParameters = $this->urlMatcher->match($link->getValue())) {
                    $link->setUrlParameters($urlParameters);
                }
            } catch(ResourceNotFoundException $exception) {}
            try {
                $link->setResource($this->resourceTransformer->getResourceProxy($link->getValue()));
            } catch(InvalidArgumentException $e) {}

            $links[] = $link;
        }

        $this->urlMatcher->getContext()->setMethod($requestMethod);

        $event->getRequest()->attributes->set('links', $links);
    }
}
<?php
namespace Ibrows\RestBundle\Listener;

use Ibrows\RestBundle\Debug\Converter\ConverterInterface;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\EventListener\ProfilerListener;
use Symfony\Component\HttpKernel\Profiler\Profile;

class DebugResponseListener
{
    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var string
     */
    private $keyName;

    /**
     * @var ConverterInterface[]
     */
    private $converters;

    /**
     * @var ProfilerListener|null
     */
    private $profilerListener;

    /**
     * DebugViewResponseListener constructor.
     * @param array $configuration
     */
    public function __construct(
        array $configuration
    ) {
        $this->enabled = $configuration['enabled'];
        $this->keyName = $configuration['key_name'];
        $this->profilerListener = null;
        $this->converters = [];
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if(
            $this->enabled &&
            $this->profilerListener !== null &&
            $event->isMasterRequest() &&
            $event->getResponse()->getStatusCode() !== Response::HTTP_NO_CONTENT &&
            $event->getResponse()->headers->get('Content-Type') === 'application/json' &&
            $event->getResponse()->getContent() != null
        ) {
            $this->appendDebugInformation($event);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    protected function appendDebugInformation(FilterResponseEvent $event)
    {
        $profile = $this->getProfile($event->getRequest());
        if(!$profile) {
            return;
        }

        $content = json_decode($event->getResponse()->getContent(), true);
        $content[$this->keyName] = $this->extractInformation($profile, $event);
        $event->getResponse()->setContent(json_encode($content));
    }

    /**
     * I'm really sorry for the reflection here.
     * If you've a better idea improve it please.
     *
     * @param Request $request
     * @return Profile|null
     */
    protected function getProfile(Request $request)
    {
        $profilesProperty = new ReflectionProperty($this->profilerListener, 'profiles');
        $profilesProperty->setAccessible(true);

        $profiles = $profilesProperty->getValue($this->profilerListener);

        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        if(!isset($profiles[$request])) {
            return null;
        }

        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        return $profiles[$request];
    }

    /**
     * @param FilterResponseEvent $event
     * @return string
     */
    protected function getTokenLink(FilterResponseEvent $event)
    {
        return (
            $event->getRequest()->isSecure()
                ? 'https://'
                : 'http://'
            ) .
            $event->getRequest()->getHttpHost() .
            $event->getResponse()->headers->get('X-Debug-Token-Link');
    }

    /**
     * @param Profile $profile
     * @param FilterResponseEvent $event
     * @return array
     */
    protected function extractInformation(Profile $profile, FilterResponseEvent $event)
    {
        $debugInformation = [
            'tokenUrl' => $this->getTokenLink($event),
            'ip' => $profile->getIp(),
            'method' => $profile->getMethod(),
            'url' => $profile->getUrl(),
            'time' => date('c', $profile->getTime()),
            'statusCode' => $profile->getStatusCode(),
        ];

        foreach($profile->getCollectors() as $collector) {
            $debugInformation = array_merge(
                $debugInformation,
                $this->convertCollector($collector)
            );
        }

        return $debugInformation;
    }

    /**
     * @param DataCollectorInterface $collector
     * @return array
     */
    protected function convertCollector(DataCollectorInterface $collector)
    {
        $debugInformation = [];

        foreach($this->converters as $converter) {
            if(
                $converter->supports($collector) &&
                $data = $converter->convert($collector)
            ) {
                $debugInformation[$converter->getName()] = $data;
            }
        }

        return $debugInformation;
    }

    /**
     * @param ProfilerListener $profilerListener
     */
    public function setProfilerListener(ProfilerListener $profilerListener)
    {
        $this->profilerListener = $profilerListener;
    }

    /**
     * @param ConverterInterface $converter
     */
    public function addConverter(ConverterInterface $converter)
    {
        $this->converters[] = $converter;
    }
}
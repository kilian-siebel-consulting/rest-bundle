<?php
namespace Ibrows\RestBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\DataCollector\DoctrineDataCollector;
use ReflectionProperty;
use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\EventListener\ProfilerListener;
use Symfony\Component\HttpKernel\Profiler\Profile;

class DebugViewResponseListener
{
    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var ProfilerListener|null
     */
    private $profilerListener = null;

    /**
     * DebugViewResponseListener constructor.
     * @param array $configuration
     */
    public function __construct(
        array $configuration
    ) {
        $this->enabled = $configuration['enabled'];
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
        $content['_debug'] = $this->extractInformation($profile, $event);
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
     * @param Profile $profile
     * @param FilterResponseEvent $event
     * @return array
     */
    protected function extractInformation(Profile $profile, FilterResponseEvent $event)
    {
        /** @var MemoryDataCollector $memory */
        $memory = $profile->getCollector('memory');
        /** @var TimeDataCollector $time */
        $time = $profile->getCollector('time');
        /** @var SecurityDataCollector $security */
        $security = $profile->getCollector('security');
        /** @var DoctrineDataCollector $db */
        $db = $profile->getCollector('db');

        $tokenLink = (
            $event->getRequest()->isSecure()
                ? 'https://'
                : 'http://'
            ) .
            $event->getRequest()->getHttpHost() .
            $event->getResponse()->headers->get('X-Debug-Token-Link');

        return [
            'tokenUrl' => $tokenLink,
            'ip' => $profile->getIp(),
            'method' => $profile->getMethod(),
            'url' => $profile->getUrl(),
            'time' => date('c', $profile->getTime()),
            'statusCode' => $profile->getStatusCode(),
            'memory' => $this->formatBytes($memory->getMemory()),
            'time_elapsed' => round((microtime(true) * 1000) - $time->getStartTime()) . ' ms',
            'security' => [
                'enabled' => $security->isEnabled(),
                'user' => (string) $security->getUser(),
                'roles' => $security->getRoles(),
                'authenticated' => $security->isAuthenticated(),
                'token' => $security->getTokenClass(),
            ],
            'db' => [
                'mapping_errors' => $db->getMappingErrors(),
                'invalid_entities' => $db->getInvalidEntityCount(),
                'query_count' => $db->getQueryCount(),
                'query_time' => round($db->getTime()) . ' ms',
            ],
        ];
    }

    /**
     * @param int $bytes
     * @return string
     */
    protected function formatBytes($bytes) {
        $precision = 2;

        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * @param ProfilerListener $profilerListener
     */
    public function setProfilerListener(ProfilerListener $profilerListener)
    {
        $this->profilerListener = $profilerListener;
    }
}
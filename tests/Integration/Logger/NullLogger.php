<?php
namespace Ibrows\RestBundle\Tests\Integration\Logger;

use Psr\Log\NullLogger as BaseNullLogger;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class NullLogger extends BaseNullLogger implements DebugLoggerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLogs()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function countErrors()
    {
        return 0;
    }
}

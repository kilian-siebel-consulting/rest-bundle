<?php
namespace Ibrows\RestBundle\Patch;

interface TokenEscapeInterface
{
    const REPLACEMENTS = [
        '~0' => '~',
        '~1' => '/',
    ];

    /**
     * @param string $token
     * @return string
     */
    public function unescape($token);

    /**
     * @param string $token
     * @return string
     */
    public function escape($token);
}

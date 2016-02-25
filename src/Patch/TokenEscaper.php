<?php
namespace Ibrows\RestBundle\Patch;

class TokenEscaper implements TokenEscapeInterface
{
    /**
     * {@inheritdoc}
     */
    public function unescape($token)
    {
        return str_replace(
            array_keys(self::REPLACEMENTS),
            array_values(self::REPLACEMENTS),
            $token
        );
    }

    /**
     * {@inheritdoc}
     */
    public function escape($token)
    {
        return str_replace(
            array_values(self::REPLACEMENTS),
            array_keys(self::REPLACEMENTS),
            $token
        );
    }
}

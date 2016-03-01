<?php
namespace Ibrows\RestBundle\Patch;

class TokenEscaper implements TokenEscapeInterface
{
    private $replacements = [
        '~0' => '~',
        '~1' => '/',
    ];

    /**
     * @return array
     */
    public function getReplacements()
    {
        return $this->replacements;
    }

    /**
     * @param array $replacements
     */
    public function setReplacements(array $replacements)
    {
        $this->replacements = $replacements;
    }


    /**
     * {@inheritdoc}
     */
    public function unescape($token)
    {
        return str_replace(
            array_keys($this->replacements),
            array_values($this->replacements),
            $token
        );
    }

    /**
     * {@inheritdoc}
     */
    public function escape($token)
    {
        return str_replace(
            array_values($this->replacements),
            array_keys($this->replacements),
            $token
        );
    }
}

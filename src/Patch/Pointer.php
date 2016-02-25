<?php
namespace Ibrows\RestBundle\Patch;

use Ibrows\RestBundle\Patch\Exception\InvalidPathException;

class Pointer implements PointerInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string[]
     */
    private $tokens;

    /**
     * @var TokenEscapeInterface
     */
    private $escaper;

    /**
     * Pointer constructor.
     * @param TokenEscapeInterface $escaper
     */
    private function __construct(
        TokenEscapeInterface $escaper
    ) {
        $this->escaper = $escaper;
    }

    /**
     * @param string               $path
     * @param TokenEscapeInterface $tokenEscaper
     * @return Pointer
     */
    public static function fromPath($path, TokenEscapeInterface $tokenEscaper)
    {
        $pointer = new Pointer($tokenEscaper);
        $pointer->path = (string)$path;
        return $pointer;
    }

    /**
     * @param string[]             $tokens
     * @param TokenEscapeInterface $tokenEscaper
     * @return Pointer
     */
    public static function fromTokens(array $tokens, TokenEscapeInterface $tokenEscaper)
    {
        $pointer = new Pointer($tokenEscaper);
        $pointer->tokens = $tokens;
        return $pointer;
    }

    /**
     * {@inheritdoc}
     */
    public function tokens()
    {
        if (!$this->tokens) {
            $this->sliceTokens();
        }
        return $this->tokens;
    }

    /**
     * {@inheritdoc}
     */
    public function lastToken()
    {
        if (!$this->tokens) {
            $this->sliceTokens();
        }
        return end($this->tokens);
    }

    /**
     * {@inheritdoc}
     */
    public function path()
    {
        if (!$this->path) {
            $this->generatePath();
        }
        return $this->path;
    }

    private function generatePath()
    {
        $escapedTokens = array_map(
            function ($token) {
                return $this->escaper->escape($token);
            },
            $this->tokens
        );

        $this->path = '/' . implode('/', $escapedTokens);
    }

    private function sliceTokens()
    {
        if (0 !== strpos($this->path, '/')) {
            throw new InvalidPathException(
                $this,
                sprintf(
                    'Paths must always start with "/". %s given',
                    $this->path
                )
            );
        }

        // Remove token before first /
        $this->tokens = array_slice(
            explode('/', $this->path),
            1
        );

        // Unescape special cases
        $this->tokens = array_map(
            function ($token) {
                return $this->escaper->unescape($token);
            },
            $this->tokens
        );
    }
}

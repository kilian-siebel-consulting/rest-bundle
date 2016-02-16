<?php
namespace Ibrows\RestBundle\Patch;

use Closure;
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
     * @var Closure
     */
    private $addressLookup;

    /**
     * @var TokenEscapeInterface
     */
    private $escaper;

    /**
     * Pointer constructor.
     * @param Closure              $addressLookup
     * @param TokenEscapeInterface $escaper
     */
    private function __construct(
        Closure $addressLookup,
        TokenEscapeInterface $escaper
    ) {
        $this->addressLookup = $addressLookup;
        $this->escaper = $escaper;
    }

    /**
     * @param string               $path
     * @param Closure              $addressLookup
     * @param TokenEscapeInterface $tokenEscaper
     * @return Pointer
     */
    public static function fromPath($path, Closure $addressLookup, TokenEscapeInterface $tokenEscaper)
    {
        $pointer = new Pointer($addressLookup, $tokenEscaper);
        $pointer->path = (string)$path;
        return $pointer;
    }

    /**
     * @param string[]             $tokens
     * @param Closure              $addressLookup
     * @param TokenEscapeInterface $tokenEscaper
     * @return Pointer
     */
    public static function fromTokens(array $tokens, Closure $addressLookup, TokenEscapeInterface $tokenEscaper)
    {
        $pointer = new Pointer($addressLookup, $tokenEscaper);
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
    public function resolve(& $object, array $options = [])
    {
        $lookup = $this->addressLookup;
        return $lookup($this, $object, $options);
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

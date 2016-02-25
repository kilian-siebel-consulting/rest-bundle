<?php
namespace Ibrows\RestBundle\Patch;

class PointerFactory implements PointerFactoryInterface
{
    /**
     * @var TokenEscapeInterface
     */
    private $tokenEscaper;

    /**
     * PointerFactory constructor.
     * @param TokenEscapeInterface $tokenEscaper
     */
    public function __construct(
        TokenEscapeInterface $tokenEscaper
    ) {
        $this->tokenEscaper = $tokenEscaper;
    }

    /**
     * {@inheritdoc}
     */
    public function createFromPath($path)
    {
        return Pointer::fromPath(
            $path,
            $this->tokenEscaper
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createFromTokens(array $tokens)
    {
        return Pointer::fromTokens(
            $tokens,
            $this->tokenEscaper
        );
    }
}

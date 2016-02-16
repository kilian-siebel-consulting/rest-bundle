<?php
namespace Ibrows\RestBundle\Patch;

class PatchConverter implements PatchConverterInterface
{
    /**
     * @var OperationCheckerInterface
     */
    private $operationChecker;

    /**
     * @var PointerFactoryInterface
     */
    private $pointerFactory;

    /**
     * @var OperationFactoryInterface
     */
    private $operationFactory;

    /**
     * PatchConverter constructor.
     * @param OperationCheckerInterface $operationChecker
     * @param PointerFactoryInterface   $pointerFactory
     * @param OperationFactoryInterface $operationFactory
     */
    public function __construct(
        OperationCheckerInterface $operationChecker,
        PointerFactoryInterface $pointerFactory,
        OperationFactoryInterface $operationFactory
    ) {
        $this->operationChecker = $operationChecker;
        $this->pointerFactory = $pointerFactory;
        $this->operationFactory = $operationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function convert(array $rawPatch)
    {
        $patch = [];

        while($rawOperation = array_shift($rawPatch)) {
            $this->operationChecker->validate($rawOperation);

            $pointer = $this->pointerFactory->createFromPath($rawOperation['path']);

            $value = array_key_exists('value', $rawOperation)
                ? $rawOperation['value']
                : null;

            $fromPointer = array_key_exists('from', $rawOperation)
                ? $this->pointerFactory->createFromPath($rawOperation['from'])
                : null;

            $parameters = array_diff_key($rawOperation, [
                'op' => true,
                'path' => true,
                'value' => true,
                'from' => true,
            ]);

            array_push(
                $patch,
                $this->operationFactory->create(
                    $rawOperation['op'],
                    $pointer,
                    $fromPointer,
                    $value,
                    $parameters
                )
            );
        }

        return $patch;
    }
}

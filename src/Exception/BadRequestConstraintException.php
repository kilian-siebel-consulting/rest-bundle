<?php
namespace Ibrows\RestBundle\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @codeCoverageIgnore
 */
class BadRequestConstraintException extends BadRequestHttpException implements DisplayableException
{

    /**
     * @var ConstraintViolationListInterface
     */
    private $violations;

    /**
     * BadRequestConstraintException constructor.
     *
     * @param ConstraintViolationListInterface $violations
     * @param Exception|null                   $previous
     * @param int                              $code
     */
    public function __construct(ConstraintViolationListInterface $violations, Exception $previous = null, $code = 0)
    {
        $this->violations = $violations;
        parent::__construct("Failed constraints", $previous, $code);
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $violations = [];
        
        /** @var ConstraintViolationInterface $violation */
        foreach ($this->violations as $violation) {
            $violations[] = [
                'code' => $violation->getCode(),
                'message' => $violation->getMessage(),
                'property_path' => $this->mapPropertyPath($violation->getPropertyPath()),
            ];
        }
        
        return ['violations' => $violations];
    }

    /**
     * @param string $path
     * @return string
     */
    private function mapPropertyPath($path)
    {
        $propertyPath = new PropertyPath($path);
        $pathElements = $propertyPath->getElements();

        return '/' . implode('/', $pathElements);
    }
}

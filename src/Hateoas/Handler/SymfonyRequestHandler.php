<?php

namespace Hateoas\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Adrien Brault <adrien.brault@gmail.com>
 */
class SymfonyRequestHandler implements HandlerInterface
{
    private static $requestProperties = array(
        'attributes',
        'request',
        'query',
        'server',
        'cookies',
        'headers',
    );

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @var Request
     */
    private $request;

    public function __construct(PropertyAccessor $propertyAccessor = null, Request $request = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        $this->request = $request;
    }

    /**
     * This setters should come in handy with symfony's DI request scope
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value, $data)
    {
        if (null === $this->request) {
            throw new \RuntimeException('The request was not set on the SymfonyRequestHandler.');
        }

        if (!preg_match('/(?P<property>[^.]+)([.](?P<key>.+))?/', $value, $matches)) {
            return null;
        }

        $property = $matches['property'];

        if (isset($matches['key'])) {
            if (!in_array($property, self::$requestProperties)) {
                throw new \InvalidArgumentException(
                    sprintf('The property "%s" is not supported on the Request.', $property)
                );
            }

            return $this->request->{$property}->get($matches['key']);
        }

        return $this->propertyAccessor->getValue($this->request, $property);
    }
}

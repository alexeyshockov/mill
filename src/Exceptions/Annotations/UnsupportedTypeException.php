<?php
namespace Mill\Exceptions\Annotations;

use Mill\Exceptions\BaseException;

class UnsupportedTypeException extends BaseException
{
    use AnnotationExceptionTrait;

    public static function create(string $annotation, string $class, ?string $method): UnsupportedTypeException
    {
        $message = sprintf(
            'The type on `%s` in %s::%s is unsupported. Please check the documentation for supported types.',
            $annotation,
            $class,
            $method
        );

        $exception = new self($message);
        $exception->annotation = $annotation;
        $exception->class = $class;
        $exception->method = $method;

        return $exception;
    }
}

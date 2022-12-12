<?php

namespace WendellAdriel\ValidatedDTO\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidCastableException extends Exception
{
    /**
     * @param  string  $property
     */
    public function __construct(string $property)
    {
        parent::__construct("Property {$property} has an invalid cast configuration", Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
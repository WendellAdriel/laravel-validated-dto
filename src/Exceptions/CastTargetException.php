<?php

namespace WendellAdriel\ValidatedDTO\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CastTargetException extends Exception
{
    public function __construct(string $property)
    {
        parent::__construct("The property: {$property} has an invalid cast configuration", Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

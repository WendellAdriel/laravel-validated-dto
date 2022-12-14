<?php

namespace WendellAdriel\ValidatedDTO\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class CastException extends Exception
{
    /**
     * @param  string  $property
     */
    public function __construct(string $property)
    {
        parent::__construct("Unable to cast property: {$property} - invalid value", Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

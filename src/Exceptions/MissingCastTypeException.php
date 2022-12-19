<?php

namespace WendellAdriel\ValidatedDTO\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class MissingCastTypeException extends Exception
{
    /**
     * @param  string  $property
     */
    public function __construct(string $property)
    {
        parent::__construct("Missing cast type configuration for property: {$property}", Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

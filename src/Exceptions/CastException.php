<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

final class CastException extends Exception
{
    public function __construct(string $property)
    {
        parent::__construct("Unable to cast property: {$property} - invalid value", Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

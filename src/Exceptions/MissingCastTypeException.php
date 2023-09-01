<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

final class MissingCastTypeException extends Exception
{
    public function __construct(string $property)
    {
        parent::__construct("Missing cast type configuration for property: {$property}", Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

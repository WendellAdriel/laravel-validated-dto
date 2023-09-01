<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

final class InvalidJsonException extends Exception
{
    public function __construct()
    {
        parent::__construct('The JSON string provided is not valid', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}

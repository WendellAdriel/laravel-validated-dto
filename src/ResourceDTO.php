<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WendellAdriel\ValidatedDTO\Support\ResourceCollection;

abstract class ResourceDTO extends SimpleDTO implements Responsable
{
    private int $status;

    private array $headers;

    public function __construct(?array $data = null, int $status = 200, array $headers = [])
    {
        parent::__construct($data);
        $this->status = $status;
        $this->headers = $headers;
    }

    public static function collection(array $data, int $status = 200, array $headers = []): ResourceCollection
    {
        return new ResourceCollection($data, static::class, $status, $headers);
    }

    /**
     * @param  Request  $request
     */
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse($this->toArray(), $this->status, $this->headers);
    }
}

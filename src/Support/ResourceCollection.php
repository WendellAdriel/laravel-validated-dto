<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Support;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Request;
use WendellAdriel\ValidatedDTO\Casting\DTOCast;
use WendellAdriel\ValidatedDTO\Exceptions\CastException;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;

/**
 * @internal
 */
final class ResourceCollection implements Responsable
{
    public function __construct(
        private array $data,
        private string $dtoClass,
        private int $status = 200,
        private array $headers = []
    ) {}

    /**
     * @param  Request  $request
     *
     * @throws CastException|CastTargetException|ValidationException
     */
    public function toResponse($request): JsonResponse
    {
        $result = [];

        $dtoCast = new DTOCast($this->dtoClass);
        foreach ($this->data as $item) {
            $result[] = $dtoCast->cast('', $item)->toArray();
        }

        return new JsonResponse($result, $this->status, $this->headers);
    }
}

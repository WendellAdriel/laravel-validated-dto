<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Concerns;

use Illuminate\Database\Eloquent\Model;

trait DataTransformer
{
    /**
     * Returns the DTO validated data in array format.
     */
    public function toArray(): array
    {
        return $this->buildDataForExport();
    }

    /**
     * Returns the DTO validated data in a JSON string format.
     */
    public function toJson(): string
    {
        return json_encode($this->buildDataForExport());
    }

    /**
     * Returns the DTO validated data in a pretty JSON string format.
     */
    public function toPrettyJson(): string
    {
        return json_encode($this->buildDataForExport(), JSON_PRETTY_PRINT);
    }

    /**
     * Creates a new model with the DTO validated data.
     */
    public function toModel(string $model): Model
    {
        return new $model($this->buildDataForExport());
    }
}

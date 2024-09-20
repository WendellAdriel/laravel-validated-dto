<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Concerns;

use Illuminate\Database\Eloquent\Model;

trait DataTransformer
{
    public function toArray(): array
    {
        return $this->buildDataForExport();
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->buildDataForExport(), $options);
    }

    public function toPrettyJson(): string
    {
        return json_encode($this->buildDataForExport(), JSON_PRETTY_PRINT);
    }

    public function toModel(string $model): Model
    {
        return new $model($this->buildDataForExport());
    }
}

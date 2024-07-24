<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Contracts\Support\Arrayable;

class ArrayableObject implements Arrayable
{
    public function key(): string
    {
        return 'arrayable-object-key';
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key(),
        ];
    }
}

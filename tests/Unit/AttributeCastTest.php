<?php

declare(strict_types=1);

use function Pest\Faker\faker;
use WendellAdriel\ValidatedDTO\Tests\Datasets\AttributesDTO;
use WendellAdriel\ValidatedDTO\Tests\Datasets\ModelCastInstance;

it('properly casts a Model property to a DTO class', function () {
    $model = new ModelCastInstance([
        'name' => faker()->name,
        'metadata' => '{"age": 10, "doc": "foo"}',
    ]);

    expect($model->metadata)
        ->toBeInstanceOf(AttributesDTO::class);
});

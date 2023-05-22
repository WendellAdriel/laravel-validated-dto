<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Database\Eloquent\Model;

class ModelCastInstance extends Model
{
    protected $fillable = ['name', 'metadata'];

    protected $casts = [
        'metadata' => AttributesDTO::class,
    ];
}

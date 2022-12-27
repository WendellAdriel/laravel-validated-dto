<?php

namespace WendellAdriel\ValidatedDTO\Tests\Datasets;

use Illuminate\Database\Eloquent\Model;

class ModelInstance extends Model
{
    protected $fillable = ['name', 'age'];
}

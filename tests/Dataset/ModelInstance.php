<?php

namespace WendellAdriel\ValidatedDTO\Tests\Dataset;

use Illuminate\Database\Eloquent\Model;

class ModelInstance extends Model
{
    protected $fillable = ['name', 'age'];
}

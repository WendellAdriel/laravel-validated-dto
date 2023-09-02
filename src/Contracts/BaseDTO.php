<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Contracts;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface BaseDTO
{
    public static function fromJson(string $json): self;

    public static function fromArray(array $data): self;

    public static function fromRequest(Request $request): self;

    public static function fromModel(Model $model): self;

    public static function fromCommandArguments(Command $command): self;

    public static function fromCommandOptions(Command $command): self;

    public static function fromCommand(Command $command): self;

    public function toArray(): array;

    public function toJson(): string;

    public function toPrettyJson(): string;

    public function toModel(string $model): Model;
}

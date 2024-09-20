<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Contracts;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface BaseDTO
{
    public static function fromJson(string $json): static;

    public static function fromArray(array $data): static;

    public static function fromRequest(Request $request): static;

    public static function fromModel(Model $model): static;

    public static function fromCommandArguments(Command $command): static;

    public static function fromCommandOptions(Command $command): static;

    public static function fromCommand(Command $command): static;

    public function toArray(): array;

    public function toJson($options = 0): string;

    public function toPrettyJson(): string;

    public function toModel(string $model): Model;
}

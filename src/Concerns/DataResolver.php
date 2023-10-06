<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Concerns;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use WendellAdriel\ValidatedDTO\Exceptions\CastTargetException;
use WendellAdriel\ValidatedDTO\Exceptions\InvalidJsonException;
use WendellAdriel\ValidatedDTO\Exceptions\MissingCastTypeException;

trait DataResolver
{
    /**
     * @throws InvalidJsonException|ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromJson(string $json): static
    {
        $jsonDecoded = json_decode($json, true);
        if (! is_array($jsonDecoded)) {
            throw new InvalidJsonException();
        }

        return new static($jsonDecoded);
    }

    /**
     * @throws CastTargetException|MissingCastTypeException
     */
    public static function fromArray(array $array): static
    {
        return new static($array);
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromRequest(Request $request): static
    {
        return new static($request->all());
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromModel(Model $model): static
    {
        return new static($model->toArray());
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommandArguments(Command $command): static
    {
        return new static(self::filterArguments($command->arguments()));
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommandOptions(Command $command): static
    {
        return new static($command->options());
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommand(Command $command): static
    {
        return new static(array_merge(self::filterArguments($command->arguments()), $command->options()));
    }

    private static function filterArguments(array $arguments): array
    {
        $result = [];
        foreach ($arguments as $key => $value) {
            if (! is_numeric($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}

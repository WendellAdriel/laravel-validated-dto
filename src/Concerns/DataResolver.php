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
    public static function fromJson(string $json): self
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
    public static function fromArray(array $array): self
    {
        return new static($array);
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromRequest(Request $request): self
    {
        return new static($request->all());
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromModel(Model $model): self
    {
        return new static($model->toArray());
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommandArguments(Command $command): self
    {
        return new static($command->arguments());
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommandOptions(Command $command): self
    {
        return new static($command->options());
    }

    /**
     * @throws ValidationException|MissingCastTypeException|CastTargetException
     */
    public static function fromCommand(Command $command): self
    {
        return new static(array_merge($command->arguments(), $command->options()));
    }
}

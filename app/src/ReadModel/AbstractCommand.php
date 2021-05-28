<?php


namespace App\ReadModel;


use App\Exceptions\InvalidTypeException;
use App\Exceptions\LogicException;

abstract class AbstractCommand
{
    public function __construct(array $properties = [])
    {
        $this->setup($properties);
    }

    public function toArray(): array
    {
        return array_replace(get_class_vars(static::class), get_object_vars($this));
    }

    final public function getArrayFromJson(string $value): array
    {
        try {
            $decoded = json_decode($value, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidTypeException('json', $value);
        }

        if (false === \is_array($decoded)) {
            throw new InvalidTypeException('json object', $value);
        }

        return $decoded;
    }

    final public static function hasProperty(self $command, string $property): bool
    {
        return property_exists(\get_class($command), $property);
    }

    final public static function getValueByProperty(self $command, string $property): mixed
    {
        if (self::hasProperty($command, $property)) {
            return $command->{$property}; /* @phpstan-ignore-line */
        }

        throw new LogicException("{$property} not found in class.");
    }

    final public static function findValueByProperty(self $command, string $property): mixed
    {
        if (self::hasProperty($command, $property)) {
            return $command->{$property}; /* @phpstan-ignore-line */
        }

        return null;
    }

    private function setup(array $properties): void
    {
        /** @psalm-var mixed $value */
        foreach ($properties as $property => $value) {
            if (true === \is_string($value)) {
                $value = trim($value);

                if ('' === $value) {
                    continue;
                }
            }

            if (null === $value) {
                continue;
            }

            if (property_exists(static::class, $property)) {
                $method = 'set' . str_replace(
                        ' ',
                        '',
                        mb_convert_case(
                            str_replace('_', ' ', $property),
                            \MB_CASE_TITLE,
                            'UTF-8'
                        )
                    );
                if (\is_callable([$this, $method])) {
                    $this->{$method}($value); /* @phpstan-ignore-line */

                    continue;
                }

                $this->{$property} = $value; /* @phpstan-ignore-line */
            }
        }
    }
}


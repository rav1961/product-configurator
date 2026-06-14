<?php

declare(strict_types=1);

namespace App\Shared\Cache;

use BackedEnum;
use JsonException;

final class CacheKeyBuilder
{
    /**
     * @param  array<string, mixed>  $values
     *
     * @throws JsonException
     */
    public function make(CachePolicy $policy, array $values = []): string
    {
        ksort($values);

        return implode(':', [
            $this->normalizeSegment($policy->name->value),
            $this->normalizeSegment($policy->version),
            $this->hashValues($values),
        ]);
    }

    /**
     * @param  array<string, mixed>  $values
     *
     * @throws JsonException
     */
    private function hashValues(array $values): string
    {
        return hash('xxh128', json_encode(
            $this->normalizeValues($values),
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE,
        ));
    }

    private function normalizeSegment(string $value): string
    {
        return trim(str_replace([' ', '/'], '.', mb_strtolower($value)), '.');
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function normalizeValues(array $values): array
    {
        $normalized = [];

        foreach ($values as $key => $value) {
            $normalized[(string) $key] = $this->normalizeValue($value);
        }

        ksort($normalized);

        return $normalized;
    }

    private function normalizeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            $value = trim($value);

            return $value === '' ? null : mb_strtolower($value);
        }

        if (is_array($value)) {
            return $this->normalizeValues($value);
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return $value;
    }
}

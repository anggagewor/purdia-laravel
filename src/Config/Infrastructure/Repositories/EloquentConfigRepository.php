<?php

namespace Purdia\Config\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Purdia\Config\Domain\Contracts\ConfigRepository;
use Purdia\Config\Domain\Models\Config;

class EloquentConfigRepository implements ConfigRepository
{
    public function get(string $group, string $key, mixed $default = null): mixed
    {
        $config = Config::where('group', $group)
            ->where('key', $key)
            ->first();

        if (! $config) {
            return $default;
        }

        return $config->typed_value;
    }

    public function set(string $group, string $key, mixed $value, string $type = 'string'): Config
    {
        $storedValue = $this->prepareValue($value, $type);

        return Config::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $storedValue, 'type' => $type],
        );
    }

    public function getGroup(string $group): Collection
    {
        return Config::where('group', $group)->get();
    }

    public function getGroups(): Collection
    {
        return Config::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');
    }

    public function delete(string $group, string $key): bool
    {
        return Config::where('group', $group)
            ->where('key', $key)
            ->delete() > 0;
    }

    public function has(string $group, string $key): bool
    {
        return Config::where('group', $group)
            ->where('key', $key)
            ->exists();
    }

    private function prepareValue(mixed $value, string $type): string
    {
        return match ($type) {
            'json', 'array' => json_encode($value),
            'boolean' => $value ? 'true' : 'false',
            default => (string) $value,
        };
    }
}

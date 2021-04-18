<?php

namespace Kelunik\FiberLocal;

final class FiberLocal
{
    public static function withInitial(\Closure $closure): self
    {
        $instance = new self;
        $instance->initial = $closure;

        return $instance;
    }

    private \WeakMap $storage;
    private \Closure $initial;

    public function __construct()
    {
        $this->storage = new \WeakMap;
    }

    public function set(mixed $value): void
    {
        $this->storage[\Fiber::this() ?? $this] = $value;
    }

    public function get(): mixed
    {
        if (isset($this->initial)) {
            return $this->storage[\Fiber::this() ?? $this] ?? ($this->initial)();
        }

        return $this->storage[\Fiber::this() ?? $this] ?? null;
    }
}
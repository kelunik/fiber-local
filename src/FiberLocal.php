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

    /**
     * @var FiberLocal|null Allows FiberLocal to be used without fiber support.
     */
    private ?self $legacy;

    private \WeakMap $storage;
    private \Closure $initial;

    public function __construct()
    {
        $this->storage = new \WeakMap;
        $this->legacy = \class_exists(\Fiber::class, false) ? null : $this;
    }

    public function set(mixed $value): void
    {
        $this->storage[$this->legacy ?? \Fiber::this() ?? $this] = $value;
    }

    public function get(): mixed
    {
        if (isset($this->initial)) {
            return $this->storage[$this->legacy ?? \Fiber::this() ?? $this] ?? ($this->initial)();
        }

        return $this->storage[$this->legacy ?? \Fiber::this() ?? $this] ?? null;
    }
}
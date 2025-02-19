<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


use Neunerlei\Dbg\HookType;

class Hooks
{
    protected array $listeners = [];

    /**
     * Adds a new listener to a specific hook type
     * @param HookType $type The type of the hook to listen to
     * @param callable $listener The listener to add, will be executed when the hook is triggered
     * @return Hooks
     */
    public function addListener(HookType $type, callable $listener): static
    {
        $this->listeners[$type->name][] = $listener;
        return $this;
    }

    /**
     * Triggers a specific hook, calling all registered listeners providing the given arguments
     * @param HookType $type The type of the hook to trigger
     * @param mixed ...$args The arguments to pass to the listeners
     * @return void
     */
    public function trigger(HookType $type, mixed ...$args): void
    {
        if (!isset($this->listeners[$type->name])) {
            return;
        }

        foreach ($this->listeners[$type->name] as $listener) {
            $listener(...$args);
        }
    }

    /**
     * Removes all listeners from all hooks
     * @return void
     */
    public function clear(): void
    {
        $this->listeners = [];
    }
}

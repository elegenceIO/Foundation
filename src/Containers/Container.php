<?php
namespace ElegenceIO\Foundation\Containers;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class Container implements ContainerInterface
{

    protected array $instances = [];
    protected array $bindings = [];
    private array $flags = [];


    public function set(string $name,string|int|array|callable|object $value):void
    {
        $this->bindings[$name] = $value;
    }

    public function bind(string $id,callable $factory):void
    {
        $this->bindings[$id] = $factory;
    }

    public function singleton(string $id, callable $factory):void
    {
        $this->bindings[$id] = function() use ($factory,$id)
        {
            return $this->instances[$id] ??= $factory($this);
        };
    }




    public function has(string $id):bool
    {
        return isset($this->bindings[$id]);
    }


    public function get(string $id)
    {
        return $this->make($id);
    }

    
    public function make(string $id)
    {
          if (!$this->has($id)) {
        throw new class(
            "Service [$id] is not bound in the container."
        ) extends InvalidArgumentException
          implements NotFoundExceptionInterface {};
    }

        $binding = $this->bindings[$id];

        return is_callable($binding) ? $binding($this) : $binding;
    }

    
}
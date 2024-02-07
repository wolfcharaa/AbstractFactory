<?php

declare(strict_types=1);

namespace wolfchara\AbstractFactory\Laravel;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class AbstractFactory
{
    private string $serviceName;
    private string $namespace;

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function create(string $className): ?object
    {
        $className = basename(str_replace('\\', '/', $className));
        $class = get_class($this);
        $this->serviceName = basename(str_replace('\\', '/', $class));
        $this->namespace = preg_replace('/' . $this->serviceName . '$/u', '', $class);
        $this->serviceName = basename(str_replace('\\', '/', $this->namespace));
        $className = ucfirst($className);
        $findClass = $this->findExistsClass($className);

        return $this->container->get($findClass);
    }

    private function findExistsClass($className): string
    {
        $names = [
            $className => 0,
            "{$className}{$this->serviceName}" => 0
        ];
        $exitClass = null;

        foreach ($names as $name => $count) {
            $className = "{$this->namespace}{$name}";

            if (!class_exists($className)) {
                continue;
            }

            $exitClass = $className;
            $names[$name]++;
        }

        if (!$exitClass) {
            throw new NotFoundHttpException("class $className not found");
        }

        if (array_sum($names) !== 1) {
            throw new UnprocessableEntityHttpException("find more 1 classes {$className}");
        }

        return $exitClass;
    }
}
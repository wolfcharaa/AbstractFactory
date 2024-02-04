<?php

declare(strict_types=1);

namespace wolfchara\AbstractFactory\Symfony;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Yaml\Yaml;

class AbstractFactory
{
    private string $serviceName;
    private string $namespace;

    public function __construct(private readonly ContainerInterface $container)
    {
    }

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

        foreach ($names as $name) {
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

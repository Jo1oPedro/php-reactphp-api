<?php

namespace App\container;

use League\Container\Definition\DefinitionAggregateInterface;
use League\Container\Inflector\InflectorAggregateInterface;
use League\Container\ServiceProvider\ServiceProviderAggregateInterface;
use Psr\Container\ContainerInterface;

class Container extends \League\Container\Container
{
    private static ?ContainerInterface $container = null;
    private function __construct(
        DefinitionAggregateInterface $definitions = null,
        ServiceProviderAggregateInterface $providers = null,
        InflectorAggregateInterface $inflectors = null)
    {
        parent::__construct($definitions, $providers, $inflectors);
    }

    public static function getInstance()
    {
        if(is_null(self::$container)) {
            self::$container = new static();
        }
        return self::$container;
    }
}
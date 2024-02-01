<?php

use App\container\Container;
use League\Container\Argument\Literal\StringArgument;
use League\Container\ReflectionContainer;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(BASE_PATH . '/enviroment/.env');

$container = Container::getInstance();
$container->delegate(new ReflectionContainer(true));

// SETA O BASE PATH PARA ACESSAR QUALQUER ARQUIVO DA APLICAÇÃO
$container->add('BASE_PATH', new StringArgument(BASE_PATH));

// SETA AS VARIAVEIS DE AMBIENTE GLOBALMENTE
$symfonyDotEnvVars = explode(",", $_SERVER['SYMFONY_DOTENV_VARS']);
foreach($symfonyDotEnvVars as $symfonyDotEnvVar) {
    $container->add($symfonyDotEnvVar, new StringArgument($_SERVER[$symfonyDotEnvVar]));
}
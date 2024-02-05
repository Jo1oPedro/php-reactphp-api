<?php

use App\container\Container;
use League\Container\Argument\Literal\StringArgument;
use League\Container\ReflectionContainer;
use React\MySQL\ConnectionInterface;
use React\MySQL\Factory;
use React\Socket\Connector;
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

## SETANDO INTERFACE PADRÃO DE CONEXÃO NO CONTAINER
$serviceIp = gethostbyname('banco_de_dados_relacional');

// SÓ É POSSÍVEL CONECTAR AO MYSQL LATEST OU 8+ UTILIZANDO ESSE CONNECTOR DEVIDO A ALGUNS PROBLEMAS DA BIBLIOTECA
$connector = new Connector([
    'dns' => $serviceIp,
    'tcp' => [
        // We have to set this correct, otherwise you get that error:
        // https://github.com/friends-of-reactphp/mysql/issues/112
        'bindto' => "{$serviceIp}:3306",
    ],
    'tls' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$mysql = new Factory(connector: $connector);
$connection = $mysql->createLazyConnection(
    "{$container->get('MYSQL_USER')}:{$container->get('MYSQL_PASSWORD')}@{$serviceIp}:3306/{$container->get('MYSQL_DATABASE')}"
);

$container->add(ConnectionInterface::class, $connection);
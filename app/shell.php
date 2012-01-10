<?php

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('dev', false);
$request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
$handle = $kernel->handle($request);
$container = $kernel->getContainer();
$em = $container->get('doctrine')->getEntityManager();

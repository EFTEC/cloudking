<?php

use www\examplenamespace\cl\service\ExampleHelloService;

include 'ExampleDefinition.php';
include 'service\IExampleHelloService.php';
include 'service\ExampleHelloService.php';

ExampleDefinition::init();
ExampleDefinition::$service->folderServer=__DIR__; // or you could select any folder.run_initial.php
ExampleDefinition::$service->serviceInstance=new ExampleHelloService();
ExampleDefinition::run();
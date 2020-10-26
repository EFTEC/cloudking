<?php

use www\examplenamespace\cl\service\Example2WSService;

include __DIR__ . '/Definition.php';
include __DIR__ . '/service/IExample2WSService.php';
include __DIR__ . '/service/Example2WSService.php';

Definition::init();
Definition::$service->folderServer=__DIR__;
Definition::$service->serviceInstance=new Example2WSService();
Definition::run();

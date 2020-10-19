<?php

include __DIR__.'/../1.create/Definition.php';

include __DIR__.'/../2.service/Example2WSService.php';

Definition::init(true); // false means there is not a web gui
Definition::$service->serviceInstance=new Example2WSService(); // we tied the web service with our service class

Definition::run();
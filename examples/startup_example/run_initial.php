<?php

include 'ExampleDefinition.php';

ExampleDefinition::init();
ExampleDefinition::$service->folderServer=__DIR__; // or you could select any folder.run_initial.php
ExampleDefinition::run();
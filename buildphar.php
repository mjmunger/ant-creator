<?php

$phar = new Phar("antcreator.phar", 0,'antcreator.phar');
$phar->startBuffering();
$phar->buildFromDirectory('.');
$defaultStub = $phar->createDefaultStub('create-app.php');
$stub = "#!/usr/bin/env php \n". $defaultStub;
$phar->setStub($stub);
$phar->stopBuffering();
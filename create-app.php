<?php

namespace AntAppCreator;

include('classes/AppCreator.class.php');

$targetDir = '/tmp/_phpant_template/';

$Creator = new AppCreator();
$Creator->verifyPrerequisites();

$data = $Creator->interview();

$Creator->getTemplate($targetDir);
$Creator->findAndReplace($data);

$targetDir = $Creator->checkoutTargetRepo($data['TARGETREPO']);

$Creator->writeAppFile($targetDir);
$Creator->publishApp($targetDir);
$Creator->addAppFiles($targetDir);
$Creator->commitApp($targetDir);
$Creator->pushApp($targetDir);

printf("\n APP CREATION COMPLETE.\n");
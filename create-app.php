<?php

namespace AntAppCreator;

include('classes/AppCreator.class.php');


function show_help() {
?>

SUMMARY
    Creates a PHP-Ant app.

SYNTAX
    create-app [target path]

    You may specify "." as the target path.

EXAMPLES:

    To create an app in the current directory:

      create-app .

    To create an app in a different directory:

      create-app /foo/path/

NOTES:
    - You MUST have already downloaded and installed the ant-signer package,
      and generated your private and public keys with `publish -k`

    - This app will prompt you with a wizard to fill out the required data.

    - SSH access control for your remote git repository is recommended.

<?php
    die();
}

if($argc != 2) show_help();

$appTargetPath = $argv[1];

if($appTargetPath == ".") $appTargetPath = getcwd();
if(file_exists($appTargetPath) == false) die (sprintf("Path not found! (%s)", $appTargetPath));

$tmpDir = '/tmp/_phpant_template/';

$Creator = new AppCreator($appTargetPath);
$Creator->verifyPrerequisites();

$data = $Creator->interview();

$Creator->getTemplate($tmpDir);
$Creator->findAndReplace($data);

$targetDir = $Creator->checkoutTargetRepo($data['TARGETREPO']);

$Creator->writeAppFile($targetDir);
$Creator->publishApp($targetDir);
$Creator->addAppFiles($targetDir);
$Creator->commitApp($targetDir);
$Creator->pushApp($targetDir);

printf("\n APP CREATION COMPLETE.\n");
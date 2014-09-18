<?php

require 'postDeployComposer.php';

$symfonyConsole = $basePath . "app/console";
$webPath = $basePath . "web";

if (file_exists($symfonyConsole)) {
//    echo executeCmd("php $symfonyConsole doctrine:migrations:migrate --no-interaction --env=prod");
    echo executeCmd("php $symfonyConsole doctrine:schema:update --force");

    echo executeCmd("php $symfonyConsole cache:clear --env=prod --no-debug");
    executeCmd("php $symfonyConsole assets:install $webPath");
    executeCmd("php $symfonyConsole assetic:dump");
}

<?php

require_once './ComposerPostDeploy.php';
require_once './SymfonyPostDeploy.php';

/*
 * Permit to do a "composer install" and initialize a SF2 project using a simple GET request : http://www.domainname.com/postdeploy/run.php
 */

try {
    /* ##################
     *  COMPOSER INSTALL
     * ################## */

    $composerPostDeploy = new PostDeploy\ComposerPostDeploy('../../', true);
    $composerPostDeploy->composerInstall();

    /* #################
     *  SYMFONY2 INSTALL
     * ################# */

    $symfonyPostDeploy = new PostDeploy\SymfonyPostDeploy($composerPostDeploy);

    //If there is migrations... migrate, else update DB schema.
    if (
        file_exists($composerPostDeploy->getBasePath() . 'app/DoctrineMigrations') &&
        iterator_count(new FilesystemIterator($composerPostDeploy->getBasePath() . 'app/DoctrineMigrations', FilesystemIterator::SKIP_DOTS)) > 0
    ) {
        $symfonyPostDeploy->doctrineDoMigrations();
    } else {
        $symfonyPostDeploy->doctrineSchemaUpdate();
    }

    $symfonyPostDeploy->clearCache();
    $symfonyPostDeploy->assetsInstall();

} catch (\ErrorException $e) {
    echo '[ERROREXCEPTION] ' . $e->getMessage();
}

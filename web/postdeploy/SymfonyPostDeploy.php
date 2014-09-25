<?php

namespace PostDeploy;

/**
 * Class SymfonyPostDeploy
 * @package PostDeploy
 *
 * Permit to run SF commands with PHP after a composer install
 */
class SymfonyPostDeploy
{
    protected $composerPostDeploy;
    protected $symfonyConsole = 'app/console';
    protected $webPath = 'web';

    public function __construct(ComposerPostDeploy $composerPostDeploy)
    {
        $this->composerPostDeploy = $composerPostDeploy;
        $this->symfonyConsole     = $composerPostDeploy->getBasePath() . $this->symfonyConsole;
        $this->webPath            = $composerPostDeploy->getBasePath() . $this->webPath;

        if (!file_exists($this->symfonyConsole)) {
            throw new \ErrorException('Symfony console file not found');
        }
    }

    public function executeSymfonyCmd($command)
    {
        $this->composerPostDeploy->log("[COMMAND] php $this->symfonyConsole $command");
        $res = ComposerPostDeploy::executeCmd("php $this->symfonyConsole $command");
        return $this->composerPostDeploy->log($res);
    }

    public function doctrineDoMigrations($env = 'prod')
    {
        $this->executeSymfonyCmd("doctrine:migrations:migrate --no-interaction --env=$env");
    }

    public function doctrineSchemaUpdate($env = 'prod')
    {
        $this->executeSymfonyCmd("doctrine:schema:update --env=$env --force");
    }

    public function clearCache($env = 'prod')
    {
        $this->executeSymfonyCmd("cache:clear --no-debug --env=$env");
    }

    public function assetsInstall($env = 'prod')
    {
        $this->executeSymfonyCmd("assets:install --no-debug --env=$env $this->webPath");
        $this->executeSymfonyCmd("assetic:dump --no-debug --env=$env");
    }
}

<?php

namespace PostDeploy;

/**
 * Class SymfonyPostDeploy
 * @package PostDeploy
 *
 * Permit to run SF commands with PHP after a composer install. Another possible way : put commands in composer.json "post-install-cmd" part.
 */
class SymfonyPostDeploy
{
    protected $composerPostDeploy;
    protected $symfonyConsole = 'app/console';
    protected $webPath = 'web';
    protected $phpCommand = 'php';

    public function __construct(ComposerPostDeploy $composerPostDeploy)
    {
        $this->composerPostDeploy = $composerPostDeploy;
        $this->symfonyConsole     = $composerPostDeploy->getBasePath() . $this->symfonyConsole;
        $this->webPath            = $composerPostDeploy->getBasePath() . $this->webPath;
        $this->phpCommand         = $composerPostDeploy->getPhpCommand();

        if (!file_exists($this->symfonyConsole)) {
            throw new \ErrorException('Symfony console file not found');
        }
    }

    public function executeSymfonyCmd($command)
    {
        $this->composerPostDeploy->log("[COMMAND] $this->phpCommand $this->symfonyConsole $command");
        $res = ComposerPostDeploy::executeCmd("$this->phpCommand $this->symfonyConsole $command");
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
        $this->executeSymfonyCmd("assets:install --quiet --no-debug --env=$env $this->webPath");
        $this->executeSymfonyCmd("assetic:dump --quiet --no-debug --env=$env");
    }
}

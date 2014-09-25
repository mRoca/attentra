<?php

namespace PostDeploy;

use ErrorException;

/**
 * Class ComposerPostDeploy
 * @package PostDeploy
 *
 * Permit to download and use composer with PHP
 */
class ComposerPostDeploy
{
    protected $basePath = "../"; //Relative project root path
    protected $tmpDir = "tmp/"; //Composer HOME for current project

    protected $composerPHARFile = "composer.phar";
    protected $composerInstallFile = "composerinstall.php";
    protected $composerJsonFile = "composer.json";
    protected $composerLockFile = "composer.lock";

    protected $useSystemComposer = true; //Automaticaly set to false if composer cmd isn't found

    protected $logs = array();
    protected $echoWhenLogging = false;

    public function __construct($basePath = '', $echoWhenLogging = false)
    {
        $this->echoWhenLogging = $echoWhenLogging;

        $basePath                          = $basePath !== '' ? $basePath : $this->basePath;
        $this->basePath                    = rtrim(realpath($basePath), '/') . '/';
        $this->tmpDirFileFullPath          = rtrim($this->basePath . $this->tmpDir, '/') . '/';
        $this->composerInstallFileFullPath = $this->basePath . $this->tmpDir . $this->composerInstallFile;
        $this->composerPHARFileFullPath    = $this->basePath . $this->tmpDir . $this->composerPHARFile;
        $this->composerJsonFileFullPath    = $this->basePath . $this->composerJsonFile;
        $this->composerLockFileFullPath    = $this->basePath . $this->composerLockFile;

        $this->useSystemComposer &= self::shellCommandExists('composer');
    }

    /**
     * @param string $cmd
     * @return array
     * @throws ErrorException
     */
    public function composerExecute($cmd)
    {
        $composerCmd = $this->useSystemComposer ? "composer" : "php $this->composerPHARFileFullPath";

        $this->log("[COMMAND] $composerCmd $cmd");

        if (self::isWindows()) {
            $res = $this->executeCmd("cd $this->basePath && SET COMPOSER_HOME=$this->tmpDirFileFullPath && $composerCmd --no-interaction --no-progress $cmd");
        } else {
            $res = $this->executeCmd("cd $this->basePath && COMPOSER_HOME=$this->tmpDirFileFullPath $composerCmd --no-interaction --no-progress $cmd");
        }

        return $this->log($res);
    }

    /**
     * @throws ErrorException
     */
    public function composerDownload()
    {
        //TMP path creation
        if (!file_exists($this->tmpDirFileFullPath)) {
            if (!mkdir($this->tmpDirFileFullPath, 0777, true)) {
                throw new ErrorException("Error when creating path [$this->tmpDirFileFullPath]");
            }
            file_put_contents($this->tmpDirFileFullPath . '.htaccess', "Deny from All");
        }

        //Composer file downloading or updating
        if (!file_exists($this->composerPHARFileFullPath)) {
            $this->log("Downloading & installing Composer");

            $contentFile = self::urlGetContent('https://getcomposer.org/installer');

            if (!strlen($contentFile)) {
                throw new ErrorException("Downloaded composer file empty");
            }

            if (!file_put_contents($this->composerInstallFileFullPath, $contentFile)) {
                throw new ErrorException("Error when creating file [$this->composerInstallFileFullPath]");
            }

            $res = self::executeCmd("php $this->composerInstallFileFullPath --install-dir=$this->tmpDirFileFullPath");
            $this->log($res);

            unlink($this->composerInstallFileFullPath);

            //Composer file exists verification
            if (!file_exists($this->composerPHARFileFullPath)) {
                throw new ErrorException("File [$this->composerPHARFileFullPath] not found.");
            }
        }

        //Composer update
        if (time() - filemtime($this->composerPHARFileFullPath) > 10 * 24 * 3600) {
            self::composerExecute('self-update');
        }
    }

    /**
     * @throws ErrorException
     */
    public function composerInstall()
    {
        if (!file_exists($this->composerJsonFileFullPath)) {
            throw new ErrorException("File [$this->composerJsonFileFullPath] not found");
        }

        if (!$this->useSystemComposer) {
            $this->composerDownload();
        }

        $this->composerExecute('install');
    }

    /**
     * @param array|string $textOrArray
     * @return array
     */
    public function log($textOrArray)
    {
        if (is_string($textOrArray))
            $textOrArray = array($textOrArray);

        if ($this->echoWhenLogging) {
            echo implode("\n", $textOrArray) . "\n";
        }

        $this->logs = array_merge($this->logs, $textOrArray);

        return $textOrArray;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param bool $returnString
     * @return array
     */
    public function getLogs($returnString = false)
    {
        return $returnString ? implode("\n", $this->logs) : $this->logs;
    }

    // =============================================================

    public static function isWindows()
    {
        return strncasecmp(PHP_OS, 'WIN', 3) === 0;
    }

    /**
     * @param string $url
     * @return mixed
     * @throws ErrorException
     */
    public static function urlGetContent($url)
    {
        if (!function_exists('curl_version')) {
            throw new ErrorException("This app needs the Curl PHP extension.");
        }

        if (substr($url, 0, 5) === 'https' && !extension_loaded('openssl')) {
            throw new ErrorException("This app needs the Open SSL PHP extension.");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Caution : not secure
        $data = curl_exec($ch);

        $error = curl_error($ch);
        curl_close($ch);

        if ($data === false) {
            throw new ErrorException("Error when getting the file [$url] : $error");
        }

        return $data;
    }

    /**
     * Do an exec
     * @param string $cmd
     * @return array
     * @throws ErrorException
     */
    public static function executeCmd($cmd)
    {
        //CMD without 2>&1 , errors are shown in STDERR, and exec doesn't catch SDTERR
        exec($cmd . " 2>&1", $output, $return_var);

        if ($return_var > 0) {
            throw new ErrorException("Error when executing command : [$cmd] :\n" . implode($output, "\n") . "\n", $return_var);
        }

        return $output;
    }

    /**
     * @param string $cmd
     * @return bool
     */
    public static function shellCommandExists($cmd)
    {
        $returnVal = shell_exec("which $cmd");

        return !empty($returnVal);
    }
}

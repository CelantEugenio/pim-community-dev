<?php

/**
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Akeneo\Tool\Component\Console;

use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Command launcher
 *
 * @author JM Leroux <jean-marie.leroux@akeneo.com>
 */
class CommandLauncher
{
    /** @var string Application root directory */
    protected $rootDir;

    /** @var string Application execution environment */
    protected $environment;

    /** @var string */
    protected $logsDir;

    /**
     * @param string $rootDir
     * @param string $environment
     * @param string $logsDir
     */
    public function __construct(string $rootDir, string $environment, string $logsDir)
    {
        $this->rootDir = $rootDir;
        $this->environment = $environment;
        $this->logsDir = $logsDir;
    }

    /**
     * @return false|string
     */
    protected function getPhp()
    {
        $pathFinder = new PhpExecutableFinder();

        return $pathFinder->find();
    }

    /**
     * @param string $command
     */
    protected function buildCommandString(string $command): string
    {
        $phpCommand = $this->getPhp();

        if ('cli' === php_sapi_name()) {
            $memoryLimit = ini_get('memory_limit');
            $phpCommand = "{$this->getPhp()} -d memory_limit={$memoryLimit}";
        }

        return "{$phpCommand} {$this->rootDir}/../bin/console --env={$this->environment} {$command}";
    }

    /**
     * Launch command in background and return
     *
     * @param string $command
     * @param string $logfile
     *
     * @return null
     */
    public function executeBackground(string $command, string $logfile = null)
    {
        $cmd = $this->buildCommandString($command);
        if (null === $logfile) {
            $logfile = sprintf('%s%scommand_execute.log', $this->logsDir, DIRECTORY_SEPARATOR);
        }
        $cmd = escapeshellcmd($cmd);
        $cmd .= sprintf(' >> %s 2>&1 &', $logfile);

        exec($cmd);

        return null;
    }

    /**
     * Launch command and return result
     *
     * @param string $command
     */
    public function executeForeground(string $command): CommandResult
    {
        $cmd = $this->buildCommandString($command);
        $cmd = escapeshellcmd($cmd);

        $output = [];
        $status = null;

        exec($cmd, $output, $status);

        return new CommandResult($output, $status);
    }
}

<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


use Kint\Kint;

class Config implements \JsonSerializable
{
    protected bool $enabled = true;
    protected bool $environmentDetection = true;
    protected string $envVarKey = 'APP_ENV';
    protected string $envVarValue = 'dev';
    protected bool $cliIsDev = true;
    protected string|null $debugReferrer = null;
    protected string|null $consolePassword = null;
    protected string|null $logDir = null;
    protected string|null $logStream = null;

    /**
     * Returns true if the debugging functionality is enabled, false if not.
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Main switch to enable/disable the debugging functionality. If you set this to
     * false, none of the functions will do or output anything.
     *
     * @param bool $enabled
     * @return void
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = Kint::$enabled_mode = $enabled;
    }

    /**
     * Returns true if the environment detection is enabled, false if not.
     * @return bool
     */
    public function useEnvironmentDetection(): bool
    {
        return $this->environmentDetection;
    }

    /**
     * Disables the environment detection mechanism if set to false.
     * Environment detection is used to determine if the current environment is considered a "development" environment.
     * Default: true
     *
     * @param bool $environmentDetection
     * @return void
     */
    public function setEnvironmentDetection(bool $environmentDetection): void
    {
        $this->environmentDetection = $environmentDetection;
    }

    /**
     * Returns the name of the environment variable to look for when enabling the debug feature.
     * @return string
     */
    public function getEnvVarKey(): string
    {
        return $this->envVarKey;
    }

    /**
     * Determines the name of the environment variable to look for when
     * enabling the debug feature.
     * Default: PROJECT_ENV
     *
     * @param string $envVarKey
     * @return void
     */
    public function setEnvVarKey(string $envVarKey): void
    {
        $this->envVarKey = $envVarKey;
    }

    /**
     * Returns the value that is expected from the configured environment variable to enable the debugger.
     * @return string
     */
    public function getEnvVarValue(): string
    {
        return $this->envVarValue;
    }

    /**
     * Used in combination with "envVarKey" and determines which value to expect
     * from the configured environment variable to enable the debugger.
     * Default: dev
     * @param string $envVarValue
     * @return void
     */
    public function setEnvVarValue(string $envVarValue): void
    {
        $this->envVarValue = $envVarValue;
    }

    /**
     * Returns true if the debugger should always output stuff in a CLI environment or not.
     * @return bool
     */
    public function isCliIsDev(): bool
    {
        return $this->cliIsDev;
    }

    /**
     * Determines if the debugger should always output stuff in a CLI environment or not.
     * Default: true
     * @param bool $cliIsDev
     * @return void
     */
    public function setCliIsDev(bool $cliIsDev): void
    {
        $this->cliIsDev = $cliIsDev;
    }

    /**
     * Returns the referrer that is expected to enable the debugger capabilities.
     * @return string|null
     */
    public function getDebugReferrer(): ?string
    {
        return $this->debugReferrer;
    }

    /**
     * If set this will be expected as the referrer to enable the debugger capabilities.
     * @param string|null $debugReferrer
     * @return void
     */
    public function setDebugReferrer(?string $debugReferrer): void
    {
        $this->debugReferrer = $debugReferrer;
    }

    /**
     * Returns the password that is required to access the console output.
     * @return string|null
     */
    public function getConsolePassword(): ?string
    {
        return $this->consolePassword;
    }

    /**
     * If set the phpConsole will require this value as password before printing the console output to the browser.
     * @param string|null $consolePassword
     * @return void
     */
    public function setConsolePassword(?string $consolePassword): void
    {
        $this->consolePassword = $consolePassword;
    }

    /**
     * Returns the directory where the log file should be written to.
     * Returns null if no explicit log directory is set. In this case the logger can decide where to write the log file.
     * @return string|null
     */
    public function getLogDir(): ?string
    {
        return $this->logDir;
    }

    /**
     * If set, the logFile() function will dump the logfile to the given director.
     * Make sure it exists and is writable by the webserver!
     * @param string|null $logDir
     * @return void
     */
    public function setLogDir(?string $logDir): void
    {
        $this->logDir = $logDir;
    }

    /**
     * Returns the editor link format that is used to generate clickable links in the debug output.
     * Returns null if no explicit editor link format is set. In this case the logger can decide how to generate the links.
     * @return string|null
     */
    public function getLogStream(): ?string
    {
        return $this->logStream;
    }

    /**
     * If set, the logStream() function will dump the log to the given stream.
     * Defaults to php://stdout
     * @param string|null $logStream
     * @return void
     */
    public function setLogStream(?string $logStream): void
    {
        $this->logStream = $logStream;
    }

    public function jsonSerialize(): array
    {
        return [
            'enabled' => $this->enabled,
            'environmentDetection' => $this->environmentDetection,
            'envVarKey' => $this->envVarKey,
            'envVarValue' => $this->envVarValue,
            'cliIsDev' => $this->cliIsDev,
            'debugReferrer' => $this->debugReferrer,
            'consolePassword' => $this->consolePassword,
            'logDir' => $this->logDir,
            'logStream' => $this->logStream,
        ];
    }

    public function importFromArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $setter = match ($key) {
                'enabled' => 'setEnabled',
                'environmentDetection' => 'setEnvironmentDetection',
                'envVarKey' => 'setEnvVarKey',
                'envVarValue' => 'setEnvVarValue',
                'cliIsDev' => 'setCliIsDev',
                'debugReferrer' => 'setDebugReferrer',
                'consolePassword' => 'setConsolePassword',
                'logDir' => 'setLogDir',
                'logStream' => 'setLogStream',
                default => null,
            };

            if ($setter === null) {
                continue;
            }

            $this->$setter($value);
        }
    }
}

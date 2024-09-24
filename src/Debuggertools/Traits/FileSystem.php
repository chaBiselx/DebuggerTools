<?php

declare(strict_types=1);

namespace Debuggertools\Traits;

use Debuggertools\Escaper\ShellArgEscaper;

trait FileSystem
{
    /**
     * Create a dir if not exist
     *
     * @param string $path
     * @return void
     */
    protected function createDirIfNotExist(string $path, int $Permission = 0777): void
    {
        if (!file_exists($path)) {
            mkdir($path, $Permission, true);
        }
    }


    /**
     * Append log to file
     *
     * @param string $pathLog
     * @param string $logMessage
     * @return void
     */
    protected function appendToFile(string $pathLog, string $logMessage): void
    {
        $traitShellArgEscaper = new ShellArgEscaper();
        $logMessage = $traitShellArgEscaper->escape($logMessage);
        system("echo $logMessage >> " . $pathLog);
    }

    /**
     * Create missing directory
     *
     * @return boolean
     */
    protected function createMissingDirectories(): bool
    {
        $permissions = 0755;
        $path = dirname($this->pathFile);
        if (is_dir($path)) {
            return true; // Le dossier existe déjà
        }

        // Tente de créer le dossier et retourne true en cas de succès
        if (mkdir($path, $permissions, true)) {
            return true;
        }

        // Retourne false en cas d'échec
        return false;
    }
}

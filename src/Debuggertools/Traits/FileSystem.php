<?php

namespace Debuggertools\Traits;

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
}

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
	protected function createDirIfNotExist(string $path): void
	{
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
	}
}

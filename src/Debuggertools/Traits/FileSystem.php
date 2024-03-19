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
		$path = preg_replace('/\//', '\\', $path);
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
	}
}

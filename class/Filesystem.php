<?php

namespace ThemeViz;

class Filesystem
{
	private $recursiveMatchingScans = [];

    public function getFile($path)
    {
        return file_get_contents($path);
    }

    public function deleteFolder($projectRelativePath)
	{
		if (!$projectRelativePath) {
			throw new \Exception("Directory path required");
		}

		if (!THEMEVIZ_BASE_PATH) {
			throw new \Exception("Basedir not defined");
		}

		$realPath = realpath( THEMEVIZ_BASE_PATH . "/$projectRelativePath");

		if (!$realPath) {
			return false;
		}

		if (substr($realPath, 0, strlen(THEMEVIZ_BASE_PATH)) !== THEMEVIZ_BASE_PATH) {
			throw new \Exception("Specified directory not inside basedir");
		}

		$result = system("rm -r $realPath");

		if ($result === false) {
			throw new \Exception("Failed to delete directory");
		}

		return $result;
	}

    public function deleteTree($projectRelativePath)
    {
		if (!$projectRelativePath) {
			throw new \Exception("Directory path required");
		}

		if (!THEMEVIZ_BASE_PATH) {
			throw new \Exception("Basedir not defined");
		}

		$realPath = realpath( THEMEVIZ_BASE_PATH . "/$projectRelativePath");

		if (!$realPath) {
			return false;
		}

		if (substr($realPath, 0, strlen(THEMEVIZ_BASE_PATH)) !== THEMEVIZ_BASE_PATH) {
			throw new \Exception("Specified directory not inside basedir");
		}

        $result = system("rm -r $realPath/*");

		if ($result === false) {
			throw new \Exception("Failed to delete directory");
		}

		return $result;
    }

    public function fileForceContents($path, $contents)
    {
        $parts = explode('/', $path);
        array_pop($parts);
        $dir = implode("/", $parts);
        $this->makeTree($dir);
        file_put_contents($path, $contents);
    }

    /**
     * @param $path
     */
    public function makeTree($path)
    {
        $parts = explode("/", $path);
        $dir = "";
        foreach ($parts as $part) {
            if (!is_dir($dir .= "/$part")) $this->makeDir($dir);
        }
    }

    public function makeDir($path)
    {
        mkdir($path);
    }

    public function scanDir($path)
    {
    	return array_diff(scandir($path), ["..","."]);
    }

    public function isDir($path)
    {
        return is_dir($path);
    }

	public function findPathsMatchingRecursive($path, $regex)
	{
		$key = "$path:$regex";

		if (!isset($this->recursiveMatchingScans[$key])) {
			$this->recursiveMatchingScans[$key] = $this->doRecursivePathMatching($path, $regex);
		}

		return $this->recursiveMatchingScans[$key];
	}

	/**
	 * @param $path
	 * @param $regex
	 * @return array
	 */
	private function doRecursivePathMatching($path, $regex): array
	{
		$paths = $this->getPathsInPath($path);
		$dirPaths = $this->filterPathsForDirs($paths);
		$filePaths = array_diff($paths, $dirPaths);
		$matchingPaths = $this->filterPaths($regex, $filePaths);

		return array_reduce($dirPaths, function ($carry, $dirPath) use ($regex) {
			$matches = $this->doRecursivePathMatching($dirPath, $regex);

			return array_merge($carry, $matches);
		}, $matchingPaths);
	}

	/**
	 * @param $path
	 * @return array
	 */
	private function getPathsInPath($path): array
	{
		$scan = array_diff($this->scanDir($path), [".", "..", ".git"]);
		return array_map(function ($scanName) use ($path) {
			return "$path/$scanName";
		}, $scan);
	}

	/**
	 * @param $paths
	 * @return array
	 */
	private function filterPathsForDirs($paths): array
	{
		return array_filter($paths, function ($path) {
			return $this->isDir($path);
		});
	}

	/**
	 * @param $regex
	 * @param $filePaths
	 * @return array
	 */
	private function filterPaths($regex, $filePaths): array
	{
		$matchingPaths = array_filter($filePaths, function ($filePath) use ($regex) {
			return preg_match($regex, $filePath);
		});
		return $matchingPaths;
	}
}
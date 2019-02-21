<?php

namespace ThemeViz;

class Git
{
    private $originalBranch;
    private $didStash;

    public function getRemoteUrl($path)
	{
		$out = $this->exec("cd $path; git remote get-url origin");

		return $out[0];
	}

	public function clone($remoteUrl, $destinationDir, $branch = "production") {
		if (!(realpath($destinationDir))) {
			throw new \Exception("Could not resolve destination directory");
		}

    	$this->exec("cd $destinationDir; git clone --recurse-submodules -j8 -b $branch --single-branch --depth 1 $remoteUrl .");
	}

    public function saveState($path)
    {
        $this->originalBranch = $this->getBranch($path);
        $this->didStash = $this->stash($path);
    }

    /**
     * @param $path
     * @return bool
     * @throws \Exception
     */
    public function resetState($path)
    {
        if (!$this->originalBranch) throw new \Exception("No saved state!");

        $checkoutBranchSuccessful = $this->checkoutBranch($path, $this->originalBranch);
        $applyStashSuccessful = ($this->didStash) ? $this->stashApply($path) : true;

        return $checkoutBranchSuccessful && $applyStashSuccessful;
    }

    public function checkoutBranch($path, $branch)
    {
    	// Warning: This will create a new branch if one doesn't exist.

        var_dump("Checkout branch $branch");
        $out = $this->exec("cd $path; git checkout $branch");

        return strpos($out[0], "Your branch is") !== false;
    }

    private function getBranch($path)
    {
        $out = $this->exec("cd $path; git status");

        return array_pop(explode(" ", $out[0]));
    }

    private function stash($path)
    {
        var_dump("Stashing");
        $out = $this->exec("cd $path; git stash");
        var_dump($out);

        return strpos($out[0], "Saved working directory") !== false;
    }

    private function stashApply($path)
    {
        var_dump("Applying stash");
        $out = $this->exec("cd $path; git stash apply");
        var_dump($out);

        return strpos($out[0], "On branch") !== false;
    }

    public function checkoutRemoteBranch($path, $branch)
    {
        var_dump("Checkout remote branch $branch");
        $out = $this->exec("cd $path; git checkout -b $branch --track origin/$branch");

        return strpos($out[1], "Switched to a new branch") !== false;
    }

    public function pull($path, $branch)
    {
        $out = $this->exec("cd $path; git pull origin $branch");

        return strpos($out[0], "From github.com") !== false;
    }

    private function exec($command)
    {
        exec($command, $output);

        return $output;
    }
}
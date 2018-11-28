<?php

namespace ThemeViz;

class Git
{
    private $originalBranch;
    private $didStash;

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

    private function checkoutBranch($path, $branch)
    {
        $out = $this->exec("cd $path; git checkout $branch");

        return strpos($out[0], "On branch") !== false;
    }

    private function getBranch($path)
    {
        $out = $this->exec("cd $path; git status");

        return array_pop(explode(" ", $out[0]));
    }

    private function stash($path)
    {
        $out = $this->exec("cd $path; git stash");

        return strpos($out[0], "Saved working directory") !== false;
    }

    private function stashApply($path)
    {
        $out = $this->exec("cd $path; git stash apply");

        return strpos($out[0], "On branch") !== false;
    }

    public function checkoutRemoteBranch($path, $branch)
    {
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
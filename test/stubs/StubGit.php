<?php

namespace ThemeViz;

class StubGit extends Git
{
    use Stub;

    public function saveState($path)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function resetState($path)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function checkoutRemoteBranch($path, $branch)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function pull($path, $branch)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }

    public function checkoutBranch($path, $branch)
    {
        return $this->handleCall(__FUNCTION__, func_get_args());
    }
}
<?php

namespace ThemeViz;

class Factory
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Firefox $firefox */
    private $firefox;

    /** @var Git $git */
    private $git;

    /** @var Less $less */
    private $less;

    /** @var Pixelmatch $pixelmatch */
    private $pixelmatch;

    /** @var Twig $twig */
    private $twig;

    public function __construct(
        Filesystem $filesystem = null,
        Firefox $firefox = null,
        Git $git = null,
        Less $less = null,
        Pixelmatch $pixelmatch = null,
        Twig $twig = null
    )
    {
        $this->filesystem = $filesystem;
        $this->firefox = $firefox;
        $this->git = $git;
        $this->less = $less;
        $this->pixelmatch = $pixelmatch;
        $this->twig = $twig;
    }

    /**
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->getObject(
            "Renderer",
            $this->getDiffer(),
            $this->getFilesystem(),
            $this->getGit(),
            $this->getPhotographer(),
            $this->getScenarioStorage(),
            $this->getSummaryCompiler(),
            $this->getTwigCompiler()
        );
    }

    /**
     * @return TwigCompiler
     */
    public function getTwigCompiler()
    {
        return $this->getObject(
            "TwigCompiler",
            $this->getLessCompiler(),
            $this->getTwig()
        );
    }

    /**
     * @return LessCompiler
     */
    public function getLessCompiler()
    {
        return $this->getObject(
            "LessCompiler",
            $this->getFilesystem(),
            $this->getLess()
        );
    }

    /**
     * @return Differ
     */
    public function getDiffer()
    {
        return $this->getObject(
            "Differ",
            $this->getFilesystem(),
            $this->getPixelmatch()
        );
    }

    /**
     * @return Photographer
     */
    public function getPhotographer()
    {
        return $this->getObject(
            "Photographer",
            $this->getFilesystem(),
            $this->getFirefox()
        );
    }

    /**
     * @return SummaryCompiler
     */
    public function getSummaryCompiler()
    {
        return $this->getObject(
            "SummaryCompiler",
            $this->getFilesystem(),
            $this->getTwig()
        );
    }

    /**
     * @return ScenarioStorage
     */
    public function getScenarioStorage()
    {
        return $this->getObject(
            "ScenarioStorage",
            $this->getFilesystem()
        );
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->getObject("Filesystem");
    }

    /**
     * @return Firefox
     */
    public function getFirefox()
    {
        return $this->getObject("Firefox");
    }

    /**
     * @return Git
     */
    public function getGit()
    {
        return $this->getObject("Git");
    }

    /**
     * @return Less
     */
    public function getLess()
    {
        return $this->getObject("Less");
    }

    /**
     * @return Pixelmatch
     */
    public function getPixelmatch()
    {
        return $this->getObject("Pixelmatch");
    }

    /**
     * @return Twig
     */
    public function getTwig()
    {
        return $this->getObject("Twig");
    }

    private function getObject($class, ...$dependencies)
    {
        $fullClassName = "\\ThemeViz\\$class";
        $propertyName = lcfirst($class);

        if (! isset($this->$propertyName)) {
            $this->$propertyName = new $fullClassName(...$dependencies);
        }

        return $this->$propertyName;
    }
}
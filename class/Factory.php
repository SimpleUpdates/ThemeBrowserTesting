<?php

namespace ThemeViz;

class Factory
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Less $less */
    private $less;

    /** @var Twig $twig */
    private $twig;

    public function __construct(Filesystem $filesystem = null, Less $less = null, Twig $twig = null)
    {
        $this->filesystem = $filesystem;
        $this->less = $less;
        $this->twig = $twig;
    }

    /**
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->getObject(
            "Renderer",
            $this->getFilesystem(),
            $this->getLess(),
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
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->getObject("Filesystem");
    }

    /**
     * @return Less
     */
    public function getLess()
    {
        return $this->getObject("Less");
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
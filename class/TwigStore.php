<?php

namespace ThemeViz;

class TwigStore
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    private $folder;

    public function __construct(
        Filesystem $filesystem
    )
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param $twigComponents
     * @param $folder
     */
    public function persistTwigComponents($twigComponents, $folder): void
    {
        $this->folder = $folder;

        array_map(function ($key) use ($twigComponents) {
            $scenarios = $twigComponents[$key];
            $templatePath = $key;

            $this->persistTwigScenarios($templatePath, $scenarios);
        }, array_keys($twigComponents));
    }

    /**
     * @param $templatePath
     * @param $scenarios
     */
    private function persistTwigScenarios($templatePath, $scenarios): void
    {
        array_map(function ($key) use ($templatePath, $scenarios) {
            $scenarioName = $key;
            $html = $scenarios[$key];
            $newPath = $this->generateBuildPath($scenarioName, $templatePath);

            $this->filesystem->fileForceContents($newPath, $html);
        }, array_keys($scenarios));
    }

    /**
     * @param $scenarioName
     * @param $templatePath
     * @return string
     */
    private function generateBuildPath($scenarioName, $templatePath): string
    {
        $pathParts = pathinfo($templatePath);
        $directory = $pathParts["dirname"];
        $filename = $pathParts["filename"];
        $extension = $pathParts["extension"];

        return "$this->folder/$directory/$filename--$scenarioName.$extension";
    }
}
<?php

namespace ThemeViz;

class ScenarioStorage
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    public function __construct(
        Filesystem $filesystem
    )
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param $templates
     */
    public function persistScenarios($templates): void
    {
        array_map(function ($key) use ($templates) {
            $scenarios = $templates[$key];
            $templatePath = $key;

            $this->persistTemplateScenarios($templatePath, $scenarios);
        }, array_keys($templates));
    }

    /**
     * @param $templatePath
     * @param $scenarios
     */
    private function persistTemplateScenarios($templatePath, $scenarios): void
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

        return THEMEVIZ_BASE_PATH . "/build/ref/html/$directory/$filename--$scenarioName.$extension";
    }
}
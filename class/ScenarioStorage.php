<?php

namespace ThemeViz;

class ScenarioStorage
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
     * @param $templates
     * @param $folder
     */
    public function persistScenarios($templates, $folder): void
    {
        $this->folder = $folder;

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

        return "$this->folder/$directory/$filename--$scenarioName.$extension";
    }
}
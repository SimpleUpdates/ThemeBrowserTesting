<?php

namespace ThemeViz;

class Renderer
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var TwigCompiler $twigCompiler */
    private $twigCompiler;

    private $themeConfig;
    private $componentsFile;

    public function __construct(
        Filesystem $filesystem,
        TwigCompiler $twigCompiler
    )
    {
        $this->filesystem = $filesystem;
        $this->twigCompiler = $twigCompiler;
    }

    public function compile()
    {
        $this->themeConfig = $this->getThemeConfig();
        $this->componentsFile = $this->getComponentsFile();

        if (!$components = $this->twigCompiler->compileTwig($this->themeConfig, $this->componentsFile)) return;

        $this->filesystem->deleteTree(THEMEVIZ_BASE_PATH . "/build");

        $this->filesystem->makeTree(THEMEVIZ_BASE_PATH . "/build/ref/shots");

        $this->persistScenarios($components);
    }

    /**
     * @param $templates
     */
    private function persistScenarios($templates): void
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

        return THEMEVIZ_BASE_PATH . "/build/ref/$directory/$filename--$scenarioName.$extension";
    }

    /**
     * @return array
     */
    private function getComponentsFile(): array
    {
        return $this->getCachedDecodedJsonFile(
            "componentsFile",
            THEMEVIZ_THEME_PATH . "/components.json"
        );
    }

    /**
     * @return array
     */
    private function getThemeConfig(): array
    {
        return $this->getCachedDecodedJsonFile(
            "themeConfig",
            THEMEVIZ_THEME_PATH . "/theme.conf"
        );
    }

    /**
     * @param $fieldName
     * @param $path
     * @return mixed
     */
    private function getCachedDecodedJsonFile($fieldName, $path)
    {
        if (!$this->$fieldName) {
            $json = $this->filesystem->getFile($path);
            $this->$fieldName = json_decode($json, TRUE) ?? [];
        }

        return $this->$fieldName;
    }
}
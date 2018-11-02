<?php

namespace ThemeViz;

class Renderer
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Less $less */
    private $less;

    /** @var LessCompiler $lessCompiler */
    private $lessCompiler;

    /** @var Twig $twig */
    private $twig;

    public function __construct(
        Filesystem $filesystem,
        Less $less,
        LessCompiler $lessCompiler,
        Twig $twig
    )
    {
        $this->filesystem = $filesystem;
        $this->less = $less;
        $this->lessCompiler = $lessCompiler;
        $this->twig = $twig;
    }

    public function compile()
    {
        if (!$components = $this->compileTwig()) return;

        $this->filesystem->deleteTree(THEMEVIZ_BASE_PATH . "/build");
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

        return THEMEVIZ_BASE_PATH . "/build/$directory/$filename--$scenarioName.$extension";
    }

    /**
     * @return array
     */
    private function compileTwig(): array
    {
        $this->twig->registerFunction(
            "getIcon",
            [$this, "getIcon"],
            ['is_safe' => ['html']]
        );

        return $this->compileScreens();
    }

    /**
     * @return array
     */
    private function compileScreens(): array
    {
        $screens = $this->getComponentsFile()["screens"] ?? [];

        return array_reduce($screens, function ($carry, $screen) {
            $path = $screen["path"];
            $renderedScenarios = $this->compileScenarios($screen);

            return array_merge($carry, [$path => $renderedScenarios]);
        }, []);
    }

    /**
     * @param array $screen
     * @return array
     */
    private function compileScenarios(array $screen): array
    {
        return array_map(function ($scenario) use ($screen) {
            return $this->compileScenario($screen, $scenario);
        }, $screen["scenarios"]);
    }

    /**
     * @param array $screen
     * @param array $scenario
     * @return string
     * @throws \Less_Exception_Parser
     */
    private function compileScenario(array $screen, array $scenario)
    {
        $classes = implode(",", $this->getComponentsFile()["wrapperClasses"] ?? []);
        $useBootstrap = $this->getThemeConfig()["depends"]["settings"]["global_bootstrap"] ?? FALSE;
        $css = $this->lessCompiler->getCss($this->getThemeConfig(), $this->getComponentsFile());
        $componentData = [
            "themeviz_component_path" => $screen["path"],
            "themeviz_wrapper_classes" => $classes,
            "themeviz_css" => $css,
            "themeviz_use_bootstrap" => $useBootstrap
        ];
        $themeDefaults = $this->getComponentsFile()["defaults"]["twig"] ?? [];
        $data = array_merge_recursive($componentData, $themeDefaults, $scenario);

        return $this->twig->renderFile("component.twig", $data);
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

    /**
     * @param string $string
     * @return string
     */
    public function getIcon(string $string)
    {
        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\" width=\"1em\" height=\"1em\" class=\"icon\" data-identifier=\"$string\"><path d=\"M416 192V81.9c0-6.4-2.5-12.5-7-17L351 7c-4.5-4.5-10.6-7-17-7H120c-13.3 0-24 10.7-24 24v168c-53 0-96 43-96 96v136c0 13.3 10.7 24 24 24h72v40c0 13.3 10.7 24 24 24h272c13.3 0 24-10.7 24-24v-40h72c13.3 0 24-10.7 24-24V288c0-53-43-96-96-96zM144 48h180.1L368 91.9V240H144V48zm224 416H144v-80h224v80zm96-64h-48v-40c0-13.2-10.8-24-24-24H120c-13.2 0-24 10.8-24 24v40H48V288c0-26.5 21.5-48 48-48v24c0 13.2 10.8 24 24 24h272c13.2 0 24-10.8 24-24v-24c26.5 0 48 21.5 48 48v112zm-8-96c0 13.3-10.7 24-24 24s-24-10.7-24-24 10.7-24 24-24 24 10.7 24 24z\"></path></svg>";
    }
}
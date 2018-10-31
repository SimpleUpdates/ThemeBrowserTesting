<?php

namespace ThemeViz;

class Renderer
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Less $less */
    private $less;

    /** @var Twig $twig */
    private $twig;

    /** @var array $config */
    private $config = [];

    /** @var string $css */
    private $css = "";

    public function __construct(Filesystem $filesystem, Less $less, Twig $twig)
    {
        $this->filesystem = $filesystem;
        $this->less = $less;
        $this->twig = $twig;
    }

    public function compile()
    {
        if (!$templates = $this->compileTwig()) return;

        $this->filesystem->deleteTree(THEMEVIZ_BASE_PATH . "/build");
        $this->persistScenarios($templates);
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
            $css = $this->getCss();
            $classes = implode(",",$this->getConfig()["wrapperClasses"] ?? []);
            $html = "<style>$css</style><div class='$classes'>$scenarios[$key]</div>";
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
        $screens = $this->getConfig()["screens"] ?? [];

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
     */
    private function compileScenario(array $screen, array $scenario)
    {
        $defaults = $this->getConfig()["defaults"]["twig"] ?? [];
        $data = array_merge_recursive($defaults, $scenario);

        return $this->twig->render(
            $screen["path"],
            $data
        );
    }

    /**
     * @throws \Less_Exception_Parser
     * @throws \Exception
     */
    private function getCss()
    {
        if (!$this->css) {
            $this->parseBaseLess();
            $this->parseLessDefaults();
            $this->parseThemeGlobalLess();

            $this->css = $this->less->getCss();
        }

        return $this->css;
    }

    private function parseLessDefaults(): void
    {
        $defaults = $this->getConfig()["defaults"]["less"] ?? [];

        $defaultsString = array_reduce(array_keys($defaults), function ($carry, $key) use ($defaults) {
            $varName = $key;
            $varValue = $defaults[$key];

            return "$carry $varName: $varValue;";
        }, "");

        $this->less->parse($defaultsString);
    }

    private function parseBaseLess(): void
    {
        $baseLess = $this->filesystem->getFile(THEMEVIZ_BASE_PATH . "/base.less") ?? "";
        $this->less->parse($baseLess);
    }

    /**
     * @throws \Less_Exception_Parser
     */
    private function parseThemeGlobalLess(): void
    {
        $this->less->parseFile(THEMEVIZ_THEME_PATH . "/style/global.less", THEMEVIZ_THEME_PATH);
    }

    public function getIcon(string $string)
    {
        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\" width=\"1em\" height=\"1em\" class=\"icon\" data-identifier=\"$string\"><path d=\"M416 192V81.9c0-6.4-2.5-12.5-7-17L351 7c-4.5-4.5-10.6-7-17-7H120c-13.3 0-24 10.7-24 24v168c-53 0-96 43-96 96v136c0 13.3 10.7 24 24 24h72v40c0 13.3 10.7 24 24 24h272c13.3 0 24-10.7 24-24v-40h72c13.3 0 24-10.7 24-24V288c0-53-43-96-96-96zM144 48h180.1L368 91.9V240H144V48zm224 416H144v-80h224v80zm96-64h-48v-40c0-13.2-10.8-24-24-24H120c-13.2 0-24 10.8-24 24v40H48V288c0-26.5 21.5-48 48-48v24c0 13.2 10.8 24 24 24h272c13.2 0 24-10.8 24-24v-24c26.5 0 48 21.5 48 48v112zm-8-96c0 13.3-10.7 24-24 24s-24-10.7-24-24 10.7-24 24-24 24 10.7 24 24z\"></path></svg>";
    }

    /**
     * @return array
     */
    private function getConfig(): array
    {
        if (!$this->config) {
            $configJson = $this->filesystem->getFile(THEMEVIZ_THEME_PATH . "/components.json");
            $this->config = json_decode($configJson, TRUE) ?: [];
        }

        return $this->config;
    }
}
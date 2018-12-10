<?php

namespace ThemeViz;

class TwigCompiler
{
    /** @var LessCompiler $lessCompiler */
    private $lessCompiler;

    /** @var Twig $twig */
    private $twig;

    private $themeConfig;
    private $componentsFile;

    private $css;

    public function __construct(LessCompiler $lessCompiler, Twig $twig)
    {
        $this->lessCompiler = $lessCompiler;
        $this->twig = $twig;
    }

    /**
     * @param $themeConfig
     * @param $componentsFile
     * @return array
     * @throws \Less_Exception_Parser
     */
    public function compileTwig($themeConfig, $componentsFile): array
    {
        $this->themeConfig = $themeConfig;
        $this->componentsFile = $componentsFile;

        $this->css = $this->lessCompiler->getCss($this->themeConfig, $this->componentsFile);

        return $this->compileScreens();
    }

    /**
     * @return array
     */
    private function compileScreens(): array
    {
        $screens = $this->componentsFile["screens"] ?? [];

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
		$defaultScenario = [];

		return array_map(function ($scenario) use ($screen) {
            return $this->compileScenario($screen, $scenario);
        }, $screen["scenarios"] ?? [$defaultScenario]);
    }

    /**
     * @param array $screen
     * @param array $scenario
     * @return string
     */
    private function compileScenario(array $screen, array $scenario)
    {
        $classes = implode(",", $this->componentsFile["wrapperClasses"] ?? []);
        $useBootstrap = $this->themeConfig["depends"]["settings"]["global_bootstrap"] ?? FALSE;
        $componentData = [
            "themeviz_component_path" => $screen["path"],
            "themeviz_wrapper_classes" => $classes,
            "themeviz_css" => $this->css,
            "themeviz_use_bootstrap" => $useBootstrap
        ];
        $themeDefaults = $this->componentsFile["defaults"]["twig"] ?? [];
        $data = array_merge_recursive($componentData, $themeDefaults, $scenario);

        return $this->twig->renderFile("component.twig", $data);
    }
}
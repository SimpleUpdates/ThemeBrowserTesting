<?php

namespace ThemeViz;

class TwigCompiler
{
	/** @var ComponentRepository $componentRepository */
	private $componentRepository;

    /** @var Twig $twig */
    private $twig;

    private $themeConfig;
    private $componentsFile;

    public function __construct(ComponentRepository $componentRepository, Twig $twig)
    {
    	$this->componentRepository = $componentRepository;
        $this->twig = $twig;
    }

    /**
     * @param $themeConfig
     * @param $componentsFile
     * @return array
	 */
    public function compileTwig($themeConfig, $componentsFile): array
    {
        $this->themeConfig = $themeConfig;
        $this->componentsFile = $componentsFile;

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
		$component = $this->componentRepository->getComponent($screen);

		$scenarios = $component->getScenarios();

		return array_map(function ($scenario) use ($screen) {
			return $this->twig->renderFile("component.twig", $scenario);
		}, $scenarios);
    }
}
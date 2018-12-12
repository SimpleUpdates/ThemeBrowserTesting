<?php

namespace ThemeViz;

class TwigCompiler
{
	/** @var ComponentFactory $componentRepository */
	private $componentRepository;

    /** @var Twig $twig */
    private $twig;

    public function __construct(ComponentFactory $componentRepository, Twig $twig)
    {
    	$this->componentRepository = $componentRepository;
        $this->twig = $twig;
    }

    /**
     * @return array
	 */
    public function compileTwig(): array
    {
		$components = $this->componentRepository->getComponents();

		return array_reduce($components, function ($carry, Component $component) {
			$path = $component->getPath();
			$renderedScenarios = $this->compileScenarios($component);

			return array_merge($carry, [$path => $renderedScenarios]);
		}, []);
    }

	/**
	 * @param $component
	 * @return array
	 */
    private function compileScenarios(Component $component): array
    {
		$scenarios = $component->getScenarios();

		return array_map(function ($scenario) {
			return $this->twig->renderFile("component.twig", $scenario);
		}, $scenarios);
    }
}
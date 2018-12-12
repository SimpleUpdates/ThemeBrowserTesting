<?php

namespace ThemeViz;

class TwigCompiler
{
	/** @var ComponentFactory $componentRepository */
	private $componentRepository;

	/** @var DataFactory $dataFactory */
	private $dataFactory;

    /** @var Twig $twig */
    private $twig;

    public function __construct(DataFactory $dataFactory, ComponentFactory $componentRepository, Twig $twig)
    {
    	$this->dataFactory = $dataFactory;
    	$this->componentRepository = $componentRepository;
        $this->twig = $twig;
    }

	/**
	 * @return array
	 * @throws \Less_Exception_Parser
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
			$data = $this->dataFactory->makeData($scenario);

			return $this->twig->renderFile("component.twig", $data);
		}, $scenarios);
    }
}
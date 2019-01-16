<?php

namespace ThemeViz;

class Build {
	/** @var Filesystem $filesystem */
	private $filesystem;

	/** @var Photographer $photographer */
	private $photographer;

	/** @var TwigStore $scenarioStorage */
	private $scenarioStorage;

	/** @var TwigCompiler $twigCompiler */
	private $twigCompiler;

	private $name;

	public function __construct(
		Filesystem $filesystem,
		Photographer $photographer,
		TwigStore $scenarioStorage,
		TwigCompiler $twigCompiler
	)
	{
		$this->filesystem = $filesystem;
		$this->photographer = $photographer;
		$this->scenarioStorage = $scenarioStorage;
		$this->twigCompiler = $twigCompiler;
	}

	public function setName(string $name)
	{
		$this->name = $name;
	}

	public function run()
	{
		$buildPath = THEMEVIZ_BASE_PATH . "/build/$this->name";

		$this->filesystem->deleteTree($buildPath);

		if (!$components = $this->twigCompiler->compileTwig()) return;

		$this->scenarioStorage->persistTwigComponents(
			$components,
			"$buildPath/html"
		);

		$this->photographer->photographComponents(
			"$buildPath/html",
			"$buildPath/shots"
		);
	}
}
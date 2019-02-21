<?php

namespace ThemeViz\File\TwigFile;


use ThemeViz\File\TwigFile;

class Scenario extends TwigFile
{
	protected $template = "component.twig";

	private $scenarioData = [];
	private $buildName;
	private $name;

	/**
	 * @param mixed $scenarioData
	 * @return Scenario
	 */
	public function setScenarioData($scenarioData)
	{
		$this->scenarioData = $scenarioData;
		return $this;
	}

	/**
	 * @param mixed $buildName
	 * @return Scenario
	 */
	public function setBuildName($buildName)
	{
		$this->buildName = $buildName;
		return $this;
	}

	/**
	 * @param mixed $name
	 * @return Scenario
	 */
	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getHeadHtml()
	{
		return $this->renderHtml("head");
	}

	public function getProductionHtml()
	{
		return $this->renderHtml("production");
	}

	/**
	 * @return string
	 * @throws \Exception
	 */
	protected function makeContents()
	{
		$dataArray = $this->getDataArray();

		if ($this->stylesheet) {
			$styleFolder = THEMEVIZ_BASE_PATH . "/style";
			$filePath = "$styleFolder/$this->stylesheet";
			$this->less->parseFile($filePath, $styleFolder);
			$css = $this->less->getCss();
			$dataArray["themeviz_css"] = $css;
		}

		return $this->twig->renderFile($this->template, $dataArray, $this->buildName);
	}

	/**
	 * @param $state
	 * @return string
	 */
	private function renderHtml($state)
	{
		return $this->twig->renderFile(
			$this->scenarioData["themeviz_component_path"],
			$this->getDataArray(),
			$state
		);
	}

	protected function getDataArray()
	{
		return array_merge($this->scenarioData, [
			"themeviz_build_name" => $this->buildName,
			"themeviz_css_href" => $this->getCssHref()
		]);
	}

	protected function getBuildPath()
	{
		$relativePath = $this->getRelativePath();

		return "$this->buildName/html/$relativePath";
	}

	public function getRelativePath()
	{
		$pathParts = pathinfo($this->scenarioData["themeviz_component_path"]);
		$directory = $pathParts["dirname"];
		$filename = $pathParts["filename"];
		$extension = $pathParts["extension"];

		return "$directory/$filename--$this->name.$extension";
	}

	/**
	 * @return string
	 */
	protected function getCssHref(): string
	{
		$htmlRelDir = pathinfo($this->scenarioData["themeviz_component_path"], PATHINFO_DIRNAME);
		$dirPieces = explode("/", $htmlRelDir);
		$dirPieceCount = count($dirPieces);
		$cssHref = str_repeat("../", $dirPieceCount + 1) . "theme.css";
		return $cssHref;
	}
}
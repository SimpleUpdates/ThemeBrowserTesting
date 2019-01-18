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

	protected function getDataArray()
	{
		return array_merge($this->scenarioData, [
			"themeviz_css_href" => $this->getCssHref()
		]);
	}

	protected function getBuildPath()
	{
		$pathParts = pathinfo($this->scenarioData["themeviz_component_path"]);
		$directory = $pathParts["dirname"];
		$filename = $pathParts["filename"];
		$extension = $pathParts["extension"];

		return "$this->buildName/html/$directory/$filename--$this->name.$extension";
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
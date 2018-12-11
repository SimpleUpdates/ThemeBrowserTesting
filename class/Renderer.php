<?php

namespace ThemeViz;

class Renderer
{
    /** @var Differ $differ */
    private $differ;

    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Git $git */
    private $git;

    /** @var Photographer $photographer */
    private $photographer;

    /** @var ScenarioStorage $scenarioStorage */
    private $scenarioStorage;

    /** @var SummaryCompiler $summaryCompiler */
    private $summaryCompiler;

    /** @var TwigCompiler $twigCompiler */
    private $twigCompiler;

    private $themeConfig;
    private $componentsFile;

    public function __construct(
        Differ $differ,
        Filesystem $filesystem,
        Git $git,
        Photographer $photographer,
        ScenarioStorage $scenarioStorage,
        SummaryCompiler $summaryCompiler,
        TwigCompiler $twigCompiler
    )
    {
        $this->differ = $differ;
        $this->filesystem = $filesystem;
        $this->git = $git;
        $this->photographer = $photographer;
        $this->scenarioStorage = $scenarioStorage;
        $this->summaryCompiler = $summaryCompiler;
        $this->twigCompiler = $twigCompiler;
    }

    /**
     * @throws \Exception
     */
    public function compile()
    {
        $this->themeConfig = $this->getThemeConfig();
        $this->componentsFile = $this->getComponentsFile();

        if (!$this->themeConfig) {
        	throw new \Exception("Missing theme.conf file!");
		}

        if (!$this->componentsFile) {
        	throw new \Exception("Missing components file!");
		}

        $this->filesystem->deleteTree(THEMEVIZ_BASE_PATH . "/build");

        $this->makeBuild("pull");

        $this->git->saveState(THEMEVIZ_THEME_PATH);
        $this->git->checkoutRemoteBranch(THEMEVIZ_THEME_PATH, "production");
        $this->git->pull(THEMEVIZ_THEME_PATH, "production");

        $this->makeBuild("production");

        $this->git->resetState(THEMEVIZ_THEME_PATH);

        $this->differ->buildDiffs();

        $this->summaryCompiler->compile();
    }

    /**
     * @param $buildName
     * @throws \Less_Exception_Parser
     */
    private function makeBuild($buildName): void
    {
        if (!$components = $this->twigCompiler->compileTwig($this->themeConfig, $this->componentsFile)) return;

        $componentFolder = THEMEVIZ_BASE_PATH . "/build/$buildName/html";
        $photoFolder = THEMEVIZ_BASE_PATH . "/build/$buildName/shots";

        $this->scenarioStorage->persistScenarios($components, $componentFolder);
        $this->photographer->photographComponents($componentFolder, $photoFolder);
    }

	/**
	 * @return array
	 * @throws \Exception
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
	 * @throws \Exception
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
	 * @throws \Exception
	 */
    private function getCachedDecodedJsonFile($fieldName, $path)
    {
        if (!$this->$fieldName) {
            $json = $this->filesystem->getFile($path);

			$this->$fieldName = json_decode($json, TRUE);

            if ($json && $this->$fieldName === NULL) {
            	throw new \Exception("Error attempting to decode json file: $path");
			}
        }

        return $this->$fieldName;
    }
}
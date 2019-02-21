<?php

namespace ThemeViz;

class Twig
{
    private $twigOptions;
    private $filesystemLoader;
    private $twig;

    public function renderFile($templateFile, $data = [], $state = "head")
    {
    	$this->init($state);

        $template = $this->twig->load($templateFile);

        $data["themeviz_theme_path"] = THEMEVIZ_THEME_PATH;
        $data["themeviz_base_path"] = THEMEVIZ_BASE_PATH;

        return $template->render($data);
    }

    private function registerFunction($name, $function, $options = [])
    {
        $this->twig->addFunction(new \Twig_Function($name, $function, $options));
    }

    /**
     * @param string $string
     * @return string
     */
    public function getIcon(string $string)
    {
        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 512 512\" width=\"1em\" height=\"1em\" class=\"icon\" data-identifier=\"$string\"><path d=\"M416 192V81.9c0-6.4-2.5-12.5-7-17L351 7c-4.5-4.5-10.6-7-17-7H120c-13.3 0-24 10.7-24 24v168c-53 0-96 43-96 96v136c0 13.3 10.7 24 24 24h72v40c0 13.3 10.7 24 24 24h272c13.3 0 24-10.7 24-24v-40h72c13.3 0 24-10.7 24-24V288c0-53-43-96-96-96zM144 48h180.1L368 91.9V240H144V48zm224 416H144v-80h224v80zm96-64h-48v-40c0-13.2-10.8-24-24-24H120c-13.2 0-24 10.8-24 24v40H48V288c0-26.5 21.5-48 48-48v24c0 13.2 10.8 24 24 24h272c13.2 0 24-10.8 24-24v-24c26.5 0 48 21.5 48 48v112zm-8-96c0 13.3-10.7 24-24 24s-24-10.7-24-24 10.7-24 24-24 24 10.7 24 24z\"></path></svg>";
    }

	private function init($state): void
	{
		$statePaths = [
			"head" => THEMEVIZ_THEME_PATH,
			"production" => THEMEVIZ_BASE_PATH . "/tmp"
		];

		$sourcePath = $statePaths[$state];

		$this->twigOptions = ["debug" => TRUE];

		$this->filesystemLoader = new \Twig_Loader_Filesystem([
			THEMEVIZ_BASE_PATH . "/view",
			$sourcePath
		]);

		$this->twig = new \Twig_Environment($this->filesystemLoader, $this->twigOptions);
		$this->twig->addExtension(new \Twig_Extension_Debug());

		$this->registerFunction(
			"getIcon",
			[$this, "getIcon"],
			['is_safe' => ['html']]
		);
	}
}
<?php

namespace ThemeViz;

class Twig
{
    private $twigOptions;
    private $filesystemLoader;
    private $twig;

    public function __construct()
    {
        $this->twigOptions = ["debug" => TRUE];

        $this->filesystemLoader = new \Twig_Loader_Filesystem([
            THEMEVIZ_BASE_PATH . "/view",
            THEMEVIZ_THEME_PATH
        ]);

        $this->twig = new \Twig_Environment($this->filesystemLoader, $this->twigOptions);
    }

    public function renderFile($templateFile, $data = [])
    {
        $template = $this->twig->load($templateFile);

        $data["themeviz_theme_path"] = THEMEVIZ_THEME_PATH;
        $data["themeviz_base_path"] = THEMEVIZ_BASE_PATH;

        return $template->render($data);
    }

    public function registerFunction($name, $function, $options = [])
    {
        $this->twig->addFunction(new \Twig_Function($name, $function, $options));
    }
}
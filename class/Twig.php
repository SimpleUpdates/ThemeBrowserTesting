<?php

namespace ThemeViz;

class Twig
{
    private $twig;

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem([
            THEMEVIZ_THEME_PATH,
            THEMEVIZ_THEME_PATH . "/partial",
            THEMEVIZ_THEME_PATH . "/layout"
        ]);

        $this->twig = new \Twig_Environment($loader, ["debug" => TRUE]);
    }

    public function render($templateFile, $data = [])
    {
        $template = $this->twig->load($templateFile);

        return $template->render($data);
    }

    public function registerFunction($name, $function, $options = [])
    {
        $this->twig->addFunction(new \Twig_Function($name, $function, $options));
    }
}
<?php

namespace ThemeViz;

class Less
{
    private $less;

    public function __construct()
    {
        $this->resetParser();
    }

    /**
     * @param string $less
     */
    public function parse(string $less): void
    {
        $this->less->parse($less);
    }

    /**
     * @param string $filename
     * @param string $uriRoot
	 */
    public function parseFile(string $filename, string $uriRoot): void
    {
        $this->less->parseFile($filename, $uriRoot);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getCss()
    {
        return $this->less->getCss();
    }

    public function resetParser()
    {
        $this->less = new \Less_Parser();
    }
}
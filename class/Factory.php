<?php

namespace ThemeViz;

class Factory
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Firefox $firefox */
    private $firefox;

    /** @var Git $git */
    private $git;

    /** @var Less $less */
    private $less;

    /** @var Pixelmatch $pixelmatch */
    private $pixelmatch;

    /** @var Twig $twig */
    private $twig;

	/**
	 * Factory constructor.
	 * @param Filesystem|null $filesystem
	 * @param Firefox|null $firefox
	 * @param Git|null $git
	 * @param Less|null $less
	 * @param Pixelmatch|null $pixelmatch
	 * @param Twig|null $twig
	 */
	public function __construct(
        Filesystem $filesystem = null,
        Firefox $firefox = null,
        Git $git = null,
        Less $less = null,
        Pixelmatch $pixelmatch = null,
        Twig $twig = null
    )
    {
        $this->filesystem = $filesystem;
        $this->firefox = $firefox;
        $this->git = $git;
        $this->less = $less;
        $this->pixelmatch = $pixelmatch;
        $this->twig = $twig;
    }

	/**
	 * @param $method
	 * @param array $args
	 * @return null
	 * @throws \ReflectionException
	 */
	public function __call($method, $args = [])
	{
		$isGet = substr( $method, 0, 3 ) === "get";

		if (!$isGet) return null;

		$name = substr($method, 3, strlen($method) - 3);
		$dependencyNames = $this->getDependencyNames($name);
		$dependencies = array_map(function($dependencyName) {
			$methodName = "get$dependencyName";
			return $this->$methodName();
		}, $dependencyNames);

		return $this->getObject($name, ...$dependencies);
	}

	/**
	 * @param $name
	 * @return array|mixed
	 * @throws \ReflectionException
	 */
	private function getDependencyNames($name)
	{
		$simpleName = $this->getSimpleClassName($name);
		$reflection = new \ReflectionClass("\\ThemeViz\\$simpleName");
		$constructor = $reflection->getConstructor();
		$params = ($constructor) ? $constructor->getParameters() : [];

		return array_map(function($param) {
			$name = $param->getClass()->name;

			return $this->getSimpleClassName($name);
		}, $params);
	}

	/**
	 * @param $name
	 * @return string
	 */
	private function getSimpleClassName($name): string
	{
		$nameFragments = explode("\\", $name);

		return end($nameFragments);
	}

	/**
	 * @param $class
	 * @param mixed ...$dependencies
	 * @return mixed
	 */
	private function getObject($class, ...$dependencies)
    {
        $fullClassName = "\\ThemeViz\\$class";
        $propertyName = lcfirst($class);

        if (! isset($this->$propertyName)) {
            $this->$propertyName = new $fullClassName(...$dependencies);
        }

        return $this->$propertyName;
    }
}
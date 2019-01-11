<?php

namespace ThemeViz;

class Factory
{
	private $namespace = __NAMESPACE__;
	private $objects = [];

	public function __construct(...$objects)
	{
		if ($objects) {
			$this->injectObjects(...$objects);
		}
	}

	public function injectObjects(...$objects)
	{
		$this->objects = array_merge($this->objects, $objects);
	}

	/**
	 * @param $class
	 * @return null
	 * @throws \ReflectionException
	 */
	public function get($class)
	{
		if (is_a($this, $class)) return $this;
		$qualifiedName = $this->getQualifiedName($class);
		$dependencies = $this->getDependencies($qualifiedName);
		return $this->getObject($qualifiedName, ...$dependencies);
	}

	/**
	 * @param $class
	 * @return null
	 * @throws \ReflectionException
	 */
	public function make($class)
	{
		$qualifiedName = $this->getQualifiedName($class);
		$dependencies = $this->getDependencies($qualifiedName);
		return $this->makeObject($qualifiedName, ...$dependencies);
	}

	/**
	 * @param $qualifiedName
	 * @return array
	 * @throws \ReflectionException
	 */
	private function getDependencies($qualifiedName)
	{
		$dependencyNames = $this->getDependencyNames($qualifiedName);
		return array_map([$this, "get"], $dependencyNames);
	}

	/**
	 * @param $className
	 * @return array|mixed
	 * @throws \ReflectionException
	 */
	private function getDependencyNames($className)
	{
		$reflection = new \ReflectionClass($className);
		$constructor = $reflection->getConstructor();
		$params = ($constructor) ? $constructor->getParameters() : [];
		return array_map(function (\ReflectionParameter $param) {
			$name = $param->getClass()->name;
			return $this->getQualifiedName($name);
		}, $params);
	}

	/**
	 * @param $name
	 * @return string
	 */
	private function getQualifiedName($name)
	{
		$isQualified = strpos(trim($name, "\\"), "$this->namespace\\") === 0;
		return $isQualified ? $name : "\\$this->namespace\\$name";
	}

	/**
	 * @param string $class
	 * @param array ...$dependencies
	 * @return mixed
	 */
	private function getObject($class, ...$dependencies)
	{
		return $this->getSavedObject($class) ?:
			$this->objects[] = new $class(...$dependencies);
	}

	/**
	 * @param string $class
	 * @param array ...$dependencies
	 * @return mixed
	 */
	private function makeObject($class, ...$dependencies)
	{
		return $this->getSavedObject($class) ?:
			new $class(...$dependencies);
	}

	/**
	 * @param $class
	 * @return mixed
	 */
	private function getSavedObject($class)
	{
		$matchingObjects = array_filter($this->objects, function ($object) use ($class) {
			return is_a($object, $class);
		});
		return end($matchingObjects);
	}
}
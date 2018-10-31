<?php

namespace ThemeViz;

trait Stub
{
    private $calls = [];
    private $returnValues = [];
    private $indexedReturnValues = [];
    private $methodCallIndices = [];

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct() {}

    /**
     * @param $method
     * @param $args
     * @return mixed|null
     */
    public function handleCall($method, $args)
    {
        $this->calls[$method][] = $args;

        return $this->getIndexedReturnValue($method) ?? $this->getReturnValue($method);
    }

    /**
     * @param $method
     * @param $returnValue
     */
    public function setReturnValue($method, $returnValue): void
    {
        $this->returnValues[$method] = $returnValue;
    }

    /**
     * @param int $index Zero-based call index
     * @param $method
     * @param $returnValue
     */
    public function setReturnValueAt(int $index, string $method, $returnValue): void
    {
        $this->indexedReturnValues[$method][$index] = $returnValue;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function wasMethodCalled(string $method)
    {
        return !empty($this->getCalls($method));
    }

    /**
     * @param string $method
     * @param mixed ...$args
     * @return bool
     */
    public function wasMethodCalledWith(string $method, ...$args): bool
    {
        return in_array($args, $this->getCalls($method));
    }

    /**
     * @param string $method
     * @param string $needle
     * @return bool
     */
    public function doCallsContain(string $method, string $needle)
    {
        $haystack = json_encode($this->getCalls($method));

        return strpos($haystack, $needle) !== false;
    }

    /**
     * @param $method
     * @return array
     */
    public function getCalls($method): array
    {
        return $this->calls[$method] ?? [];
    }

    /**
     * @param $method
     * @return mixed
     */
    private function getIndexedReturnValue($method)
    {
        $this->incrementCallIndex($method);

        $currentIndex = $this->methodCallIndices[$method];

        return $this->indexedReturnValues[$method][$currentIndex] ?? null;
    }

    /**
     * @param $method
     * @return mixed
     */
    private function getReturnValue($method)
    {
        return $this->returnValues[$method] ?? null;
    }

    /**
     * @param $method
     */
    private function incrementCallIndex($method): void
    {
        $this->methodCallIndices[$method] =
            isset($this->methodCallIndices[$method]) ? $this->methodCallIndices[$method] + 1 : 0;
    }
}

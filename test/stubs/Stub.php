<?php

namespace ThemeViz;

trait Stub
{
    private $calls = [];
    private $returnValues = [];
    private $indexedReturnValues = [];
    private $mappedReturnValues = [];
    private $methodCallIndices = [];

    /** @var \PHPUnit\Framework\TestCase $testCase */
    private $testCase;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(\PHPUnit\Framework\TestCase $testCase) {
        $this->testCase = $testCase;
    }

    /**
     * @param $method
     * @param $args
     * @return mixed|null
     */
    public function handleCall($method, $args)
    {
        $this->calls[$method][] = $args;

        return $this->getIndexedReturnValue($method) ??
            $this->getMappedReturnValue($method, $args) ??
            $this->getReturnValue($method);
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
     */
    private function incrementCallIndex($method): void
    {
        $this->methodCallIndices[$method] =
            isset($this->methodCallIndices[$method]) ? $this->methodCallIndices[$method] + 1 : 0;
    }

    /**
     * @param $method
     * @param $args
     * @return null
     */
    private function getMappedReturnValue($method, $args)
    {
        $callSignature = json_encode($args);

        return $this->mappedReturnValues[$method][$callSignature] ?? null;
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
     * @param array $map Array of arrays, each internal array representing a list of arguments followed by a single
     * return value
     */
    public function setMappedReturnValues(string $method, array $map)
    {
        $processedMap = array_reduce($map, function($carry, $entry) use($method) {
            $returnValue = array_pop($entry);
            $callSignature = json_encode($entry);
            return array_merge($carry, [
                $callSignature => $returnValue
            ]);
        }, []);

        $this->mappedReturnValues[$method] = array_merge(
            $this->mappedReturnValues[$method] ?? [],
            $processedMap
        );
    }

    /**
     * @param string $method
     */
    public function assertMethodCalled(string $method)
    {
        $this->testCase->assertTrue(
            $this->wasMethodCalled($method),
            "Failed asserting that '$method' was called"
        );
    }

    /**
     * @param string $method
     */
    public function assertMethodNotCalled(string $method)
    {
        $this->testCase->assertFalse(
            $this->wasMethodCalled($method),
            "Failed asserting that '$method' was not called"
        );
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
     */
    public function assertMethodCalledWith(string $method, ...$args)
    {
        $argsExport = var_export($args, TRUE);
        $haystackExport = var_export($this->getCalls($method), TRUE);
        $message = "Failed asserting that '$method' was called with args $argsExport\r\n\r\nHaystack:\r\n$haystackExport";

        $this->testCase->assertTrue(
            $this->wasMethodCalledWith($method, ...$args),
            $message
        );
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

    public function assertAnyCallMatches(string $method, callable $callable)
    {
        $calls = $this->getCalls($method);
        $bool = array_reduce($calls, $callable, FALSE);
        $haystackExport = var_export($calls, TRUE);
        $message = "Failed asserting any call matches callback.\r\n\r\nHaystack:\r\n$haystackExport";
        $this->testCase->assertTrue($bool, $message);
    }

    /**
     * @param string $method
     * @param string $needle
     */
    public function assertCallsContain(string $method, string $needle)
    {
        $message = "Failed asserting that '$needle' is in haystack: \r\n" .
            $this->getCallHaystack($method);

        $this->testCase->assertTrue(
            $this->doCallsContain($method, $needle),
            $message
        );
    }

    /**
     * @param string $method
     * @param string $needle
     * @return bool
     */
    public function doCallsContain(string $method, string $needle)
    {
        $haystack = $this->getCallHaystack($method);

        return strpos($haystack, $needle) !== false;
    }

    /**
     * @param string $method
     * @return string
     */
    private function getCallHaystack(string $method): string
    {
        return stripslashes(var_export($this->getCalls($method), true));
    }

    /**
     * @param $method
     * @return array
     */
    public function getCalls($method): array
    {
        return $this->calls[$method] ?? [];
    }
}

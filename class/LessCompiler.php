<?php

namespace ThemeViz;

class LessCompiler
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /** @var Less $less */
    private $less;

    /** @var string $css */
    private $css = "";

    public function __construct(Filesystem $filesystem, Less $less)
    {
        $this->filesystem = $filesystem;
        $this->less = $less;
    }

    /**
     * @param $themeConfig
     * @param $componentsFile
     * @return string
     * @throws \Less_Exception_Parser
     */
    public function getCss($themeConfig, $componentsFile)
    {
        if (!$this->css) {
            $this->parseBaseLess();
            $this->parseThemeConfigProperties($themeConfig);
            $this->parseLessDefaults($componentsFile);
            $this->parseThemeGlobalLess();

            $this->css = $this->less->getCss();
        }

        return $this->css;
    }

    private function parseLessDefaults($componentsFile): void
    {
        $defaults = $componentsFile["defaults"]["less"] ?? [];

        $defaultsString = array_reduce(array_keys($defaults), function ($carry, $key) use ($defaults) {
            $varName = $key;
            $varValue = $defaults[$key];

            return "$carry $varName: $varValue;";
        }, "");

        $this->less->parse($defaultsString);
    }

    private function parseBaseLess(): void
    {
        $baseLess = $this->filesystem->getFile(THEMEVIZ_BASE_PATH . "/view/base.less") ?? "";
        $this->less->parse($baseLess);
    }

    /**
     * @throws \Less_Exception_Parser
     */
    private function parseThemeGlobalLess(): void
    {
        $this->less->parseFile(THEMEVIZ_THEME_PATH . "/style/global.less", THEMEVIZ_THEME_PATH);
    }

    private function parseThemeConfigProperties($themeConfig): void
    {
        $properties = $themeConfig["config"] ?? [];
        $keys = array_keys($properties);

        $themeConfigLess = array_reduce($keys, function ($carry, $key) use ($properties) {
            $property = $properties[$key];
            $rawValue = $property['value'];
            $formattedValue = $this->isImageProperty($property) ? $this->formatImageLessValue($rawValue) : $rawValue;

            return "$carry @config-$key: $formattedValue;";
        }, "");

        $this->less->parse($themeConfigLess);
    }

    /**
     * @param $value
     * @return string
     */
    private function formatImageLessValue($value): string
    {
        $vendorSubstitutedPathFragment = str_replace("{{ su.misc.privatelabel }}", "su", $value);
        $fullPath = THEMEVIZ_THEME_PATH . "/asset/$vendorSubstitutedPathFragment";

        return "'$fullPath'";
    }

    /**
     * @param $property
     * @return bool
     */
    private function isImageProperty($property): bool
    {
        return $property["type"] === "image";
    }
}
<?php

final class TestPhotographer extends ThemeViz\TestCase
{
    /** @var \ThemeViz\Photographer $renderer */
    private $photographer;

    protected function setUp()
    {
        parent::setUp();

        $this->photographer = $this->factory->getPhotographer();
    }

    public function testPersistsPhotoPath()
    {
        $this->photographer->photographComponents(
            "/path/to/components",
            "/path/to/photos"
        );

        $this->mockFilesystem->assertMethodCalledWith(
            "makeTree",
            "/path/to/photos"
        );
    }

    public function testScansForComponents()
    {
        $this->photographer->photographComponents(
            "/path/to/components",
            "/path/to/photos"
        );

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            "/path/to/components"
        );
    }

    public function testPhotographsComponents()
    {
        $this->mockFilesystem->setReturnValue(
            "scanDir",
            ["component.html"]
        );

        $this->photographer->photographComponents(
            "/path/to/components",
            "/path/to/photos"
        );

        $this->mockFirefox->assertMethodCalledWith(
            "saveShot",
            "/path/to/photos",
            "component.png",
            "/path/to/components/component.html"
        );
    }
    
    public function testDoesNotPhotographDots()
    {
        $this->mockFilesystem->setReturnValue(
            "scanDir",
            [".",".."]
        );

        $this->photographer->photographComponents(
            "/path/to/components",
            "/path/to/photos"
        );

        $this->mockFirefox->assertMethodNotCalled("saveShot");
    }

    public function testChecksIfDir()
    {
        $this->mockFilesystem->setReturnValue(
            "scanDir",
            ["component.html"]
        );

        $this->photographer->photographComponents(
            "/path/to/components",
            "/path/to/photos"
        );

        $this->mockFilesystem->assertMethodCalledWith(
            "isDir",
            "/path/to/components/component.html"
        );
    }

    public function testScansRecursively()
    {
        $this->mockFilesystem->setReturnValueAt(
            0,
            "scanDir",
            ["dir"]
        );

        $this->mockFilesystem->setREturnValueAt(
            0,
            "isDir",
            TRUE
        );

        $this->photographer->photographComponents(
            "/path/to/components",
            "/path/to/photos"
        );

        $this->mockFilesystem->assertMethodCalledWith(
            "scanDir",
            "/path/to/components/dir"
        );

        $this->mockFirefox->assertMethodNotCalled("saveShot");
    }
}
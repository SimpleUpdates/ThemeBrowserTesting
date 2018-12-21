<?php

final class TestDataFactory extends ThemeViz\TestCase {

	/** @var \ThemeViz\DataFactory $dataFactory */
	private $dataFactory;

	private $themeConfWithCollection = [
		"depends" => [
			"collections" => [
				"myCarousel" => [
					"name" => "My Carousel",
					"singular_name" => "Carousel Item",
					"fields" => [
						"image" => [
							"name" => "Image",
							"type" => "image",
							"required" => true,
							"image_min_size" => "1500 x 425"
						],
						"caption" => [
							"name" => "Slide Caption",
							"type" => "text"
						],
						"description" => [
							"name" => "Slide Description",
							"type" => "text"
						],
						"url" => [
							"name" => "Link",
							"type" => "text"
						],
						"urlTarget" => [
							"name" => "Open Link in New Window",
							"type" => "tf"
						],
						"activationDate" => [
							"name" => "Activation Date",
							"type" => "date"
						],
						"deactivationDate" => [
							"name" => "Deactivation Date",
							"type" => "date"
						]
					],
					"data" => [
						[
							"image" => "{{ su.misc.privatelabel }}/carousel1.jpg",
							"caption" => "Slide One",
							"description" => "Description for slide one. Edit in theme customizer."
						],
						[
							"image" => "{{ su.misc.privatelabel }}/carousel2.jpg",
							"caption" => "Slide Two",
							"description" => "Description for slide two. Edit in theme customizer."
						],
						[
							"image" => "{{ su.misc.privatelabel }}/carousel3.jpg",
							"caption" => "Slide Three",
							"description" => "Description for slide three. Edit in theme customizer."
						]
					]
				]
			]
		]
	];

	protected function setUp()
	{
		parent::setUp();

		$this->dataFactory = $this->factory->getDataFactory();
	}

//	public function testLoadsCollectionData()
//	{
//		$this->loadMinimalComponentsFile();
//
//		$this->mockFilesystem->loadThemeConf($this->themeConfWithCollection);
//
//		$data = $this->dataFactory->makeData([]);
//
//		$result = $data->su()->collection('myCarousel')->find();
//
//		$this->assertCount(3, $result);
//	}

	public function testReturnsArray()
	{
		$this->loadMinimalComponentsFile();

		$data = $this->dataFactory->makeData([]);

		$this->assertTrue(is_array($data));
	}
}
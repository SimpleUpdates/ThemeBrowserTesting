<?php

namespace ThemeViz;


class DataFactory
{
	public function makeData($array) {
		return new Data($array);
	}
}
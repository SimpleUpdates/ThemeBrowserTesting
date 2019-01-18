<?php

namespace ThemeViz;


class Doiuse
{
	public function run($cssPath)
	{
		return $this->exec("doiuse --browsers \"ie >= 9, > 1%, last 2 versions\" $cssPath");
	}

	private function exec($command)
	{
		exec($command, $output);

		return $output;
	}
}
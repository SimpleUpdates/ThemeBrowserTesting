<?php

namespace ThemeViz;

define("THEMEVIZ_BASE_PATH", dirname(__FILE__));
include_once(THEMEVIZ_BASE_PATH . "/vendor/autoload.php");

$app = new \Slim\App([
	"debug" => true
]);

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->add(function (Request $request, Response $response, callable $next) {
	$uri = $request->getUri();
	$path = $uri->getPath();
	if ($path != '/' && substr($path, -1) == '/') {
		// permanently redirect paths with a trailing slash
		// to their non-trailing counterpart
		$uri = $uri->withPath(substr($path, 0, -1));

		if($request->getMethod() == 'GET') {
			return $response->withRedirect((string)$uri, 301);
		}
		else {
			return $next($request->withUri($uri), $response);
		}
	}

	return $next($request, $response);
});

$app->get('/', function ($request, $response, $args) {
	if ($_GET["theme"]) {
		define("THEMEVIZ_THEME_PATH", realpath($_GET["theme"]));
		$factory = new Factory();
		/** @var App $tvApp */
		$tvApp = $factory->getApp();
		$tvApp->buildStyleGuide();
		$styleGuide = file_get_contents(THEMEVIZ_BASE_PATH."/build/styleGuide.html");

		return $response->getBody()->write($styleGuide);
	}

	return $response->getBody()->write(<<<DOC
<form method="get">
	<input type="text" name="theme" placeholder="Theme Path" />
</form>
DOC
	);
});

$app->get('/html', function ($request, $response, array $args) {
	if ($_GET["path"]) {
		$html = file_get_contents($_GET["path"]);

		return $response->getBody()->write($html);
	}
});

$app->run();
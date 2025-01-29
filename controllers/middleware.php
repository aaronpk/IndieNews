<?php

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Set the Not Found Handler
$errorMiddleware->setErrorHandler(
  HttpNotFoundException::class,
  function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {

    $response = new Response();
    $response->getBody()->write('404 Not Found');

    return $response->withStatus(404);

  }
);

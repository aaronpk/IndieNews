<?php
$app->get('/', function($request, $response) {
  return render($response, 'index', array(
    'title' => 'IndieNews homepage',
    'meta' => '',
    'lang' => 'en'
  ));
});

$app->get('/how-to-submit-a-post', function($request, $response) use($app) {
  return render($response, 'submit-full', array(
    'title' => 'IndieNews - How to submit a post',
    'meta' => '',
    'lang' => 'en'
  ));
});

$app->redirect('/how', '/how-to-submit-a-post', 301);
$app->redirect('/how-to-comment', '/how-to-submit-a-post', 301);
$app->redirect('/constructing-post-urls', '/how-to-submit-a-post', 301);

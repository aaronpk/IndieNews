<?php

// Home Page
$app->get('/', function() use($app) {

  $req = $app->request();

  render('index', array(
    'title' => 'IndieNews',
    'meta' => ''
  ));

});

// Newest
$app->get('/newest', function() use($app) {

  $req = $app->request();

  $posts = ORM::for_table('posts')->where('parent_id', 0)->order_by_desc('date_submitted')->find_many();

  render('newest', array(
    'title' => 'IndieNews - Newest Submissions',
    'posts' => $posts,
    'meta' => ''
  ));

});

// Single Post Page
$app->get('/post/:id', function($id) use($app) {

  $post = ORM::for_table('posts')->where('id', $id)->find_one();

  if(!$post) {
    $app->pass(); // Will trigger a 404 error
  }

  render('post', array(
    'title' => $post->title,
    'post' => $post,
    'meta' => ''
  ));

})->conditions(array('id'=>'\d+'));


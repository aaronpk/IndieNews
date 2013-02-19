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

$app->post('/webmention', function() use($app) {

  $req = $app->request();
  $res = $app->response();

  $sourceURL = $req->post('source');
  $targetURL = $req->post('target');

  $error = function($res, $err) {
    $res->status(400);
    $res['Content-Type'] = 'application/json';
    $res->body(json_encode(array(
      'error' => $err
    )));
  };
  
  $source = parse_url($sourceURL);

  # Verify $source is valid
  if($source == FALSE
    || !array_key_exists('scheme', $source)
    || !in_array($source['scheme'], array('http','https'))
    || !array_key_exists('host', $source)
    || ($source['host'] == gethostbyname($source['host']))
  ) {
    $error($res, 'source_not_found');
    return;
  }


  # Verify $target is actually a resource under our control (home page, post ID)
  $target = parse_url($targetURL);
  # Verify $source is valid
  if($target == FALSE
    || !array_key_exists('scheme', $target)
    || !in_array($target['scheme'], array('http','https'))
    || !array_key_exists('host', $target)
    || $target['host'] != $_SERVER['SERVER_NAME']
  ) {
    $error($res, 'target_not_supported');
    return;
  }


  # Verify the $source actually contains a link to $target
  if(FALSE) {
    $error($res, 'no_link_found');
  }


  # Get the domain of $source and find or create a user account
  $domain = $source['host'];
  $user = ORM::for_table('users')->where('domain', $domain)->find_one();

  if($user == FALSE) {
    $user = ORM::for_table('users')->create();
    $user->domain = $domain;
    $user->date_created = date('Y-m-d H:i:s');
    $user->save();
  }


  # Make sure there is no existing post for $source
  $post = ORM::for_table('posts')->where('href', $sourceURL)->find_one();

  if($post != FALSE) {
    $error($res, 'already_registered');
  }


  # Record a new post and a vote from the domain

  $post = ORM::for_table('posts')->create();
  $post->user_id = $user->id;
  $post->date_submitted = date('Y-m-d H:i:s');
  $post->domain = $domain;
  $post->title = '';
  $post->href = $sourceURL;
  $post->points = 1;
  $post->save();

  $vote = ORM::for_table('votes')->create();
  $vote->post_id = $post->id;
  $vote->user_id = $user->id;
  $vote->date = date('Y-m-d H:i:s');
  $vote->save();


  $res->status(202);
  $res['Content-Type'] = 'application/json';
  $res->body(json_encode(array(
    'result' => 'WebMention was successful',
    'source' => $req->post('source'),
    'target' => $req->post('target')
  )));
});

$app->post('/webmention-error', function() use($app) {

  $req = $app->request();
  $res = $app->response();

  $res->status(400);
  $res['Content-Type'] = 'application/json';
  $res->body(json_encode(array(
    'error' => 'no_link_found'
  )));
});


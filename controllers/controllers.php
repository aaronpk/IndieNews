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

// Log in with IndieAuth
$app->get('/indieauth', function() use($app) {

  $req = $app->request();
  $params = $req->params();

  if(array_key_exists('token', $params)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://indieauth.com/session?token=' . $params['token']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $auth = json_decode(curl_exec($ch));
    if($auth) {
      $_SESSION['user'] = preg_replace('|https?://|', '', $auth->me);

      // Create the user record if it doesn't yet exist
      $user = ORM::for_table('users')->where('domain', session('user'))->find_one();

      if($user == FALSE) {
        $user = ORM::for_table('users')->create();
        $user->domain = session('user');
        $user->date_created = date('Y-m-d H:i:s');
        $user->save();
      }

    }
    $app->redirect('/', 301);
  }

  $app->redirect('/', 301);
});

$app->get('/signout', function() use($app) {
  unset($_SESSION['user']);
  $app->redirect('/', 301);
});

$app->post('/vote', function() use($app) {

  $req = $app->request();
  $params = $req->params();
  $res = $app->response();

  $id = false;

  if($user=getLoggedInUser()) {

    // Ensure they haven't already voted
    $existing = ORM::for_table('votes')->where('post_id', $params['id'])->where('user_id', $user->id)->find_one();
    if(!$existing) {
      $vote = ORM::for_table('votes')->create();
      $vote->post_id = $params['id'];
      $vote->user_id = $user->id;
      $vote->date = date('Y-m-d H:i:s');
      $vote->save();

      $result = 'ok';
      $id = $params['id'];
    } else {
      $result = 'already_voted';
      $id = $params['id'];
    }

  } else {
    $result = 'not_logged_in';
  }

  $res['Content-Type'] = 'application/json';
  $res->body(json_encode(array(
    'result' => $result,
    'id' => $id
  )));  
});

/*
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
*/

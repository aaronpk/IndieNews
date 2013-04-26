<?php

function getUserVotesForPosts(&$posts) {
  // If the user is logged in, retrieve all their votes for these articles
  $votes = array();

  if($user=getLoggedInUser()) {
    $ids = array();
    foreach($posts as $i=>$post) {
      $ids[] = $post->id;
    }
    $results = ORM::for_table('votes')->where('user_id', $user->id)->where_in('post_id', $ids)->find_many();
    foreach($results as $r) {
      $votes[] = $r->post_id;
    }
  }

  return $votes;
}

// Home Page
$app->get('/', function() use($app) {

  $req = $app->request();

  // Check if we need to recalculate the scores yet (only calculate once per hour)

  $last = ORM::for_table('properties')->where('key', 'last_computed')->find_one();
  if($last == false || (time() - strtotime($last['val'])) > 3600) {
    $posts = ORM::for_table('posts')->raw_execute('
      UPDATE posts SET score = (points-1) / POWER(GREATEST(1, TIMESTAMPDIFF(HOUR, date_submitted, NOW())), 1.8)
    ');
    if($last == false) {
      $last = ORM::for_table('properties')->create();
      $last->key = 'last_computed';
    }
    $last->val = date('Y-m-d H:i:s');
    $last->save();
  }

  $posts = ORM::for_table('posts')->raw_query('
    SELECT *, GREATEST(1, TIMESTAMPDIFF(HOUR, date_submitted, NOW())) AS age
    FROM posts
    ORDER BY score DESC, points DESC, date_submitted DESC
    LIMIT 20
  ')->find_many();
  $votes = getUserVotesForPosts($posts);

  render('posts', array(
    'title' => 'IndieNews - Front Page',
    'posts' => $posts,
    'votes' => $votes,
    'meta' => ''
  ));
});

// Newest
$app->get('/newest', function() use($app) {

  $req = $app->request();

  $posts = ORM::for_table('posts')->where('parent_id', 0)->order_by_desc('date_submitted')->limit(20)->find_many();
  $votes = getUserVotesForPosts($posts);

  render('posts', array(
    'title' => 'IndieNews - Newest Submissions',
    'posts' => $posts,
    'votes' => $votes,
    'meta' => ''
  ));
});

// Single Post Page
$app->get('/post/:id', function($id) use($app) {

  $post = ORM::for_table('posts')->where('id', $id)->find_one();
  $posts = array($post);
  $votes = getUserVotesForPosts($posts);

  if(!$post) {
    $app->pass(); // Will trigger a 404 error
  }

  render('post', array(
    'title' => $post->title,
    'post' => $post,
    'votes' => $votes,
    'meta' => ''
  ));

})->conditions(array('id'=>'\d+'));

$app->get('/submit', function() use($app) {
  render('submit', array(
    'title' => 'IndieNews - Submit a post',
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
  $points = false;

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

      $points = recalculatePoints($params['id']);
      $points = $points . ' point' . ($points == 1 ? '' : 's');
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
    'id' => $id,
    'points' => $points
  )));  
});



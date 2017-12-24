<?php
$app->get('/', function() use($app) {
  render('index', array(
    'title' => 'IndieNews homepage',
    'meta' => '',
    'lang' => 'en'
  ));
});

$app->get('/how-to-submit-a-post', function() use($app) {
  render('submit-full', array(
    'title' => 'IndieNews - How to submit a post',
    'meta' => '',
    'lang' => 'en'
  ));
});

$app->get('/how', function() use($app) {
  $app->redirect('/how-to-submit-a-post', 301);
});

$app->get('/how-to-comment', function() use($app) {
  $app->redirect('/how-to-submit-a-post', 301);
});

$app->get('/constructing-post-urls', function() use($app) {
  $app->redirect('/how-to-submit-a-post', 301);
});

// Log in with IndieAuth
$app->get('/indieauth', function() use($app) {

  $req = $app->request();
  $params = $req->params();

  if(array_key_exists('token', $params)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://indieauth.com/verify?code=' . $params['token']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $auth = json_decode(curl_exec($ch));
    if($auth) {
      $_SESSION['user'] = $auth->me;

      // Create the user record if it doesn't yet exist
      $user = ORM::for_table('users')->where('url', session('user'))->find_one();

      if($user == FALSE) {
        $user = ORM::for_table('users')->create();
        $user->url = session('user');
        $user->date_created = date('Y-m-d H:i:s');
        $user->save();
      }

    }
    $app->redirect('/?error=verify_failed', 301);
  }

  $app->redirect('/?error=no_code', 301);
});

$app->get('/signout', function() use($app) {
  unset($_SESSION['user']);
  $app->redirect('/', 301);
});

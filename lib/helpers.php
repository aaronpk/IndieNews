<?php

function render($page, $data) {
  global $app;
  return $app->render('layout.php', array_merge($data, array('page' => $page)));
};

function partial($template, $data, $debug=false) {
  global $app;

  if($debug) {
    $tpl = new Savant3(\Slim\Extras\Views\Savant::$savantOptions);
    echo '<pre>' . $tpl->fetch($template . '.php') . '</pre>';
    return '';
  }

  ob_start();
  $tpl = new Savant3(\Slim\Extras\Views\Savant::$savantOptions);
  foreach($data as $k=>$v) {
    $tpl->{$k} = $v;
  }
  $tpl->display($template . '.php');
  return ob_get_clean();
}

function session($key) {
  if(array_key_exists($key, $_SESSION))
    return $_SESSION[$key];
  else
    return null;
}

function getLoggedInUser() {
  if(session('user')) {
    return ORM::for_table('users')->where('domain', session('user'))->find_one();
  } else {
    return false;
  }
}

function recalculatePoints($postID) {
  $count = ORM::for_table('votes')->where('post_id', $postID)->count();
  $post = ORM::for_table('posts')->find_one($postID);
  $post->points = $count;
  $post->save();
  return $count;
}

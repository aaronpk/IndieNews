<?php
use Cake\I18n\I18n;
use Slim\Views\PhpRenderer;

// Redirect old feed URLs
$app->redirect('/home.json', '/en.json', 301);
$app->redirect('/newest.json', '/en.json', 301);

// Redirect post IDs to the post URL version
$app->get('/post/{id:[0-9]+}{format:|\.json}', function($request, $response, $args) {
  $post = ORM::for_table('posts')
    ->where('id', $args['id'])
    ->where('deleted', 0)
    ->find_one();

  if(!$post)
    return $response->withStatus(404);

  $url = Config::$baseURL . '/post/'. slugForURL($post->href) . ($args['format']);

  return $response
    ->withHeader('Location', $url)
    ->withStatus(302);
});

// Language-specific feeds
$app->get('/{lang:'.LANG_REGEX.'}{format:|\.json|\.jf2}', function($request, $response, $args) {

  $params = $request->getQueryParams();

  $format = formatFromRouteArgs($args);

  I18n::setLocale($args['lang']);

  // Get posts ordered by date submitted
  $posts = ORM::for_table('posts')
    ->where('lang', $args['lang'])
    ->where('deleted', 0)
    ->order_by_desc('date_submitted');

  if(array_key_exists('before', $params)) {
    $before = date('Y-m-d H:i:s', b60to10($params['before']));
    $posts = $posts->where_lt('date_submitted', $before);
  }

  $posts = $posts->limit(20)->find_many();

  $atomFeed = '<link rel="alternate" type="application/atom+xml" href="https://granary-demo.appspot.com/url?input=html&output=atom&url=' . urlencode(Config::$baseURL . '/' . $args['lang']) . '">';
  $webSubTags = '<link rel="hub" href="' . Config::$hubURL . '">' . "\n" . '<link rel="self" href="' . Config::$baseURL . '/' . $args['lang'] . '">';

  $renderer = new PhpRenderer(__DIR__.'/../views/');
  $renderer->setLayout('layout.php');

  $temp = $renderer->render(new \Slim\Psr7\Response(), "posts.php", [
    'title' => 'IndieNews ' . $args['lang'],
    'posts' => $posts,
    'lang' => $args['lang'],
    'meta' => $atomFeed.$webSubTags,
  ]);

  $html = $temp->getBody()->__toString();

  $response = $response->withAddedHeader('Link', '<' . Config::$baseURL . '/'.$args['lang'].'/webmention>; rel="webmention"');
  $response = $response->withAddedHeader('Link', '<' . Config::$baseURL . '/'.$args['lang'].'>; rel="self"');
  $response = $response->withAddedHeader('Link', '<' . Config::$hubURL . '>; rel="hub"');

  return respondWithFormat($response, $html, $format);
});


$app->get('/{lang:'.LANG_REGEX.'}/{year:\d{4}}/{month:\d{2}}', function($request, $response, $args) {

  $year = $args['year'];
  $month = $args['month'];
  $lang = $args['lang'];

  I18n::setLocale($args['lang']);
  setlocale(LC_ALL, localeFromLangCode($args['lang']));

  $date = new DateTime($year.'-'.$month.'-01');

  $posts = ORM::for_table('posts')
    ->where('lang', $lang)
    ->where('deleted', 0)
    ->where_lte('date_submitted', $date->format('Y-m-t').' 23:59:59')
    ->where_gte('date_submitted', $date->format('Y-m-01'))
    ->order_by_asc('date_submitted')
    ->find_many();

  $prev = false;
  $next = false;

  $prevPost = ORM::for_table('posts')
    ->where('lang', $lang)
    ->where('deleted', 0)
    ->where_lt('date_submitted', $date->format('Y-m-01'))
    ->order_by_desc('date_submitted')
    ->find_one();
  if($prevPost) {
    $prev = new DateTime($prevPost->date_submitted);
  }

  $nextPost = ORM::for_table('posts')
    ->where('lang', $lang)
    ->where('deleted', 0)
    ->where_gt('date_submitted', $date->format('Y-m-t').' 23:59:59')
    ->order_by_asc('date_submitted')
    ->find_one();
  if($nextPost) {
    $next = new DateTime($nextPost->date_submitted);
  }

  $calendar = [];
  foreach($posts as $post) {
    $postDate = $post->post_date ?: $post->date_submitted;
    $day = printLocalDate('j', $postDate, $post->tzoffset);
    if(!array_key_exists($day, $calendar))
      $calendar[(int)$day] = [];
    $calendar[(int)$day][] = $post;
  }
  ksort($calendar);

  return render($response, 'calendar', [
    'title' => 'IndieNews ' . $lang,
    'date' => $date,
    'year' => $year,
    'month' => $month,
    'calendar' => $calendar,
    'meta' => '',
    'lang' => $lang,
    'next' => $next,
    'prev' => $prev
  ]);
});




// Language-specific submit instructions
$app->get('/{lang:'.LANG_REGEX.'}/submit', function($request, $response, $args) {
  I18n::setLocale($args['lang']);

  return render($response, 'submit', array(
    'title' => __('About IndieNews'),
    'meta' => '',
    'lang' => $args['lang']
  ));
});


$app->get('/{lang:'.LANG_REGEX.'}/members', function($request, $response, $args) {
  I18n::setLocale($args['lang']);

  $users = ORM::for_table('users')
    ->select('users.*')
    ->select_expr('COUNT(posts.id) AS num_posts')
    ->join('posts', ['posts.user_id', '=', 'users.id'])
    ->where('posts.lang', $args['lang'])
    ->where('posts.deleted', 0)
    ->where_gt('posts.date_submitted', date('Y-m-d H:i:s', strtotime('1 year ago')))
    ->group_by('users.id')
    ->order_by_desc('num_posts')
    ->find_many();

  return render($response, 'members', array(
    'title' => __('IndieNews Members'),
    'meta' => '',
    'lang' => $args['lang'],
    'users' => $users
  ));
});


// Language-specific permalinks
$app->get('/{lang:'.LANG_REGEX.'}/{slug:.*?}{format:|\.json|\.jf2}', function($request, $response, $args) {
  $format = formatFromRouteArgs($args);
  $slug = $args['slug'];

  I18n::setLocale($args['lang']);

  $post = ORM::for_table('posts')->where_in('href', array('http://'.$slug,'https://'.$slug))->find_one();
  $posts = array($post);

  $response = $response->withAddedHeader('Link', '<' . Config::$baseURL . '/'.$args['lang'].'/webmention>; rel="webmention"');

  if(!$post) {
    return $response->withStatus(404);
  }

  $renderer = new PhpRenderer(__DIR__.'/../views/');
  $renderer->setLayout('layout.php');

  $temp = $renderer->render(new \Slim\Psr7\Response(), "post.php", [
    'title' => $post->title,
    'post' => $post,
    'view' => 'single',
    'meta' => '',
    'lang' => $args['lang']
  ]);

  $html = $temp->getBody()->__toString();

  return respondWithFormat($response, $html, $format);
});

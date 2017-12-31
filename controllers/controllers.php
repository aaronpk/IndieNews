<?php
use Cake\I18n\I18n;

// Redirect old feed URLs
$app->get('/:path(.:format)', function($path, $format='html') use($app) {
  $app->redirect(Config::$baseURL . '/en'.($format == 'json' ? '.json' : ''), 301);
})->conditions(array('format'=>'json','path'=>'(home|newest)'));

// Redirect post IDs to the post URL version
$app->get('/post/:id(.:format)', function($id, $format='html') use($app) {
  $post = ORM::for_table('posts')->where('id', $id)->find_one();

  if(!$post) {
    $app->pass(); // Will trigger a 404 error
  }

  $app->redirect(Config::$baseURL . '/post/' . slugForURL($post->href) . ($format == 'html' ? '' : '.'.$format), 302);
})->conditions(array('id'=>'\d+', 'format'=>'json'));

// Redirect old "/post/" permalinks
$app->get('/post/:slug', function($slug) use($app) {
  $app->redirect(Config::$baseURL . '/en/'.$slug, 301);
})->conditions(array('slug'=>'.+\..+?'));

// Language-specific feeds
$app->get('/:lang(.:format)', function($lang='en', $format='html') use($app) {

  I18n::locale($lang);

  $req = $app->request();

  $res = $app->response();
  $res['X-Pingback'] = 'https://webmention.io/webmention?forward=' . Config::$baseURL . '/'.$lang.'/webmention';
  $res['Link'] = '<' . Config::$baseURL . '/'.$lang.'/webmention>; rel="webmention"' . "\n"
    . '<' . Config::$baseURL . '/'.$lang.'>; rel="self"' . "\n"
   . '<' . Config::$hubURL . '>; rel="hub"';

  // Get posts ordered by date submitted
  $posts = ORM::for_table('posts')
    ->where('lang', $lang)
    ->order_by_desc('date_submitted');

  if(array_key_exists('before', $req->params())) {
    $before = date('Y-m-d H:i:s', b60to10($req->params()['before']));
    $posts = $posts->where_lt('date_submitted', $before);
  }

  $posts = $posts->limit(20)->find_many();

  $atomFeed = '<link rel="alternate" type="application/atom+xml" href="https://granary-demo.appspot.com/url?input=html&output=atom&url=' . urlencode(Config::$baseURL . '/' . $lang) . '">';
  $webSubTags = '<link rel="hub" href="' . Config::$hubURL . '">' . "\n" . '<link rel="self" href="' . Config::$baseURL . '/' . $lang . '">';

  ob_start();
  render('posts', array(
    'title' => 'IndieNews ' . $lang,
    'posts' => $posts,
    'view' => 'list',
    'meta' => (Config::$hubURL ? $webSubTags : '') . "\n" . $atomFeed,
    'lang' => $lang
  ));
  $html = ob_get_clean();
  respondWithFormat($app, $html, $format);
})->conditions(array('format'=>'json|jf2', 'lang'=>LANG_REGEX));

$app->get('/:lang/:year/:month', function($lang='en', $year, $month) use($app) {

  I18n::locale($lang);
  setlocale(LC_ALL, localeFromLangCode($lang));

  $req = $app->request();

  $date = new DateTime($year.'-'.$month.'-01');

  $posts = ORM::for_table('posts')
    ->where('lang', $lang)
    ->where_lte('date_submitted', $date->format('Y-m-t').' 23:59:59')
    ->where_gte('date_submitted', $date->format('Y-m-01'))
    ->order_by_asc('date_submitted')
    ->find_many();

  $prev = false;
  $next = false;

  $prevPost = ORM::for_table('posts')
    ->where('lang', $lang)
    ->where_lt('date_submitted', $date->format('Y-m-01'))
    ->order_by_desc('date_submitted')
    ->find_one();
  if($prevPost) {
    $prev = new DateTime($prevPost->date_submitted);
  }

  $nextPost = ORM::for_table('posts')
    ->where('lang', $lang)
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

  ob_start();
  render('calendar', array(
    'title' => 'IndieNews ' . $lang,
    'date' => $date,
    'year' => $year,
    'month' => $month,
    'calendar' => $calendar,
    'meta' => '',
    'lang' => $lang,
    'next' => $next,
    'prev' => $prev
  ));
  $html = ob_get_clean();
  respondWithFormat($app, $html, 'html');
})->conditions(array('lang'=>LANG_REGEX, 'year'=>'\d{4}', 'month'=>'\d{2}'));

// Language-specific permalinks
$app->get('/:lang/:slug(.:format)', function($lang, $slug, $format='html') use($app) {
  I18n::locale($lang);

  $post = ORM::for_table('posts')->where_in('href', array('http://'.$slug,'https://'.$slug))->find_one();
  $posts = array($post);

  $res = $app->response();
  $res['X-Pingback'] = 'https://webmention.io/webmention?forward=' . Config::$baseURL . '/' . $lang . '/webmention';
  $res['Link'] = '<' . Config::$baseURL . '/'.$lang.'/webmention>; rel="webmention"';

  if(!$post) {
    $app->pass(); // Will trigger a 404 error
  }

  ob_start();
  render('post', array(
    'title' => $post->title,
    'post' => $post,
    'view' => 'single',
    'meta' => '',
    'lang' => $lang
  ));
  $html = ob_get_clean();
  respondWithFormat($app, $html, $format);
})->conditions(array('lang'=>LANG_REGEX, 'slug'=>'.+\..+?', 'format'=>'json'));

// Language-specific submit instructions
$app->get('/:lang/submit', function($lang) use($app) {
  I18n::locale($lang);

  render('submit', array(
    'title' => __('About IndieNews'),
    'meta' => '',
    'lang' => $lang
  ));
})->conditions(array('lang'=>LANG_REGEX));

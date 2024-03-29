<?php
use Cake\Core\Configure;
use Slim\Views\PhpRenderer;

Configure::write('App.paths.locales', [__DIR__.'/../resources/locales/']);

require_once(__DIR__.'/../vendor/cakephp/i18n/functions_global.php');

define('LANG_REGEX', 'en|sv|de|fr|nl|ru|es');
error_reporting(E_ALL ^ E_DEPRECATED);

function supportedLanguages() {
  return [
    'en' => 'English',
    'sv' => 'Svenska',
    'de' => 'Deutsch',
    'fr' => 'Français',
    'nl' => 'Nederlands',
    'ru' => 'русский',
    'es' => 'Español',
  ];
}

function localeFromLangCode($code) {
  switch($code) {
    case 'en':
      return 'en_US';
    case 'sv':
      return 'sv_SE.UTF-8';
    case 'de':
      return 'de_DE.UTF-8';
    case 'fr':
      return 'fr_FR.UTF-8';
    case 'nl':
      return 'nl_NL.UTF-8';
    case 'ru':
      return 'ru_RU.UTF-8';
    case 'es':
      return 'es_MX.UTF-8';
  }
}

function getPostsForParentID($parentID) {
  return ORM::for_table('posts')->raw_query('
    SELECT *, GREATEST(1, TIMESTAMPDIFF(HOUR, date_submitted, NOW())) AS age
    FROM posts
    ORDER BY date_submitted DESC
  ')->find_many();
}

function respondWithFormat($response, $html, $format) {
  if($format == 'json') {
    $parser = new mf2\Parser($html);
    $output = $parser->parse();
    $output['note'][] = "This JSON is automatically generated by parsing the microformats from the HTML representation of this page using the php-mf2 library.";
    $output['note'][] = "You can find the php-mf2 library at github.com/microformats/php-mf2";
    $output['note'][] = "If you see a problem with the output, please let me know! (github.com/aaronpk/IndieNews/issues)";
    $response->getBody()->write(json_encode($output));
    return $response->withHeader('Content-Type', 'application/json');
  } elseif($format == 'jf2') {
    $xray = new p3k\XRay();
    $parsed = $xray->parse(Config::$baseURL, $html);
    $response->getBody()->write(json_encode($parsed));
    return $response->withHeader('Content-Type', 'application/json');
  } else {
    $response->getBody()->write($html);
    return $response;
  }
}

// $format - one of the php.net/date format strings
// $date - a string that will be passed to DateTime()
// $offset - numeric timezone offset
function printLocalDate($format, $date, $offset) {
  if($offset != 0)
    $tz = new DateTimeZone(($offset < 0 ? '-' : '+') . sprintf('%02d:%02d', abs(floor($offset / 60 / 60)), (($offset / 60) % 60)));
  else
    $tz = new DateTimeZone('UTC');
  $d = new DateTime($date);
  $d->setTimeZone($tz);
  return $d->format($format);
}

function formatFromRouteArgs($args) {
  return empty($args['format']) ? 'html' : trim($args['format'], '.');
}

// Strip the http:// prefix
function slugForURL($url) {
  return preg_replace('/https?:\/\//', '', $url);
}

function permalinkForURL($lang, $url) {
  return Config::$baseURL . '/' . $lang . '/' . slugForURL($url);
}

function shouldDisplayPostName($name) {
  if(!$name) return false;
  $name = str_replace('http://','https://',$name);
  return strlen($name) < 200                  # must be less than 200 chars
    && substr_count($name, "\n") <= 1         # must be less than 2 lines
    && substr_count($name, "https://") <= 1;  # must have at most 1 URL
}

function render($response, $page, $data) {
  $renderer = new PhpRenderer(__DIR__.'/../views/');
  $renderer->setLayout('layout.php');
  return $renderer->render($response, "$page.php", $data);
};

function session($key) {
  if(array_key_exists($key, $_SESSION))
    return $_SESSION[$key];
  else
    return null;
}

function getLoggedInUser() {
  if(session('user')) {
    return ORM::for_table('users')->where('url', session('user'))->find_one();
  } else {
    return false;
  }
}

function display_url($url) {
  return preg_replace(['/https?:\/\//','/\/$/'],'',$url);
}

function pa($a) {
  echo '<pre>'; print_r($a); echo '</pre>';
}

function irc_notice($msg) {
  if(isset(Config::$ircURL)) {
    $ch = curl_init(Config::$ircURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Authorization: Bearer ' . Config::$ircToken
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
      'content' => $msg,
      'channel' => Config::$ircChannel
    )));
    curl_exec($ch);
  }
}

/**
 * Converts base 10 to base 60.
 * http://tantek.pbworks.com/NewBase60
 * @param int $n
 * @return string
 */
function b10to60($n)
{
  $s = "";
  $m = "0123456789ABCDEFGHJKLMNPQRSTUVWXYZ_abcdefghijkmnopqrstuvwxyz";
  if ($n==0)
    return 0;

  while ($n>0)
  {
    $d = $n % 60;
    $s = $m[$d] . $s;
    $n = ($n-$d)/60;
  }
  return $s;
}

/**
 * Converts base 60 to base 10, with error checking
 * http://tantek.pbworks.com/NewBase60
 * @param string $s
 * @return int
 */
function b60to10($s)
{
  $n = 0;
  for($i = 0; $i < strlen($s); $i++) // iterate from first to last char of $s
  {
    $c = ord($s[$i]); //  put current ASCII of char into $c
    if ($c>=48 && $c<=57) { $c=$c-48; }
    else if ($c>=65 && $c<=72) { $c-=55; }
    else if ($c==73 || $c==108) { $c=1; } // typo capital I, lowercase l to 1
    else if ($c>=74 && $c<=78) { $c-=56; }
    else if ($c==79) { $c=0; } // error correct typo capital O to 0
    else if ($c>=80 && $c<=90) { $c-=57; }
    else if ($c==95) { $c=34; } // underscore
    else if ($c>=97 && $c<=107) { $c-=62; }
    else if ($c>=109 && $c<=122) { $c-=63; }
    else { $c = 0; } // treat all other noise as 0
    $n = (60 * $n) + $c;
  }
  return $n;
}

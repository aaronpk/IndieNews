<?php
use Cake\I18n\I18n;

function renderBlocks($response, $lang, $error=null, $form=[]) {
  $blocks = ORM::for_table('blocks')
    ->order_by_desc('date_created')
    ->order_by_desc('id')
    ->find_many();

  return render($response, 'blocks', [
    'title' => __('Blocklist'),
    'blocks' => $blocks,
    'error' => $error,
    'form' => $form,
    'lang' => $lang,
    'meta' => '',
  ]);
}

$app->get('/{lang:'.LANG_REGEX.'}/blocks', function($request, $response, $args) {
  if(!isAdmin()) {
    return $response->withStatus(403);
  }

  I18n::setLocale($args['lang']);

  return renderBlocks($response, $args['lang']);
});

$app->post('/{lang:'.LANG_REGEX.'}/blocks', function($request, $response, $args) {
  if(!isAdmin()) {
    return $response->withStatus(403);
  }

  I18n::setLocale($args['lang']);

  $params = (array)$request->getParsedBody();
  $domain = normalizeBlockDomain($params['domain'] ?? '');
  $sourceURL = trim($params['source_url'] ?? '');
  $note = trim($params['note'] ?? '');

  if(($domain == '') == ($sourceURL == '')) {
    return renderBlocks($response->withStatus(400), $args['lang'],
      __('Enter either a domain or a source URL to block.'), $params);
  }

  if($domain != '' && !preg_match('/^[a-z0-9-]+(\.[a-z0-9-]+)*$/', $domain)) {
    return renderBlocks($response->withStatus(400), $args['lang'],
      __('That does not look like a valid domain.'), $params);
  }

  if($sourceURL != '' && !preg_match('/^https?:\/\/[^\/]+/i', $sourceURL)) {
    return renderBlocks($response->withStatus(400), $args['lang'],
      __('The source URL must be an http or https URL.'), $params);
  }

  $existing = ORM::for_table('blocks')
    ->where($domain != '' ? 'domain' : 'source_url', $domain != '' ? $domain : $sourceURL)
    ->find_one();

  if(!$existing) {
    $block = ORM::for_table('blocks')->create();
    $block->domain = $domain != '' ? $domain : null;
    $block->source_url = $sourceURL != '' ? $sourceURL : null;
    $block->note = $note != '' ? $note : null;
    $block->date_created = date('Y-m-d H:i:s');
    $block->save();
  }

  return $response
    ->withHeader('Location', Config::$baseURL . '/' . $args['lang'] . '/blocks')
    ->withStatus(302);
});

$app->post('/{lang:'.LANG_REGEX.'}/blocks/{id:[0-9]+}/delete', function($request, $response, $args) {
  if(!isAdmin()) {
    return $response->withStatus(403);
  }

  $block = ORM::for_table('blocks')
    ->where('id', $args['id'])
    ->find_one();

  if(!$block) {
    return $response->withStatus(404);
  }

  $block->delete();

  return $response
    ->withHeader('Location', Config::$baseURL . '/' . $args['lang'] . '/blocks')
    ->withStatus(302);
});

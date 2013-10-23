<?php

$app->get('/parse', function() use($app) {

  $req = $app->request();
  $res = $app->response();

  $sourceURL = $req->get('source');

  $error = function($res, $err, $description=false) {
    $res->status(400);
    $res['Content-Type'] = 'application/json';
    $error = array(
      'error' => $err
    );
    if($description)
      $error['error_description'] = $description;
    $res->body(json_encode($error));
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

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $sourceURL);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $html = curl_exec($ch);

  $parser = new ParserPlus($html);
  $output = $parser->parse();
  $page = new MF2Page($output);

  $data = array(
    'name' => null,
    'content' => null,
    'published' => null,
    'published_ts' => null,
    'author' => array(
      'name' => null,
      'url' => null,
      'photo' => null
    )
  );

  if($page->hentry) {

    $data['mf2'] = $output;

    if($page->hentry->property('name'))
      $data['name'] = trim($page->hentry->property('name', true));

    if($page->hentry->author) {
      if($page->hentry->author->url)
        $data['author']['url'] = $page->hentry->author->url;
      if($page->hentry->author->name)
        $data['author']['name'] = $page->hentry->author->name;
      if($page->hentry->author->photo)
        $data['author']['photo'] = $page->hentry->author->photo;
    }

    if($page->hentry->property('content'))
      $data['content'] = strip_tags(trim(implode("\n", $page->hentry->property('content'))));

    $entry = $page->hentry;
  } elseif($page->hevent) {
    $data['mf2'] = $output;

    if($page->hevent->author) {
      if($page->hevent->author->url)
        $data['author']['url'] = $page->hevent->author->url;
      if($page->hevent->author->name)
        $data['author']['name'] = $page->hevent->author->name;
      if($page->hevent->author->photo)
        $data['author']['photo'] = $page->hevent->author->photo;
    }

    if($page->hevent->property('name')) 
      $data['name'] = trim($page->hevent->property('name', true));
    
  } else {
    $entry = false;
  }

  if($page->hentry && ($published=$page->hentry->published)) {
    $data['published'] = $published->format('c');
    $data['published_ts'] = (int)$published->format('U');
  }

  if($page->hevent && ($published=$page->hevent->published)) {
    $data['published'] = $published->format('c');
    $data['published_ts'] = (int)$published->format('U');
  }

  if($data['name'] == $data['content'])
    $data['name'] = null;

  $res->status(200);
  $res['Content-Type'] = 'application/json';
  $res->body(json_encode($data));

});

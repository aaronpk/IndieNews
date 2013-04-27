<?php

$app->post('/webmention', function() use($app) {

  $req = $app->request();
  $res = $app->response();

  $sourceURL = $req->post('source');
  $targetURL = $req->post('target');

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

  if(preg_match('/\/post\/([0-9]+)$/', $targetURL, $match)) {
    $parentID = $match[1];
  } else {
    $parentID = 0;
  }

  $data = array(
    'domain' => $source['host'],
    'title' => false,
    'body' => false,
    'date' => false
  );
  $notices = array();

  # Now fetch and parse the page looking for Microformats
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $sourceURL);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $html = curl_exec($ch);

  $parser = new ParserPlus($html);
  $output = $parser->parse();
  $page = new MF2Page($output);

  if($page->hentry) {

    if($page->hentry->property('name'))
      $data['title'] = trim($page->hentry->property('name', true));
    else
      $notices[] = 'No "name" property found on the h-entry. Using the page title instead.';

    if($page->hentry->author && $page->hentry->author->url) {
      $authorURL = parse_url($page->hentry->author->url);
      $data['domain'] = $authorURL['host'];
    } else {
      // $error($res, 'no_author', 'No author was found for the h-entry');
      $notices[] = 'No author URL was found for the h-entry. Using the domain name instead.';
    }

    if($page->hentry->property('content'))
      $data['body'] = strip_tags(trim(implode("\n", $page->hentry->property('content'))));
    else
      $notices[] = 'No post content was found in the h-entry.';

  } elseif($page->hevent) {

    if($page->hevent->property('name')) {
      $data['title'] = trim($page->hevent->property('name', true));
    }
    if($locations=$page->hevent->location) {
      $location = $locations[0];
      $data['title'] .= ' at ' . $location->property('name', true);
    }
    
  } else {
    // $error($res, 'no_hentry', 'No h-entry was found on the page');
    $notices[] = 'No h-entry was found on the page. Using the page title for the name' . ($parentID > 0 ? ', and no comment body will be imported.' : '.');
  }

  if($page->hentry && ($published=$page->hentry->published)) {
    $data['date'] = $published->format('U');
  } else {
    $notices[] = 'No publish date found.';
  }

  # If no h-entry was found, or if didn't find the title, look at the page title
  if($data['title'] == false) {
    $pageTitle = $parser->xpath('./*/title');
    if($pageTitle->length > 0) {
      foreach($pageTitle as $t) 
        $data['title'] = $t->textContent;
    }
  }

  // print_r($notices);
  // print_r($data);
  // die();

  # Verify the $source actually contains a link to $target
  // if(FALSE) {
  //   $error($res, 'no_link_found');
  //   return;
  // }

  # Get the domain of $source and find or create a user account
  $user = ORM::for_table('users')->where('domain', $data['domain'])->find_one();

  if($user == FALSE) {
    $user = ORM::for_table('users')->create();
    $user->domain = $data['domain'];
    $user->date_created = date('Y-m-d H:i:s');
    $user->save();
  }

  # If there is no existing post for $source, update the properties
  $post = ORM::for_table('posts')->where('href', $sourceURL)->find_one();
  if($post != FALSE) {
    if($data['date'])
      $post->post_date = date('Y-m-d H:i:s', $data['date']);
    $post->domain = $data['domain'];
    $post->title = $data['title'];
    if($data['body'])
      $post->body = $data['body'];
    $post->save();
    $notices[] = 'Already registered, updating properties of the post.';
  } else {
    # Record a new post and a vote from the domain
    $post = ORM::for_table('posts')->create();
    $post->user_id = $user->id;
    $post->date_submitted = date('Y-m-d H:i:s');
    if($data['date'])
      $post->post_date = date('Y-m-d H:i:s', $data['date']);
    $post->domain = $data['domain'];
    $post->title = $data['title'];
    if($data['body'])
      $post->body = $data['body'];
    $post->href = $sourceURL;
    $post->points = 1;
    $post->parent_id = $parentID;
    $post->save();

    $vote = ORM::for_table('votes')->create();
    $vote->post_id = $post->id;
    $vote->user_id = $user->id;
    $vote->date = date('Y-m-d H:i:s');
    $vote->save();
  }

  $res->status(202);
  $res['Content-Type'] = 'application/json';
  $res->body(json_encode(array(
    'result' => 'success',
    'notices' => $notices,
    'data' => array(
      'title' => $data['title'],
      'body' => $data['body'],
      'author' => $data['domain'],
      'date' => ($data['date'] ? date('Y-m-d\TH:i:sP', $data['date']) : false)
    ),
    'source' => $req->post('source'),
    'target' => $req->post('target'),
    'href' => 'http://' . $_SERVER['SERVER_NAME'] . '/post/' . $post->id
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

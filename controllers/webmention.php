<?php

$app->post('/(:lang/)webmention', function($lang='en') use($app) {

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

  if($sourceURL == FALSE) {
    $error($res, 'missing_source_url');
    return;
  }
  
  $source = parse_url($sourceURL);

  # Verify $source is valid
  if($source == FALSE
    || !array_key_exists('scheme', $source)
    || !in_array($source['scheme'], array('http','https'))
    || !array_key_exists('host', $source)
    || ($source['host'] == gethostbyname($source['host']))
  ) {
    $error($res, 'invalid_source_url');
    return;
  }

  if($targetURL == FALSE) {
    $error($res, 'missing_target_url');
    return;
  }

  # Verify $target is actually a resource under our control (home page, individual post)
  $target = parse_url($targetURL);

  # Verify $source is valid
  if($target == FALSE
    || !array_key_exists('scheme', $target)
    || !in_array($target['scheme'], array('http','https'))
    || !array_key_exists('host', $target)
    || $target['host'] != Config::$hostname
  ) {
    $error($res, 'target_not_supported');
    return;
  }

  if(!preg_match('/^https?:\/\/' . Config::$hostname . '(?:\/'.LANG_REGEX.')?\/?/', $targetURL, $match)) {
    $error($res, 'target_not_supported', 'The target you specified does not appear to be a URL on this site.');
    return;
  }

  // Parse the language from the target URL, so that the story ends up on the specified
  // feed regardless of which endpoint it was sent to.
  // If no lang was sent in the target param (like if they just linked to the indienews home page),
  // then use the language specified by the webmention endpoint.
  if(array_key_exists(1, $match))
    $lang = $match[1];

  $record = array(
    'post_author' => $source['scheme'].'://'.$source['host'],
    'title' => false,
    'body' => false,
    'date' => false
  );
  $notices = array();

  // $error($res, 'ok_so_far', '');
  // return;

  # Now fetch and parse the page looking for Microformats
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, Config::$xrayURL);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'url' => $sourceURL
  ]));
  $response = json_decode(curl_exec($ch), true);

  if(isset($response['error'])) {
    $error($res, $response['error'], 'An error occurred while attempting to fetch the source URL: ' . $response['error_description']);
    return;
  }

  $post = $response['data'];
  if(isset($response['refs']))
    $refs = $response['refs'];
  else
    $refs = [];

  if(!isset($post['type']) || !in_array($post['type'], ['entry','event'])) {
    $error($res, 'no_link_found', 'No h-entry or h-event was found on the page, so we were unable to find a u-syndication or u-category URL. If you have multiple top-level h-* objects, ensure that one of them has a u-url property set to the URL of the page.');
    return;
  }

  $authorURL = false;

  if($post['type'] == 'entry') {

    if(isset($post['name'])) {
      $record['title'] = $post['name'];
    } else {
      $notices[] = 'No "name" property found on the h-entry.';
    }

    if(isset($post['content'])) {
      $record['body'] = $post['content']['text'];
    }

    if(array_key_exists('published', $post)) {
      $published = new DateTime($post['published']);
      $record['date'] = $published->format('U');
    } else {
      $notices[] = 'No published date found';
    }


  } elseif($post['type'] == 'event') {
    if(isset($post['name'])) {
      $record['title'] = $post['name'];
    } else {
      $notices[] = 'No "name" was found for this h-event';
    }

    if(isset($post['start'])) {
      $start = new DateTime($post['start']);
      if($start) {
        $record['date'] = $start->format('U');
      }
    }

    if($locations=$post['location']) {
      $locationURL = $locations[0];
      if(array_key_exists($locationURL, $refs)) {
        $location = $refs[$locationURL];
        if(array_key_exists('name', $location)) {
          $record['title'] .= ' at ' . $location['name'];
        }
      }
    }
  }

  if(isset($post['author']) && $post['author']['url']) {
    $authorURL = parse_url($post['author']['url']);
    if($authorURL && array_key_exists('host', $authorURL)) {
      $record['post_author'] = $post['author']['url'];
    } else {
      $notices[] = 'No host was found on the author URL (' . $post['author']['url'] . ')';
    }
  } else {
    $notices[] = 'No author URL was found for the h-entry. Using the domain name instead.';
  }

  $synURL = false;
  if(array_key_exists('syndication', $post)) {
    foreach($post['syndication'] as $syn) {
      if(preg_match('/^https?:\/\/' . Config::$hostname . '\/?/', $syn, $match)) {
        $synURL = $syn;
      }
    }
  }
  if(array_key_exists('category', $post)) {
    foreach($post['category'] as $cat) {
      if(preg_match('/^https?:\/\/' . Config::$hostname . '\/?/', $cat, $match)) {
        $synURL = $cat;
      }
    }
  }
  if(!$synURL) {
    $error($res, 'no_link_found', 'Could not find a syndication or category link for this entry to news.indieweb.org. Please see https://news.indieweb.org/how for more information.');
    return;
  }
  if($synURL != $targetURL) {
    $error($res, 'target_mismatch', 'The URL on the page did not match the target URL of the Webmention.');
    return;
  }

  if(array_key_exists('in-reply-to', $post)) {
    // We can only use the first in-reply-to. Not sure what the correct behavior would be for multiple.
    $inReplyTo = $post['in-reply-to'][0];    
  } else {
    $inReplyTo = false;
  }

  # Get the domain of $source and find or create a user account
  $user = ORM::for_table('users')->where('url', $record['post_author'])->find_one();

  if($user == FALSE) {
    $user = ORM::for_table('users')->create();
    $user->url = $record['post_author'];
    $user->date_created = date('Y-m-d H:i:s');
    $user->save();
  }

  $href = $sourceURL;

  if(array_key_exists('bookmark-of', $post)) {
    $href = $post['bookmark-of'][0];
    if(array_key_exists($href, $refs)) {
      if(array_key_exists('name', $refs[$href])) {
        $record['title'] = $refs[$href]['name'];
      } else {
        // TODO: Parse the bookmark URL and find the canonical post title
      }
    }
    // If this is a submission of a bookmark, set the post author to the bookmark website.
    // For now, just set it to the domain of the bookmark. Later we could parse the bookmark for an h-card.
    if($href != $sourceURL) {
      $record['post_author'] = parse_url($href, PHP_URL_SCHEME) . '://' . parse_url($href, PHP_URL_HOST);
    }
  }

  $indieNewsPermalink = permalinkForURL($lang, $href);

  # If there is no existing post for $source, update the properties
  $post = ORM::for_table('posts')->where('lang', $lang)->where('href', $href)->find_one();
  if($post != FALSE) {
    if($record['date'])
      $post->post_date = date('Y-m-d H:i:s', $record['date']);
    $post->post_author = $record['post_author'];
    $post->title = $record['title'];
    if($inReplyTo)
      $post->in_reply_to = $inReplyTo;
    if($record['body'])
      $post->body = $record['body'];
    $post->save();
    $notices[] = 'Already registered, updating properties of the post.';
    $update = true;
  } else {
    # Record a new post
    $post = ORM::for_table('posts')->create();
    $post->lang = $lang;
    $post->user_id = $user->id;
    $post->date_submitted = date('Y-m-d H:i:s');
    if($record['date'])
      $post->post_date = date('Y-m-d H:i:s', $record['date']);
    $post->post_author = $record['post_author'];
    $post->title = $record['title'];
    if($inReplyTo)
      $post->in_reply_to = $inReplyTo;
    if($record['body'])
      $post->body = $record['body'];
    $post->href = $href;
    $post->source_url = $sourceURL;
    $post->save();
    $update = false;

    irc_notice('[indienews' . ($lang == 'en' ? '' : '/'.strtolower($lang)) . '] New post: ' . ($post->title ? '"'.$post->title.'" ' : '') . $post->href . ($sourceURL == $href ? '' : ' (from ' . $sourceURL . ')'));

    # Ping the hub
    if(Config::$hubURL) {
      $ch = curl_init(Config::$hubURL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'hub.mode' => 'publish',
        'hub.topic' => Config::$baseURL . '/' . $lang
      ]));
      curl_setopt($ch, CURLOPT_TIMEOUT, 4);
      curl_exec($ch);
    }
  }

  $res->status(201);
  $res['Content-Type'] = 'application/json';

  $responseData = array(
    'title' => $record['title'],
    'body' => $record['body'] ? true : false,
    'author' => $record['post_author'],
    'date' => ($record['date'] ? date('Y-m-d\TH:i:sP', $record['date']) : false)
  );
  if($inReplyTo) 
    $responseData['in-reply-to'] = $inReplyTo;

  $response = array(
    'result' => 'success',
    'notices' => $notices,
    'data' => $responseData,
    'source' => $req->post('source'),
    'url' => $indieNewsPermalink
  );

  $res['Location'] = $indieNewsPermalink;
  $res->body(json_encode($response));
})->conditions(array('lang'=>LANG_REGEX));

$app->post('/webmention-error', function() use($app) {

  $req = $app->request();
  $res = $app->response();

  $res->status(400);
  $res['Content-Type'] = 'application/json';
  $res->body(json_encode(array(
    'error' => 'no_link_found'
  )));
});

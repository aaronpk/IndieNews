<?php
use Cake\I18n\I18n;

$app->get('/{lang:'.LANG_REGEX.'}/webmention', function($request, $response, $args) {
  I18n::setLocale($args['lang']);

  return render($response, 'webmention', array(
    'title' => 'IndieNews Webmention Endpoint',
    'meta' => '',
    'lang' => $args['lang']
  ));
});

$app->post('/{lang:'.LANG_REGEX.'}/webmention', function($request, $response, $args) {

  $params = (array)$request->getParsedBody();
  $is_html = $params['html'] ?? false;

  $sourceURL = $params['source'] ?? null;
  $targetURL = $params['target'] ?? null;

  $error = function($err, $description=false) use($is_html, $response, $args) {
    if($is_html) {
      $response = $response->withStatus(400);
      return render($response, 'webmention-error', [
        'title' => 'Webmention Error',
        'error' => $err,
        'description' => $description,
        'meta' => '',
        'lang' => $args['lang']
      ]);
    } else {
      $error = array(
        'error' => $err
      );
      if($description)
        $error['error_description'] = $description;

      $response->getBody()->write(json_encode($error));
      return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
  };

  if($sourceURL == null) {
    return $error('missing_source_url', 'No source URL was provided in the request.');
  }
  
  $source = parse_url($sourceURL);

  # Verify $source is valid
  if($source == null
    || !array_key_exists('scheme', $source)
    || !in_array($source['scheme'], array('http','https'))
    || !array_key_exists('host', $source)
  ) {
    return $error('invalid_source_url', 'The source URL was not valid. Ensure the URL is an http or https URL.');
  }

  if($targetURL == null) {
    return $error('missing_target_url', 'No target URL was provided in the request.');
  }

  # Verify $target is actually a resource under our control (home page, individual post)
  $target = parse_url($targetURL);

  # Verify $source is valid
  if($target == null
    || !array_key_exists('scheme', $target)
    || !in_array($target['scheme'], array('http','https'))
    || !array_key_exists('host', $target)
    || $target['host'] != parse_url(Config::$baseURL, PHP_URL_HOST)
  ) {
    return $error('target_not_supported', 'The target URL provided is not supported. Only '.Config::$baseURL.' URLs are accepted.');
  }

  if(!preg_match('/^' . str_replace('/', '\/', Config::$baseURL) . '(?:\/('.LANG_REGEX.'))\/?$/', $targetURL, $match)) {
    return $error('target_not_supported', 'The target you specified does not match a supported URL on this site.');
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

  # Now fetch and parse the page looking for Microformats
  $xray = new p3k\XRay();
  $xray->http = new p3k\HTTP('IndieNews/1.0.0 (https://news.indieweb.org/)');
  $xrayresponse = $xray->parse($sourceURL, ['ignore-as2' => true]);

  if(isset($xrayresponse['error'])) {
    return $error($xrayresponse['error'], 'An error occurred while attempting to fetch the source URL: ' . $xrayresponse['error_description']);
  }

  $post = $xrayresponse['data'];
  if(isset($xrayresponse['refs']))
    $refs = $xrayresponse['refs'];
  else
    $refs = [];

  if(!isset($post['type']) || !in_array($post['type'], ['entry','event'])) {
    return $error('no_link_found', 'No h-entry or h-event was found on the page, so we were unable to find a u-syndication or u-category URL. If you have multiple top-level h-* objects, ensure that one of them has a u-url property set to the URL of the page.');
  }

  $authorURL = false;

  if($post['type'] == 'entry') {

    if(isset($post['name'])) {
      $record['title'] = ellipsize_to_word($post['name'], 200, '...', 10);
    } else {
      $notices[] = 'No "name" property found on the h-entry.';
    }

    if(isset($post['content'])) {
      $record['body'] = ellipsize_to_word($post['content']['text'], 500, '...', 10);
    }

    if(array_key_exists('published', $post)) {
      try {
        $published = new DateTime($post['published']);
      } catch(Exception $e) {
        $notices[] = 'Failed to parse published date';
      }
      if(isset($published)) {
        $record['date'] = $published;
        $utcdate = clone $published;
        $utcdate->setTimeZone(new DateTimeZone('UTC'));
      }
    } else {
      $notices[] = 'No published date found';
    }


  } elseif($post['type'] == 'event') {
    if(isset($post['name'])) {
      $record['title'] = ellipsize_to_word($post['name'], 200, '...', 10);
    } else {
      $notices[] = 'No "name" was found for this h-event';
    }

    if(isset($post['start'])) {
      try {
        $start = new DateTime($post['start']);
      } catch(Exception $e) {
        $notices[] = 'Failed to parse start date';
      }
      if(isset($start) && $start) {
        $record['date'] = $start;
        $utcdate = clone $start;
        $utcdate->setTimeZone(new DateTimeZone('UTC'));
      }
    }

    if($locations=$post['location']) {
      $locationURL = $locations[0];
      if(array_key_exists($locationURL, $refs)) {
        $location = $refs[$locationURL];
        if(array_key_exists('name', $location)) {
          $record['title'] .= ' at ' . ellipsize_to_word($location['name'], 200, '...', 10);
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
      if(strpos($syn, Config::$baseURL) === 0) {
        $synURL = $syn;
      }
    }
  }
  if(array_key_exists('category', $post)) {
    foreach($post['category'] as $cat) {
      if(strpos($cat, Config::$baseURL) === 0) {
        $synURL = $cat;
      }
    }
  }
  if(!$synURL) {
    // Check if this post was previously submitted, and delete it if so
    $post = ORM::for_table('posts')->where('source_url', $sourceURL)->find_one();
    if($post) {
      $post->deleted = 1;
      $post->save();

      $data = array(
        'result' => 'deleted',
      );

      $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES));
      return $response;
    }

    return $error('no_link_found', 'Could not find a syndication or category link for this entry to news.indieweb.org. Please see https://news.indieweb.org/how for more information.');
  }
  if($synURL != $targetURL) {
    return $error('target_mismatch', 'The URL on the page did not match the target URL of the Webmention. Make sure your post links to ' . $targetURL);
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
    // Strip utm tracking params
    $href = p3k\url\strip_tracking_params($post['bookmark-of'][0]);
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

  # If there is no existing post for $sourceURL, update the properties
  $post = ORM::for_table('posts')->where('lang', $lang)->where('source_url', $sourceURL)->find_one();
  if($post != FALSE) {
    if($record['date']) {
      $post->post_date = $utcdate->format('Y-m-d H:i:s');
      $post->tzoffset = $record['date']->format('Z');
    }
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
    if($record['date']) {
      $post->post_date = $utcdate->format('Y-m-d H:i:s');
      $post->tzoffset = $record['date']->format('Z');
    }
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

  if($is_html) {
    return $response
      ->withHeader('Location', $indieNewsPermalink)
      ->withStatus(302);
  } else {
    $response = $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(201);

    $responseData = array(
      'title' => $record['title'],
      'body' => $record['body'] ? true : false,
      'author' => $record['post_author'],
      'date' => ($record['date'] ? $record['date']->format('c') : false)
    );
    if($inReplyTo) 
      $responseData['in-reply-to'] = $inReplyTo;

    $data = array(
      'result' => 'success',
      'notices' => $notices,
      'data' => $responseData,
      'source' => $sourceURL,
      'url' => $indieNewsPermalink
    );

    $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_SLASHES));

    return $response->withHeader('Location', $indieNewsPermalink);
  }
});


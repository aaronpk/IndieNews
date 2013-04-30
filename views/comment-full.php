<div class="row">
  <div class="span8">

<h2>How to Comment on an IndieNews Post</h2>

<p>In order to comment on a post, you do not need an IndieNews account. Instead, you 
can only submit comments as posts from your own site, by linking to the IndieNews page
you wish to comment on and sending a notification using the 
<a href="http://indiewebcamp.com/webmention">webmention</a> protocol!</p>


<h3>1. Write a comment as a post on your own site</h3>

<p>Create a new post on your site, and mark it up with the Microformats markup for 
an <a href="http://microformats.org/wiki/microformats2#h-entry">h-entry</a>.</p>



<h3>2. Add an "in-reply-to" link to the original post</h3>

<p>Somewhere in the h-entry, add a link to the original post you are commenting on 
  (not the IndieNews URL) with the class "<a href="http://indiewebcamp.com/in-reply-to">u-in-reply-to</a>".
  This usually looks something like the following:</p>

<p><pre><code>&lt;a href="http://aaronparecki.com/notes/2013/04/25/1" class="u-in-reply-to" rel="in-reply-to"&gt;
  In Reply To @aaronpk
&lt;/a&gt;</code></pre></p>



<h3>3. Add a "u-syndication" link to IndieNews</h3>

<p>Inside the h-entry, add a link to the IndieNews URL for the post with the class
  <a href="http://indiewebcamp.com/rel-syndication">u-syndication</a>. This usually
  looks something like the following:</p>

<p><pre><code>&lt;a href="http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1" class="u-syndication" rel="syndication"&gt;Also posted on IndieNews&lt;/a&gt;</code></pre></p>

<p>You can construct the IndieNews URL before it's posted to IndieNews by following the
  convention IndieNews uses for building its permalinks. Follow the example above, or
  read the full instructions on <a href="/constructing-post-urls">constructing post URLs</a>.



<h3>4. Send a <a href="http://indiewebcamp.com/webmention">WebMention</a></h3>

<h4>Example Request</h4>

<p>Make a POST request to <code>news.indiewebcamp.com/webmention</code> with two parameters, 
  <code>source</code> and <code>target</code>, where target is 
  <code>http://news.indiewebcamp.com/post/example.com/100</code> and source is 
  <code>http://example.com/100</code> assuming you are submitting a page on your site with 
  the url <code>http://example.com/100</code>.</p>

<pre><code>POST /webmention HTTP/1.1
Host: news.indiewebcamp.com

target=http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1/original-post-discovery
&amp;source=http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery
</code></pre>

<h4>Example Response</h4>

<pre><code>{
 "result": "success",
 "notices": [
 ],
 "data": {
   "title": "A demonstration of Original Post Discovery #indieweb",
   "author": "aaronparecki.com",
   "date": "2013-04-26T03:22:39+00:00"
 },
 "source": "http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery",
 "target": "http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1/original-post-discovery",
 "href": "http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1/original-post-discovery"
}
</code></pre>

<p>This webmention endpoint returns more data than is technically required for a WebMention to succeed. It will
return data that is useful for debugging purposes while you're initially trying it out.</p>

<ul>
  <li><code>result</code> - Will be equal to "success" if the submission was accepted</li>
  <li><code>notices</code> - An array of string messages if there was anything that needs attention in your submission. These are not errors, but will indicate if microformat markup was not present or invalid.</li>
  <li><code>data</code> - This object shows the values extracted from the page, including title, author and date.</li>
  <li><code>source</code> - The source URL sent in the initial request.</li>
  <li><code>target</code> - The target URL sent in the initial request.</li>
  <li><code>href</code> - The permalink to this submission on news.indiewebcamp.com.</li>
  <li><code>canonical</code> - If you accidentally linked your "in-reply-to" to the IndieNews URL, this field will tell you the canonical URL of the post you were actually replying to.</li>
</ul>

<h4>Sample Code</h4>

<h5>Curl</h5>
<pre><code>curl http://news.indiewebcamp.com/webmention -i \
  -d target=http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1/original-post-discovery \
  -d source=http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery
</code></pre>

<h5>PHP</h5>
<pre><code>&lt;?php
$ch = curl_init("http://news.indiewebcamp.com/webmention");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
  'target' => 'http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1/original-post-discovery',
  'source' => 'http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery'
));
echo curl_exec($ch);
?&gt;</code></pre>

<h5>Ruby</h5>
<pre><code>require 'rest-client'
require 'json'

data = JSON.parse RestClient.post "http://news.indiewebcamp.com/webmention", {
  'target' => 'http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1/original-post-discovery',
  'source' => 'http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery'
}
jj data
</code></pre>

<h4>Re-Submitting a Post</h4>

<p>If you update the post (for example trying to debug the microformats markup, or changing the post
title), you can re-send the webmention. The existing post will be updated with the new information found.</p>


<h3>Microformats Support</h3>

<p>Your page must be marked up with an <a href="http://microformats.org/wiki/microformats2#h-entry">h-entry</a> 
or an <a href="http://microformats.org/wiki/microformats2#h-event">h-event</a>, IndieNews will
use the name in the entry as the title of the submission.</p>

<p>If an <a href="http://microformats.org/wiki/microformats-2#h-card">h-card</a> is present,
author information will be pulled from there, otherwise it will fall back to using the domain name as the author.</p>


<h3>Pingback Support</h3>

<p>If you use a client which automatically sends <a href="http://indiewebcamp.com/pingback">pingbacks</a> to
any links found in the post, then you can use the same flow as the WebMention flow but send a Pingback instead!
You can find the pingback endpoint using the normal pingback discovery mechanism.</p>

<p>Note that the rich debugging response will not be present in the pingback reply.</p>

  </div>
</div>

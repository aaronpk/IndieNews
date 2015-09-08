<div class="hero-unit" style="padding: 40px;">
  <h2>IndieNews</h2>
  <p>IndieNews is a community-curated list of articles relevant to the <a href="http://indiewebcamp.com/why">Indie Web</a>.</p>
</div>

<div class="row">
  <div class="span8">

<h2>How to Submit a Post to IndieNews</h2>

<p>In order to submit a post, you do not need an IndieNews account. Instead, you can only submit
posts from your own site by linking to the IndieNews site and sending a notification
using the <a href="http://indiewebcamp.com/webmention">webmention</a> protocol!</p>


<h3>1. Write a post on your own site</h3>

<p>Create a new post on your site, and mark it up with the Microformats markup for 
an <a href="http://microformats.org/wiki/microformats2#h-entry">h-entry</a>.</p>



<h3>2. Add a "u-syndication" or "u-category" link to IndieNews</h3>

<p>Inside the h-entry, add a link to the IndieNews home page for your language with the class
  <a href="http://indiewebcamp.com/rel-syndication">u-syndication</a> or 
  <a href="http://indiewebcamp.com/u-category">u-category</a>. This usually
  looks something like the following:</p>

<p><pre><code>&lt;a href="http://news.indiewebcamp.com/en" class="u-syndication"&gt;
  Also posted on IndieNews
&lt;/a&gt;</code></pre></p>

<p><pre><code>&lt;a href="http://news.indiewebcamp.com/en" class="u-category"&gt;#indienews&lt;/a&gt;</code></pre></p>


<h3>3. Send a <a href="http://indiewebcamp.com/webmention">Webmention</a></h3>

<h4>Example Request</h4>

<p>Make a POST request to <code>http://news.indiewebcamp.com/en/webmention</code> with two parameters, 
  <code>source</code> and <code>target</code>, where target is 
  <code>http://news.indiewebcamp.com/en</code> and source is 
  <code>http://example.com/100</code> assuming you are submitting a page on your site with 
  the url <code>http://example.com/100</code>.</p>

<p>Note that each language's home page has a unique Webmention endpoint, so you should 
  do the Webmention endpoint discovery as normal to find it.</p>

<pre><code>POST /en/webmention HTTP/1.1
Host: news.indiewebcamp.com

target=http://news.indiewebcamp.com/en
&amp;source=http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery
</code></pre>


<h4>Example Response</h4>

<pre><code>
HTTP/1.1 201 Created
Location: http://news.indiewebcamp.com/en/aaronparecki.com/notes/2013/04/25/1/original-post-discovery

{
 "result": "success",
 "notices": [
 ],
 "data": {
   "title": "A demonstration of Original Post Discovery #indieweb",
   "author": "aaronparecki.com",
   "date": "2013-04-26T03:22:39+00:00"
 },
 "source": "http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery",
 "url": "http://news.indiewebcamp.com/en/aaronparecki.com/notes/2013/04/25/1/original-post-discovery"
}
</code></pre>

<p>You can find the permalink of your syndication by looking for the <code>Location</code> header in the response. You can then update your post with that URL so that your post always links to the IndieNews permalink instead of the IndieNews home page.</p>

<p>This webmention endpoint also returns more data that is useful for debugging purposes while you're initially trying it out.</p>

<ul>
  <li><code>result</code> - Will be equal to "success" if the submission was accepted</li>
  <li><code>notices</code> - An array of string messages if there was anything that needs attention in your submission. These are not errors, but will indicate if microformat markup was not present or invalid.</li>
  <li><code>data</code> - This object shows the values extracted from the page, including title, author and date.</li>
  <li><code>source</code> - The source URL sent in the initial request</li>
  <li><code>url</code> - The permalink to this submission on news.indiewebcamp.com.</li>
</ul>

<h4>Sample Code</h4>

<h5>Curl</h5>
<pre><code>curl http://news.indiewebcamp.com/en/webmention -i \
  -d target=http://news.indiewebcamp.com/en \
  -d source=http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery
</code></pre>

<h5>PHP</h5>
<pre><code>&lt;?php
$ch = curl_init("http://news.indiewebcamp.com/en/webmention");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, array(
  'target' => 'http://news.indiewebcamp.com/en',
  'source' => 'http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery'
));
echo curl_exec($ch);
?&gt;</code></pre>

<h5>Ruby</h5>
<pre><code>require 'rest-client'
require 'json'

data = JSON.parse RestClient.post "http://news.indiewebcamp.com/en/webmention", {
  'target' => 'http://news.indiewebcamp.com/en',
  'source' => 'http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery'
}
jj data
</code></pre>

<h4>Re-Submitting a Post</h4>

<p>If you update the post (for example trying to debug the microformats markup, or changing the post
title), you can re-send the webmention. The existing IndieNews post will be updated with the new information found.</p>


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

<div class="row">
  <div class="span8">

<h2>How to Submit a Post</h2>

<p>IndieNews is a community-curated list of articles relevant to the <a href="http://indiewebcamp.com/why">Indie Web</a>.</p>

<p>In order to submit a post, you do not need an IndieNews account. Instead, you can only submit
posts from your own site by using the <a href="http://indiewebcamp.com/webmention">webmention</a>
protocol!</p>

<p>If you want to upvote articles, you can sign in to IndieNews. Naturally, you don't need to 
create a separate IndieNews account, you just need to <a href="http://indiewebcamp.com/indieauth">sign in with your domain</a>! 
The <a href="/">Front Page</a> shows a list of articles ranked using a 
<a href="http://amix.dk/blog/post/19574">similar algorithm as HackerNews</a>. 
Recent and popular articles will tend towards the top of the list.</p>


<h3>1. Add a link to news.indiewebcamp.com</h3>

<p>Somewhere on the page you are submitting, add a link to <code>http://news.indiewebcamp.com/</code>, or
<code>http://news.indiewebcamp.com/newest</code>.</p>

<p>You can update this link later to this submission's permalink on IndieNews. See the example response 
below for how to find the permalink.</p>

<p>Note: Technically this step is not required right now. Following the webmention/pingback
protocols it should be required, but currently the system will accept a webmention even if the
source site does not link to IndieNews. I'm debating whether or not it should be required for
this, or if there should be some other verification method instead. 
<a href="http://github.com/aaronpk/IndieNews/issues">Feedback is appreciated</a>.</p>


<h3>2. Send a <a href="http://indiewebcamp.com/webmention">WebMention</a></h3>

<h4>Example Request</h4>

<p>Simply make a POST request to <code>news.indiewebcamp.com/webmention</code> with two parameters, <code>source</code> and <code>target</code>,
where target is <code>http://news.indiewebcamp.com/</code> and source is the page on your site you would
like to submit.</p>

<pre><code>POST /webmention HTTP/1.1
Host: news.indiewebcamp.com

target=http://news.indiewebcamp.com/&amp;source=http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery
</code></pre>

<h4>Example Response</h4>

<pre><code>{
  "result": "success",
  "notices": [
  ],
  "data": {
    "title": "A demonstration of Original Post Discovery indiewebcamp.com/original-post-discovery #indieweb aaronparecki.com/files/original-post-discovery.mp4",
    "author": "aaronparecki.com",
    "date": "2013-04-26T03:22:39+00:00"
  },
  "source": "http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery",
  "target": "http://news.indiewebcamp.com/",
  "href": "http://news.indiewebcamp.com/post/17"
}
</code></pre>

<p>This webmention endpoint returns more data than is technically required for a WebMention to succeed. It will
return data that is useful for debugging purposes while you're initially trying it out.</p>

<ul>
  <li><code>result</code> - Will be equal to "success" if the submission was accepted</li>
  <li><code>notices</code> - An array of string messages if there was anything that needs attention in your submission. These are not errors, but will indicate if microformat markup was not present or invalid.</li>
  <li><code>data</code> - This object shows the values extracted from the page, including title, author and date.</li>
  <li><code>source</code> - The source URL sent in the initial request</li>
  <li><code>source</code> - The target URL sent in the initial request</li>
  <li><code>href</code> - The permalink to this submission on news.indiewebcamp.com. In the future, this can be used to add a comment to the story by linking to this URL and sending another webmention or pingback.</li>
</ul>


<h4>Re-Submitting a Post</h4>

<p>If you update the post (for example trying to debug the microformats markup, or changing the post
title), you can re-send the webmention. The existing post will be updated with the new information found.</p>


<h3>Microformats Support</h3>

<p>If your page is marked up with an <a href="http://microformats.org/wiki/microformats2#h-entry">h-entry</a> 
or an <a href="http://microformats.org/wiki/microformats2#h-event">h-event</a>, this site will
use the name and author in the entry.</p>

<p>If no Microformats are present, then it falls back to using the page title and domain name
as the author.</p>


<h3>Pingback Support</h3>

<p>If you use a client which automatically sends <a href="http://indiewebcamp.com/pingback">pingbacks</a> to
any links found in the post, then you simply need to link to the IndieNews home page in a post to submit it!
You can find the pingback endpoint using the normal pingback discovery mechanism.</p>

<p>Note that the rich debugging response will not be present in the pingback reply.</p>

  </div>
</div>

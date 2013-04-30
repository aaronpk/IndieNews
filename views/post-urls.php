<div class="row">
  <div class="span8">

    <h2>How to Construct Post URLs for IndieNews</h2>

    <p>Since you need to be able to create a <a href="http://indiewebcamp.com/rel-syndication">syndication</a> 
      link to IndieNews before submitting a post, you may be wondering how you can know
      the URL your post will have on IndieNews before it's submitted!</p>

    <p>IndieNews uses a simple convention for creating permalinks to submissions, so
      that the algorithm is known ahead of time by submitters. This is best illustrated
      by an example.</p>

    <p>Say your post has the following URL on your site:</p>

    <p><code>http://aaronparecki.com/notes/2013/04/25/1/original-post-discovery</code></p>

    <p>This submission on IndieNews will have the following URL:</p>

    <p><code>http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1/original-post-discovery</code></p>

    <p>All you need to do is add this link into your <a href="http://indiewebcamp.com/h-entry">h-entry</a>
      with the class <code>u-syndication</code>, and the link will be configured properly.</p>

    <p><pre><code>&lt;a href="http://news.indiewebcamp.com/post/aaronparecki.com/notes/2013/04/25/1/original-post-discovery" class="u-syndication"&gt;Comments on IndieNews&lt;/a&gt;</code></pre></p>

  </div>
</div>
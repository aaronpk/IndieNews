<div class="hero-unit" style="padding: 40px;">
  <h2>IndieNews</h2>
  <p><?= __('IndieNews is a community-curated list of articles relevant to the {0}Indie Web{1}.', ['<a href="https://indieweb.org/why">', '</a>']) ?></p>
</div>

<div class="row">
  <div class="span12">
    <h2><?= __('Submit') ?></h2>

    <p><?= __('You won\'t find a "submit" form on this site. You don\'t even need to be logged in to this site to submit a post!') ?></p>

    <h4><?= __('Write a Post') ?></h4>

    <p><?= __('Write a post on your own site, and mark it up with the Microformats markup for an {0}.', '<a href="http://microformats.org/wiki/microformats2#h-entry">h-entry</a>') ?></p>

    <h4><?= __('Link to IndieNews') ?></h4>

    <p><?= __('Somewhere on the page you are submitting, add a {0} or {1} link to {2}the IndieNews home page{3} for your language.', ['<a href="https://indieweb.org/rel-syndication">u-syndication</a>', '<a href="https://indieweb.org/u-category">u-category</a>', '<a href="/'.$this->lang.'">', '</a>']) ?></p>

    <h4><?= __('Send a Webmention') ?></h4>

    <p><?= __('Send a {0} notification to let IndieNews know about your post. When the mention is received, IndieNews will fetch the page from your site and look for the {1} markup to find the post title and author.', ['<a href="https://indieweb.org/webmention">Webmention</a>', '<a href="http://microformats.org/wiki/microformats2#h-entry">h-entry</a>']) ?></p>

    <p style="border-top: 1px #ddd solid; margin-top: 20px; font-style: italic;">
      <a href="/how-to-submit-a-post"><?= __('Detailed instructions on how to submit a post') ?></a>
    </p>

  </div>
</div>

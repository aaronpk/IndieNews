<div class="row">
  <div class="span12">
    <h2><?= __('Submit a Post') ?></h2>

    <h4><?= __('Write a Post') ?></h4>

    <p><?= __('Write a post on your own site, and mark it up with the Microformats markup for an {0}.', '<a href="http://microformats.org/wiki/h-entry">h-entry</a>') ?></p>

    <p><?= __('To submit someone else\'s post, create a {0} post on your site linking to it.', '<a href="https://indieweb.org/bookmark">bookmark</a>') ?></p>

    <h4><?= __('Link to IndieNews') ?></h4>

    <p><?= __('Somewhere on the page you are submitting, add a {0} or {1} link to {2}the IndieNews home page{3} for your language.', ['<a href="https://indieweb.org/rel-syndication">u-syndication</a>', '<a href="https://indieweb.org/u-category">u-category</a>', '<a href="/'.$this->lang.'">', '</a>']) ?></p>

    <h4><?= __('Send a Webmention') ?></h4>

    <p><?= __('If your website sends {0} automatically, then your post will be submitted as soon as you post it to your website! Otherwise, you can enter your post\'s URL below.', ['<a href="https://www.w3.org/TR/webmention/">Webmentions</a>']) ?>

    <p><?= __('IndieNews will fetch the page from your site and look for the {1} markup to find the post title and author.', ['<a href="https://indieweb.org/webmention">Webmention</a>', '<a href="http://microformats.org/wiki/h-entry">h-entry</a>']) ?></p>

    <div style="background-color: #efefef; border: 1px #ccc solid; border-radius: 4px; padding: 12px; margin-bottom: 1em;">
      <form action="/<?= $this->lang ?>/webmention" method="POST" style="margin: 0;">
        <div>
          <label for="source"><?= __('Your Post URL') ?></label>
          <input type="url" name="source">
        </div>
        <input type="hidden" name="target" value="<?= Config::$baseURL ?>/<?= $this->lang ?>">
        <input type="hidden" name="html" value="1">
        <button type="submit" class="btn"><?= __('Submit') ?></button>
      </form>
    </div>

    <p style="border-top: 1px #ddd solid; margin-top: 20px; font-style: italic;">
      <a href="/how-to-submit-a-post"><?= __('Detailed instructions on how to submit a post') ?></a>
    </p>

  </div>
</div>

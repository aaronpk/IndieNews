<div class="row">
  <div class="span12">

    <h2><?= __('Webmention Endpoint') ?></h2>

    <div style="max-width: 600px;">
      <p><?= __('Once you\'ve <a href="/how-to-submit-a-post">written a post</a> with the proper markup, you will need to send a Webmention to submit it to IndieNews. Your software should do that automatically if it supports {0}, but you can also use the form below.', ['<a href="https://www.w3.org/TR/webmention/">Webmention</a>']) ?></p>
    </div>

    <form class="form-horizontal" action="/<?= $this->lang ?>/webmention" method="POST">
      <div class="control-group">
        <label class="control-label" for="source"><?= __('Your Post URL') ?></label>
        <div class="controls">
          <input type="url" name="source" placeholder="">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="source"><?= __('IndieNews Page') ?></label>
        <div class="controls">
          <select name="target">
            <?php foreach(supportedLanguages() as $l=>$lang): ?>
              <option value="<?= Config::$baseURL.'/'.$l ?>" <?= $this->lang == $l ? ' selected="selected"' : '' ?>><?= $lang ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <button type="submit" class="btn"><?= __('Send Webmention') ?></button>
        </div>
      </div>
      <input type="hidden" name="html" value="1">
    </form>

  </div>
</div>

<div class="row">
  <div class="span12">

    <h2><?= __('Webmention Endpoint') ?></h2>

    <div style="max-width: 600px;">
      <p><?= __('Once you\'ve <a href="/how-to-submit-a-post">written a post</a> with the proper markup, you will need to send a Webmention to submit it to IndieNews. Your software should do that automatically, but you can also use the form below.') ?></p>
    </div>

    <form class="form-horizontal" action="/<?= $this->lang ?>/webmention" method="POST">
      <div class="control-group">
        <label class="control-label" for="source">Your URL</label>
        <div class="controls">
          <input type="url" name="source" placeholder="">
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="source">IndieNews URL</label>
        <div class="controls">
          <input type="url" name="target">
        </div>
      </div>
      <div class="control-group">
        <div class="controls">
          <button type="submit" class="btn">Send Webmention</button>
        </div>
      </div>
    </form>

  </div>
</div>
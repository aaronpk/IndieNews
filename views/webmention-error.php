<div class="row">
  <div class="span12">

    <div class="alert alert-error">
      <strong><?= __('Error') ?>!</strong> <?= __('There was an error with your submission. See the details below.') ?>
    </div>

    <p><?= __('Error') ?>: <code><?= $error ?></code></p>
    <p><?= htmlspecialchars($description) ?></p>

  </div>
</div>
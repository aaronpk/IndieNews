<div class="row">
  <div class="span12">

    <h2><?= __('Members') ?></h2>

    <p><?= __('People who have submitted posts in the last year') ?></p>

    <ul>
    <?php foreach($this->users as $u): ?>
      <li><a href="<?= htmlspecialchars($u->url) ?>"><?= htmlspecialchars($u->url) ?></a> (<?= $u->num_posts ?> <?= __($u->num_posts == 1 ? 'post' : 'posts') ?>)</li>
    <?php endforeach ?>
    </ul>

  </div>
</div>

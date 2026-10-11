<div class="row">
  <div class="span12">

    <h2><?= __('Blocklist') ?></h2>

    <p><?= __('Webmentions from blocked domains (including all of their subdomains) or blocked source URLs are rejected.') ?></p>

    <?php if($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="/<?= $lang ?>/blocks" method="POST" class="well block-form">
      <label for="block-domain"><?= __('Domain') ?></label>
      <input type="text" id="block-domain" name="domain" placeholder="surge.sh" value="<?= htmlspecialchars($form['domain'] ?? '') ?>">

      <label for="block-source-url"><?= __('or a single source URL') ?></label>
      <input type="url" id="block-source-url" name="source_url" placeholder="https://example.com/post" value="<?= htmlspecialchars($form['source_url'] ?? '') ?>">

      <label for="block-note"><?= __('Note') ?></label>
      <input type="text" id="block-note" name="note" value="<?= htmlspecialchars($form['note'] ?? '') ?>">

      <div>
        <button type="submit" class="btn btn-danger"><?= __('Block') ?></button>
      </div>
    </form>

    <?php if(count($blocks)): ?>
      <table class="table table-striped blocks">
        <thead>
          <tr>
            <th><?= __('Blocked') ?></th>
            <th><?= __('Note') ?></th>
            <th><?= __('Added') ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($blocks as $block): ?>
            <tr>
              <td>
                <?php if($block->domain): ?>
                  <?= htmlspecialchars($block->domain) ?> <span class="label"><?= __('domain') ?></span>
                <?php else: ?>
                  <a href="<?= htmlspecialchars($block->source_url) ?>"><?= htmlspecialchars($block->source_url) ?></a> <span class="label"><?= __('URL') ?></span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($block->note ?? '') ?></td>
              <td><?= $block->date_created ? date('Y-m-d', strtotime($block->date_created)) : '' ?></td>
              <td class="post-actions">
                <form method="post" action="/<?= $lang ?>/blocks/<?= $block->id ?>/delete">
                  <button class="btn btn-mini" type="submit"><?= __('Unblock') ?></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p><?= __('Nothing is blocked yet.') ?></p>
    <?php endif; ?>

  </div>
</div>

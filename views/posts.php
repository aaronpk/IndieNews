
  <table class="table table-striped">
    <?php foreach($this->posts as $i=>$post): ?>
      <?= partial('_post-row', array(
        'post' => $post,
        'position' => $i+1, 
        'view' => 'list'))
      ?>
    <?php endforeach; ?>
  </table>

  <?php if(count($this->posts)): ?>
  <div style="padding-left: 8px;">
    <a href="/?before=<?= b10to60(strtotime($this->posts[count($this->posts)-1]->date_submitted)) ?>"><?= __('Older') ?></a>
  </div>
  <?php endif; ?>

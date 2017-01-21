
  <div class="h-feed">
    <div style="display: none;">
      <a href="/<?= $this->lang ?>" class="u-url"><h2 class="p-name"><?= __('IndieNews English') ?></h2></a>
    </div>

    <table class="table table-striped">
      <?php foreach($this->posts as $i=>$post): ?>
        <?= partial('_post-row', array(
          'post' => $post,
          'position' => $i+1, 
          'view' => 'list'))
        ?>
      <?php endforeach; ?>
    </table>
  </div>

  <?php if(count($this->posts) == 0): ?>
  <div class="hero-unit">
    <h2><?= __('No posts yet!') ?></h2>
    <p><?= __('There are no posts submitted to the English feed yet! As soon as you {0}submit a post{1} it will show up here.', ['<a href="/'.$this->lang.'/submit">','</a>']) ?></p>
  </div>
  <?php endif; ?>


  <?php if(count($this->posts)): ?>
  <div style="padding-left: 8px;">
    <a href="/<?= $this->lang ?>?before=<?= b10to60(strtotime($this->posts[count($this->posts)-1]->date_submitted)) ?>"><?= __('Older') ?></a>
  </div>
  <?php endif; ?>

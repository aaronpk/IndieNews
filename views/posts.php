
  <table class="table table-striped">
    <?php foreach($this->posts as $i=>$post): ?>
      <?= partial('_post-row', array(
        'post' => $post,
        'parent' => false,
        'position' => $i+1, 
        'view' => 'list'))
      ?>
    <?php endforeach; ?>
  </table>

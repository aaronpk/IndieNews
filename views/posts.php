
  <table class="table table-striped">
    <?php foreach($this->posts as $i=>$post): ?>
      <?= partial('_post-row', array(
        'post' => $post,
        'position' => $i+1, 
        'view' => 'list'))
      ?>
    <?php endforeach; ?>
  </table>


  <table class="table table-striped">
    <?php foreach($this->posts as $i=>$post): ?>
      <?= partial('_post-row', array(
        'post' => $post, 
        'position' => $i+1, 
        'voted' => in_array($post->id, $this->votes),
        'view' => 'list'))
      ?>
    <?php endforeach; ?>
  </table>

<table class="threaded">
  <?= partial('_post-row', array(
    'post' => $this->post,
    'parent' => $this->parent,
    'position' => '', 
    'voted' => in_array($this->post->id, $this->votes),
    'view' => 'single')) 
  ?>
  <tr>
    <td></td>
    <td></td>
    <td>
      <h5>How to comment</h5>
      <p>To comment on this post, you should:
        <ol>
          <li>Create a <a href="http://indiewebcamp.com/post">post</a> on your own domain</li>
          <li>link to this page, or to one of the individual comments, preferably with the <a href="http://indiewebcamp.com/in-reply-to">in-reply-to</a> markup</li>
          <li>send a <a href="http://indiewebcamp.com/webmention">webmention</a> notification</li>
        </ol>
      </p>
    </td>
  </tr>
  <?php foreach($this->replies as $i=>$post): ?>
    <?= partial('_reply-row', array(
      'post' => $post, 
      'position' => '',
      'indent' => 0,
      'voted' => in_array($post->id, $this->votes),
      'view' => 'list'))
    ?>
  <?php endforeach; ?>
</table>

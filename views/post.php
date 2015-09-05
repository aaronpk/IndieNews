<table class="threaded">
  <?= partial('_post-row', array(
    'post' => $this->post,
    'parent' => $this->parent,
    'position' => '', 
    'view' => 'single')) 
  ?>
  <tr>
    <td>
      <h5>Comment</h5>
      <action inline do="reply" with="<?= $this->post->href ?>">
        <p>To comment on this post, you should:
          <ol>
            <li>Create a <a href="http://indiewebcamp.com/post">post</a> on your own domain</li>
            <li>link to the original post with the <a href="http://indiewebcamp.com/in-reply-to">in-reply-to</a> markup</li>
            <li>add a <a href="http://indiewebcamp.com/rel-syndication">u-syndication</a> link to IndieNews</li>
            <li>send a <a href="http://indiewebcamp.com/webmention">webmention</a> notification</li>
          </ol>
        </p>
      </action>
    </td>
  </tr>
  <?php foreach($this->replies as $i=>$post): ?>
    <?= partial('_reply-row', array(
      'post' => $post, 
      'position' => '',
      'indent' => 0,
      'view' => 'list'))
    ?>
  <?php endforeach; ?>
</table>

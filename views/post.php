<table class="table table-striped">
  <?= partial('_post-row', array('post'=>$this->post, 'position'=>'', 'voted'=>in_array($this->post->id, $this->votes))) ?>
  <tr>
    <td></td>
    <td></td>
    <td>
      <h5>How to comment</h5>
      <p>Comments are not currently implemented.</p>
      <p>In the future, to comment, you would create a new 
        <a href="http://indiewebcamp.com/post">post</a> on your own domain, and link to this
        page, preferably with the <a href="http://indiewebcamp.com/in-reply-to">in-reply-to</a> markup.</p>
    </td>
  </tr>
</table>

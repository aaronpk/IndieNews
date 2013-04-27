<tr>
  <td class="nested-container" colspan="3">

    <table class="nested">
      <tr class="post post_<?= $this->post->id ?> h-entry">
        <td class="position" style="padding-left: <?= $this->indent * 20 ?>px;"><?= $this->position ? $this->position . '.' : '' ?></td>
        <td class="vote">
          <? if(session('user') && !$this->voted) { ?>
            <a href="#" data-id="<?= $this->post->id ?>" data-vote="up" class="up"><i class="icon-arrow-up"></i></a>
            <!-- <a href="#" data-id="<?= $this->post->id ?>" data-vote="down" class="down"><i class="icon-arrow-down"></i></a> -->
          <? } ?>
        </td>
        <td>
          <div class="details">
            <span class="p-author h-card"><a href="http://<?= $this->post->domain ?>" class="u-url"><?= $this->post->domain ?></a></span> | 
            <span class="points"><?= $this->post->points ?> point<?= $this->post->points == 1 ? '' : 's' ?></span> | 
            <span class="dt-published"><?= $this->post->post_date ? date('Y-m-d H:i T', strtotime($this->post->post_date)) . ' |' : '' ?></span>
            submitted <?= TimeAgo::inWords($this->post->date_submitted) ?> | 
            <a href="/post/<?= $this->post->id ?>">link</a>
          </div>
          <? if($hasTitle = ($this->post->title && trim($this->post->title) != trim($this->post->body))) { ?>
            <div class="title p-name"><a href="<?= $this->post->href ?>"><?= $this->post->title ?: preg_replace('/^https?:\/\//', '', $this->post->href) ?></a></div>
          <? } ?>
          <div class="content e-content<?= $hasTitle ? '' : ' p-name' ?>"><?= $this->post->body ?></div>
        </td>
      </tr>
    </table>
    <table class="nested">
      <?php
      $replies = getPostsForParentID($this->post->id);
      $votes = getUserVotesForPosts($replies);
      ?>
      <?php foreach($replies as $i=>$post): ?>
        <?= partial('_reply-row', array(
          'post' => $post, 
          'position' => '',
          'indent' => $this->indent + 1,
          'voted' => in_array($post->id, $votes),
          'view' => 'list'))
        ?>
      <?php endforeach; ?>
    </table>

  </td>
</tr>
<tr class="post post_<?= $this->post->id ?> single h-entry">
  <td class="position"><?= $this->position ? $this->position . '.' : '' ?></td>
  <td class="vote">
    <? if(session('user') && !$this->voted) { ?>
      <a href="#" data-id="<?= $this->post->id ?>" data-vote="up" class="up"><i class="icon-arrow-up"></i></a>
      <!-- <a href="#" data-id="<?= $this->post->id ?>" data-vote="down" class="down"><i class="icon-arrow-down"></i></a> -->
    <? } ?>
  </td>
  <td>
    <div class="title p-name"><a href="<?= $this->post->href ?>" class="u-url" rel="canonical"><?= $this->post->title ?: preg_replace('/^https?:\/\//', '', $this->post->href) ?></a></div>
    <div class="details">
      <span class="p-author h-card"><a href="http://<?= $this->post->domain ?>" class="u-url"><?= $this->post->domain ?></a></span> | 
      <span class="points"><?= $this->post->points ?> point<?= $this->post->points == 1 ? '' : 's' ?></span> | 
      <?= $this->post->post_date ? '<span class="dt-published">' . date('Y-m-d H:i T', strtotime($this->post->post_date)) . '</span> |' : '' ?>
      submitted <?= TimeAgo::inWords($this->post->date_submitted) ?> | 
      <a href="/post/<?= preg_replace('/^https?:\/\//', '', $this->post->href) ?>">
        <? if($this->view == 'list') { ?>
          <?= $this->post->comments ?: '' ?> comment<?= $this->post->comments == 1 ? '' : 's' ?>
        <? } else { ?>
          link
        <? } ?>
      </a>
      <? if($this->post->parent_id > 0) { ?>
        | <a href="/post/<?= slugForURL($this->post->href) ?>" class="u-in-reply-to" rel="in-reply-to">parent</a>
      <? } ?>
    </div>
    <? if($this->view == 'single' && $this->post->parent_id > 0 && $this->post->body && trim($this->post->title) != trim($this->post->body)) { ?>
      <div class="content e-content"><?= $this->post->body ?></div>
    <? } ?>
  </td>
</tr>

<tr class="post post_<?= $this->post->id ?> single h-entry">
  <td>
    <div class="title p-name"><a href="<?= $this->post->href ?>" class="u-url"><?= $this->post->title ?: preg_replace('/^https?:\/\//', '', $this->post->href) ?></a></div>
    <div class="details">
      <span class="p-author h-card"><a href="http://<?= $this->post->domain ?>" class="u-url"><?= $this->post->domain ?></a></span> | 
      <?= $this->post->post_date ? '<span class="dt-published">' . date('Y-m-d H:i T', strtotime($this->post->post_date)) . '</span> |' : '' ?>
      submitted <?= TimeAgo::inWords($this->post->date_submitted) ?> | 
      <a href="/post/<?= preg_replace('/^https?:\/\//', '', $this->post->href) ?>">
        <? if($this->view == 'list') { ?>
          <?= $this->post->comments ?: '' ?> comment<?= $this->post->comments == 1 ? '' : 's' ?>
        <? } else { ?>
          link
        <? } ?>
      </a>
      <? if($this->parent) { ?>
        | <a href="/post/<?= slugForURL($this->parent->href) ?>" class="u-in-reply-to" rel="in-reply-to">parent</a>
      <? } ?>
    </div>
    <? if($this->view == 'single' && $this->post->parent_id > 0 && $this->post->body && trim($this->post->title) != trim($this->post->body)) { ?>
      <div class="content p-content"><?= $this->post->body ?></div>
    <? } ?>
  </td>
</tr>

<tr class="post post_<?= $this->post->id ?> single h-entry">
  <td>
    <div class="title p-name"><a href="<?= $this->post->href ?>" class="u-url"><?= $this->post->title ?: preg_replace('/^https?:\/\//', '', $this->post->href) ?></a></div>
    <div class="details">
      <span class="p-author h-card"><a href="http://<?= $this->post->domain ?>" class="u-url"><?= $this->post->domain ?></a></span> | 
      <?= $this->post->post_date ? '<time class="dt-published" datetime="'.date('c', strtotime($this->post->post_date)).'">' . date('Y-m-d H:i T', strtotime($this->post->post_date)) . '</time> |' : '' ?>
      submitted <?= TimeAgo::inWords($this->post->date_submitted) ?> | 
      <a href="/post/<?= preg_replace('/^https?:\/\//', '', $this->post->href) ?>">permalink</a>
      <? if($this->post->in_reply_to) { ?>
        | <a href="<?= $this->post->in_reply_to ?>" class="u-in-reply-to">in reply to</a>
      <? } ?>
    </div>
  </td>
</tr>

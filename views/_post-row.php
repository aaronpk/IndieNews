<tr class="post post_<?= $this->post->id ?> single h-entry">
  <td>
    <div class="title p-name"><a href="<?= $this->post->href ?>" class="u-url"><?= $this->post->title ?: preg_replace('/^https?:\/\//', '', $this->post->href) ?></a></div>
    <div class="details">
      <span class="p-author h-card"><a href="<?= $this->post->post_author ?>" class="u-url"><?= friendly_url($this->post->post_author) ?></a></span> | 
      <? if($this->post->in_reply_to) { ?>
        <a href="<?= $this->post->in_reply_to ?>" class="u-in-reply-to"><?= __('in reply to') ?></a> |
      <? } ?>
      <?= $this->post->post_date ? '<time class="dt-published" datetime="'.date('c', strtotime($this->post->post_date)).'">' . date('Y-m-d H:i T', strtotime($this->post->post_date)) . '</time> |' : '' ?>
      <?= __('submitted') ?> <?= TimeAgo::inWords($this->post->date_submitted) ?> 
      <?= $this->post->source_url != $this->post->href ? ' '.__('submitted ... from').' <a href="'.$this->post->source_url.'">'.(parse_url($this->post->source_url, PHP_URL_HOST)).'</a>' : '' ?> | 
      <a href="/<?= $this->post->lang ?>/<?= preg_replace('/^https?:\/\//', '', $this->post->href) ?>"><?= __('permalink') ?></a>
    </div>
  </td>
</tr>

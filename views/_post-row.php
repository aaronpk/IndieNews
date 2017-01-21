<tr class="post post_<?= $this->post->id ?> single h-entry">
  <td>
    <div class="title p-name"><a href="<?= $this->post->href ?>" class="u-url"><?= htmlspecialchars(shouldDisplayPostName($this->post->title) ? $this->post->title : display_url($this->post->href)) ?></a></div>
    <? if(!shouldDisplayPostName($this->post->title)): ?>
      <div class="content e-content"><?= auto_link(htmlspecialchars(substr($this->post->title ?: $this->post->body,0,600))) ?></div>
    <? endif; ?>
    <div class="details">
      <span class="p-author h-card">
        <a href="<?= $this->post->post_author ?>" class="u-url">
          <?= display_url($this->post->post_author) ?>
        </a>
      </span> | 
      <? if($this->post->in_reply_to): ?>
        <a href="<?= $this->post->in_reply_to ?>" class="u-in-reply-to"><?= __('in reply to') ?></a> |
      <? endif ?>
      <?= $this->post->post_date ? 
          '<time class="dt-published" datetime="'.date('c', strtotime($this->post->post_date)).'">' 
            . date('Y-m-d H:i T', strtotime($this->post->post_date)) 
            . '</time> |' : '' ?>
      <?= __('submitted') ?> 
      <a href="<?= permalinkForURL($this->post->lang, $this->post->href) ?>">
        <time class="timeago" datetime="<?= date('c', strtotime($this->post->date_submitted)) ?>"><?= date('Y-m-d H:i T', strtotime($this->post->date_submitted)) ?>
        </time>
      </a>
      <?= $this->post->source_url != $this->post->href ? 
          ' ' . __('submitted ... from')
          . ' <a href="'.$this->post->source_url.'">'
          . (parse_url($this->post->source_url, PHP_URL_HOST)) 
          . '</a>' : '' ?>
    </div>
  </td>
</tr>

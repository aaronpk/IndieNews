<tr class="post post_<?= $post->id ?> single h-entry">
  <td>
    <?php if(shouldDisplayPostName($post->title)): ?>
      <div class="title p-name"><a href="<?= $post->href ?>"><?= htmlspecialchars(shouldDisplayPostName($post->title) ? $post->title : display_url($post->href)) ?></a></div>
    <?php else: ?>
      <div class="content e-content p-name"><?= htmlspecialchars(substr($post->title ?: $post->body,0,600)) ?></div>
    <?php endif; ?>
    <div class="details">
      <span>
        <a href="<?= $post->post_author ?>" class="u-author h-card">
          <?= display_url($post->post_author) ?>
        </a>
      </span> | 
      
      <?php if($post->in_reply_to): ?>
        <a href="<?= $post->in_reply_to ?>" class="u-in-reply-to"><?= __('in reply to') ?></a> |
      <?php endif ?>

      <a href="<?= $post->href ?>" class="u-url">
      <?php if($post->post_date): ?> 
        <time class="" datetime="'.printLocalDate('c', $post->post_date, $post->tzoffset).'">
          <?= printLocalDate('Y-m-d H:i T', $post->post_date, $post->tzoffset) ?>
        </time>
      <?php else: ?>
        <?= __('permalink') ?>
      <?php endif ?>
      </a> |

      <?= __('submitted') ?> 
      <a href="<?= permalinkForURL($post->lang, $post->href) ?>">
        <time class="timeago dt-published" datetime="<?= date('c', strtotime($post->date_submitted)) ?>"><?= date('Y-m-d H:i T', strtotime($post->date_submitted)) ?>
        </time>
      </a>
      <?= $post->source_url != $post->href ? 
          ' ' . __('submitted ... from')
          . ' <a href="'.$post->source_url.'">'
          . (parse_url($post->source_url, PHP_URL_HOST)) 
          . '</a>' : '' ?>
    </div>
  </td>
</tr>

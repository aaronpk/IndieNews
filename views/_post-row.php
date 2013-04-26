<tr class="post post_<?= $this->post->id ?>">
  <td class="position"><?= $this->position ? $this->position . '.' : '' ?></td>
  <td class="vote">
    <? if(session('user') && !$this->voted) { ?>
      <a href="#" data-id="<?= $this->post->id ?>" data-vote="up" class="up"><i class="icon-arrow-up"></i></a>
      <!-- <a href="#" data-id="<?= $this->post->id ?>" data-vote="down" class="down"><i class="icon-arrow-down"></i></a> -->
    <? } ?>
  </td>
  <td>
    <div class="title"><a href="<?= $this->post->href ?>"><?= $this->post->title ?: preg_replace('/^https?:\/\//', '', $this->post->href) ?></a></div>
    <div class="details"><?= $this->post->domain ?> | 
      <span class="points"><?= $this->post->points ?> point<?= $this->post->points == 1 ? '' : 's' ?></span> | 
      <?= date('Y-m-d H:i T', strtotime($this->post->date_submitted)) ?> (<?= TimeAgo::inWords($this->post->date_submitted) ?>) | 
      <a href="/post/<?= $this->post->id ?>"><?= $this->post->comments ?: '' ?> comment<?= $this->post->comments == 1 ? '' : 's' ?></a>
    </div>
  </td>
</tr>

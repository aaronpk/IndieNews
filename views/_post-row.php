<tr class="post">
  <td class="position"><?= $this->position ?>.</td>
  <td class="vote">
    <? if(session('user')) { ?>
    <a href="#" data-id="<?= $this->post->id ?>" data-vote="up" class="up post_<?= $this->post->id ?>"><i class="icon-arrow-up"></i></a>
    <!-- <a href="#" data-id="<?= $this->post->id ?>" data-vote="down" class="down post_<?= $this->post->id ?>"><i class="icon-arrow-down"></i></a> -->
    <? } ?>
  </td>
  <td>
    <div class="title"><a href="<?= $this->post->href ?>"><?= $this->post->title ?: preg_replace('/^https?:\/\//', '', $this->post->href) ?></a></div>
    <div class="details"><?= $this->post->domain ?> | <?= $this->post->points ?> point<?= $this->post->points == 1 ? '' : 's' ?> | <?= $this->post->date_submitted ?><!-- | <a href="/post/<?= $this->post->id ?>"><?= $this->post->comments ?> comment<?= $this->post->comments == 1 ? '' : 's' ?></a> --></div>
  </td>
</tr>

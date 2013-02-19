<tr class="post">
  <td class="position"><?= $this->position ?>.</td>
  <td class="vote">
    <a href="/vote?post=<?= $this->post->id ?>&amp;vote=up" class="up"><i class="icon-arrow-up"></i></a>
    <a href="/vote?post=<?= $this->post->id ?>&amp;vote=down" class="down"><i class="icon-arrow-down"></i></a>
  </td>
  <td>
    <div class="title"><a href="<?= $this->post->href ?>"><?= $this->post->title ?: preg_replace('/^https?:\/\//', '', $this->post->href) ?></a></div>
    <div class="details"><?= $this->post->domain ?> | <?= $this->post->points ?> point<?= $this->post->points == 1 ? '' : 's' ?> | <?= $this->post->date_submitted ?> | <a href="/post/<?= $this->post->id ?>"><?= $this->post->comments ?> comment<?= $this->post->comments == 1 ? '' : 's' ?></a></div>
  </td>
</tr>

<!doctype html>
<html lang="<?= $lang ?>">
<head>
  <title><?= $title ?></title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="description" content="IndieNews">
  <?php if(isset($lang)): ?>
  <link rel="webmention" href="<?= Config::$baseURL ?>/<?= $lang ?>/webmention" />
  <?php endif; ?>

  <?= $meta ?>

  <!--[if lt IE 9]>
  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.min.css">
  <link rel="stylesheet" href="/css/style.css">

  <script src="/js/jquery-1.7.1.min.js"></script>
  <script src="/js/jquery.timeago.js"></script>
  <script src="/js/timeago/jquery.timeago.<?= $lang ?>.js"></script>
</head>
<body>

<div class="navbar navbar-static-top">
  <div class="navbar-inner">
    <a class="brand" href="/">IndieNews</a>
    <ul class="nav">
      <li><a href="/<?= $lang ?>"><?= __('Home') ?></a></li>
      <li><a href="/<?= $lang ?>/submit"><?= __('Submit') ?></a></li>
      <li><a href="/<?= $lang ?>/members"><?= __('Members') ?></a></li>
    </ul>
  </div>
</div>

<div class="page">

  <?= $content ?>

  <div class="footer">
    <ul class="nav-footer">
      <li><a href="/<?= $lang ?>/submit"><?=__('About IndieNews')?></a></li>
      <li><a href="/how-to-submit-a-post"><?= __('How to Submit a Post') ?></a></li>
    </ul>
    <p class="credits">&copy; <?= date('Y') ?> by <a href="http://aaronparecki.com">Aaron Parecki</a>.
      IndieNews <?= __('is part of') ?> <a href="https://indieweb.org/">IndieWebCamp</a>.
      <?= __('This code is {0}open source{1}.', ['<a href="https://github.com/aaronpk/IndieNews">', '</a>']) ?>
      <?= __('Feel free to send a pull request, or {0}file an issue{1}.', ['<a href="https://github.com/aaronpk/IndieNews/issues">', '</a>']) ?></p>
  </div>
</div>

<script>
jQuery(function($){
  $("time.timeago").timeago();
});
</script>
</body>
</html>

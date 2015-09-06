<!doctype html>
<html lang="en">
  <head>
    <title><?= $this->title ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="IndieNews">
    <link rel="pingback" href="https://webmention.io/webmention?forward=<?= Config::$baseURL ?>/webmention" />
    <link rel="webmention" href="<?= Config::$baseURL ?>/webmention" />

    <?= $this->meta ?>

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="/css/style.css">

    <script src="/js/jquery-1.7.1.min.js"></script>
  </head>

<body>

<div class="navbar navbar-static-top">
  <div class="navbar-inner">
    <a class="brand" href="/">IndieNews</a>
    <ul class="nav">
      <li><a href="/">Home</a></li>
      <li><a href="/submit">Submit</a></li>
    </ul>
    <? if(session('user')) { ?>
      <ul class="nav pull-right">
        <li><a href="<?= session('user') ?>"><?= session('user') ?></a></li>
        <li><a href="/signout">Sign Out</a></li>
      </ul>
    <? } else { ?>
      <ul class="nav pull-right" style="font-size: 8pt;">
        <li><a href="https://indieauth.com/setup">What's This?</a></li>
      </ul>
      <form action="https://indieauth.com/auth" method="get" class="navbar-form pull-right">
        <input type="text" name="me" placeholder="yourdomain.com" class="span2" />
        <button type="submit" class="btn">Sign In</button>
        <input type="hidden" name="redirect_uri" value="<?= Config::$baseURL ?>/indieauth" />
      </form>
    <? } ?>
  </div>
</div>

<div class="page">

  <?= $this->fetch($this->page . '.php') ?>

  <div class="footer">
    <ul class="nav-footer">
      <li><a href="/submit">About IndieNews</a></li>
      <li><a href="/how-to-submit-a-post">How to Submit a Post</a></li>
    </ul>
    <p class="credits">&copy; <?=date('Y')?> by <a href="http://aaronparecki.com">Aaron Parecki</a>.
      IndieNews is part of <a href="http://indiewebcamp.com/">IndieWebCamp</a>. 
      This code is <a href="https://github.com/aaronpk/IndieNews">open source</a>. 
      Feel free to send a pull request, or <a href="https://github.com/aaronpk/IndieNews/issues">file an issue</a>.</p>
  </div>
</div>

</body>
</html>

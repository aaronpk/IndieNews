<!doctype html>
<html lang="en">
  <head>
    <title><?= $this->title ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="IndieNews">
    <link rel="pingback" href="http://pingback.me/webmention?forward=http://<?=$_SERVER['SERVER_NAME']?>/webmention" />
    <link rel="http://webmention.org/" href="http://<?=$_SERVER['SERVER_NAME']?>/webmention" />

    <?= $this->meta ?>

    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="/css/style.css">

    <script src="/js/jquery-1.7.1.min.js"></script>
    <script src="/js/vote.js"></script>
  </head>

  <body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script>
  window.fbAsyncInit = function() {
    FB.Event.subscribe('edge.create', function(targetUrl) {
      _gaq.push(['_trackSocial', 'facebook', 'like', targetUrl]);
    });
    FB.Event.subscribe('edge.remove', function(targetUrl) {
      _gaq.push(['_trackSocial', 'facebook', 'unlike', targetUrl]);
    });
    FB.Event.subscribe('message.send', function(targetUrl) {
      _gaq.push(['_trackSocial', 'facebook', 'send', targetUrl]);
    });
  };
</script>


<div class="navbar navbar-static-top">
  <div class="navbar-inner">
    <a class="brand" href="/">IndieNews</a>
    <ul class="nav">
      <li><a href="/newest">New</a></li>
      <li><a href="/submit">Submit</a></li>
    </ul>
    <ul class="nav pull-right">
      <? if(session('user')) { ?>
        <li><a href="/user?domain=<?= session('user') ?>"><?= session('user') ?></a></li>
        <li><a href="/signout">Sign Out</a></li>
      <? } else { ?>
        <li><a href="/signin">Sign In</a></li>
      <? } ?>
    </ul>
  </div>
</div>

<div class="page">

  <?= $this->fetch($this->page . '.php') ?>

  <hr />

  <div class="footer">
    &copy; <?=date('Y')?> by <a href="http://aaronparecki.com">Aaron Parecki</a>
  </div>
</div>


  </body>
</html>

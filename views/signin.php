<h2>Sign in with your domain</h2>

<form action="http://indieauth.com/auth" method="get">
  <label>Web Address:</label>
  <input type="text" name="me" placeholder="yourdomain.com" />
  <p><button type="submit" class="btn">Sign In</button></p>
  <input type="hidden" name="redirect_uri" value="http://<?= $_SERVER['SERVER_NAME'] ?>/indieauth" />
</form>
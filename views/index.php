<div class="hero-unit" style="padding: 40px;">
  <h2>IndieNews</h2>
  <p><?= __('IndieNews is a community-curated list of articles relevant to the {0}Indie Web{1}.', ['<a href="https://indieweb.org/why">', '</a>']) ?></p>
</div>

<div class="languages">
  <?php foreach(supportedLanguages() as $l=>$lang): ?>
    <div>
      <a href="/<?= $l ?>"><?= $lang ?></a>
    </div>
  <?php endforeach; ?>
</div>

<div class="row" style="margin-top: 20px;">
  <div class="span12">
    <p>Want to see a new language? You can add a translation as a pull request! Copy the <a href="https://github.com/aaronpk/IndieNews/blob/main/Locale/de/default.po">Locale</a> file to a new folder, fill it out for your language, and submit a pull request!</p>
  </div>
</div>

<style type="text/css">
.languages {
  display: flex; 
  flex-direction: row;
}
.languages div {
  flex: 1 0;
  background: #eee;
  margin-bottom: 10px;
  margin-left: 4px;
  margin-right: 4px;
  border-radius: 10px;
  text-align: center;
  font-size: 18pt;
  float: left;
}
.languages div:first-child {
  margin-left: 0;
}
.languages div:last-child {
  margin-right: 0;
}
.languages a {
  margin: 40px 20px;
  display: inline-block;
}
@media(max-width: 790px) {
  .languages {
    display: block;
  }
  .languages div {
    display: block;
    float: none;
    flex: none;
  }
  .languages a {
    margin: 30px 20px;
  }
}
</style>

<?php
class Config {
  public static $debug = false;

  public static $baseURL = 'https://news.indieweb.org';

  public static $dbHost = '127.0.0.1';
  public static $dbName = 'indienews';
  public static $dbUsername = 'indienews';
  public static $dbPassword = '';

  public static $hubURL = 'https://switchboard.p3k.io/';

  // Sends to https://github.com/aaronpk/TikTokBot
  public static $ircURL = 'http://example.com/message';
  public static $ircToken = '';
  public static $ircChannel = '#indieweb';
}

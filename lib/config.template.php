<?php
class Config {
  public static $debug = false;

  public static $baseURL = 'https://news.indieweb.org';

  public static $dbHost = '127.0.0.1';
  public static $dbName = 'indienews';
  public static $dbUsername = 'indienews';
  public static $dbPassword = '';

  // Requests from these proxies have their X-Forwarded-For header trusted
  public static $trustedProxies = [];
  // Visitors from these IPs are treated as logged in (in addition to the Remote-User header).
  // Use 'ip' => 'https://example.com/' to treat them as that user, e.g. for admin access.
  public static $loggedInIPs = [];

  public static $hubURL = 'https://switchboard.p3k.io/';

  // Sends to https://github.com/aaronpk/TikTokBot
  public static $ircURL = 'http://example.com/message';
  public static $ircToken = '';
  public static $ircChannel = '#indieweb';
}

<?php

class TimeAgo
{
  
  /**
   * Time Helper class file.
   *
   * PHP versions 4 and 5
   *
   * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
   * Copyright 2005-2007, Cake Software Foundation, Inc.
   *                1785 E. Sahara Avenue, Suite 490-204
   *                Las Vegas, Nevada 89104
   *
   * Licensed under The MIT License
   * Redistributions of files must retain the above copyright notice.
   *
   * @filesource
   * @copyright   Copyright 2005-2007, Cake Software Foundation, Inc.
   * @link        http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
   * @package     cake
   * @subpackage    cake.cake.libs.view.helpers
   * @since     CakePHP(tm) v 0.10.0.1076
   * @version     $Revision: 4410 $
   * @modifiedby    $LastChangedBy: phpnut $
   * @lastmodified  $Date: 2007-02-02 07:31:21 -0600 (Fri, 02 Feb 2007) $
   * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
   *
   * Returns either a relative date or a formatted date depending
   * on the difference between the current time and given datetime.
   * $datetime should be in a <i>strtotime</i>-parsable format, like MySQL's datetime datatype.
   *
   * Relative dates look something like this:
   *  3 weeks, 4 days ago
   *  15 seconds ago
   * Formatted dates look like this:
   *  on 02/18/2004
   *
   * The returned string includes 'ago' or 'on' and assumes you'll properly add a word
   * like 'Posted ' before the function output.
   *
   * @param string $date_string Datetime string or Unix timestamp
   * @param string $format Default format if timestamp is used in $date_string
   * @param string $countFrom Calculate time difference from this date. Defaults to time()
   * @param string $backwards False if $date_string is in the past, true if in the future
   * @return string Relative time string.
   */
  public static function inWords($datetime_string, $format = 'n/j/y', $since_string = 'now', $backwards = false, $components = 1)
  {
    $datetime = strtotime($datetime_string);
  
    if( $since_string == 'now' )
      $since = time();
    else
      $since = strtotime($since_string);
  
    $in_seconds = $datetime;
  
    if ($backwards) {
      $diff = $in_seconds - $since;
    } else {
      $diff = $since - $in_seconds;
    }
  
    $months = floor($diff / 2419200);
    $diff -= $months * 2419200;
    $weeks = floor($diff / 604800);
    $diff -= $weeks * 604800;
    $days = floor($diff / 86400);
    $diff -= $days * 86400;
    $hours = floor($diff / 3600);
    $diff -= $hours * 3600;
    $minutes = floor($diff / 60);
    $diff -= $minutes * 60;
    $seconds = $diff;
  
    //if ($months > 0) {
    if (false && $months > 0 && $since_string == 'now') {
      // over a month old, just show date (mm/dd/yyyy format)
      $relative_date = 'on ' . date($format, $in_seconds);
      $old = true;
    } else {
      $relative_date = '';
      $old = false;
  
      if ($months > 6) {
        // months only
        $relative_date .= ($relative_date ? ', ' : '') . $months . ' months';
      } elseif ($months > 0) {
        // months and weeks
        $relative_date .= ($relative_date ? ', ' : '') . $months . ' month' . ($months > 1 ? 's' : '');
        if($components > 1)
          $relative_date .= $weeks > 0 ? ($relative_date ? ', ' : '') . $weeks . ' week' . ($weeks > 1 ? 's' : '') : '';
      } elseif ($weeks > 0) {
        // weeks and days
        $relative_date .= ($relative_date ? ', ' : '') . $weeks . ' week' . ($weeks > 1 ? 's' : '');
        if($components > 1)
          $relative_date .= $days > 0 ? ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '') : '';
      } elseif($days > 0) {
        // days and hours
        $relative_date .= ($relative_date ? ', ' : '') . $days . ' day' . ($days > 1 ? 's' : '');
        if($components > 1)
          $relative_date .= $hours > 0 ? ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '') : '';
      } elseif($hours > 0) {
        // hours and minutes
        $relative_date .= ($relative_date ? ', ' : '') . $hours . ' hour' . ($hours > 1 ? 's' : '');
        if($components > 1)
          $relative_date .= $minutes > 0 ? ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '';
      } elseif($minutes > 0) {
        // minutes only
        $relative_date .= ($relative_date ? ', ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
      } else {
        // seconds only
        $relative_date .= ($relative_date ? ', ' : '') . $seconds . ' second' . ($seconds != 1 ? 's' : '');
      }
    }
  
    $ret = $relative_date;
    // show relative date and add proper verbiage
    if (!$backwards && !$old && $since_string == 'now') {
      $ret .= ' ago';
    }
    return $ret;
  }
}

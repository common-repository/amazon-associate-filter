<?php
/*
Plugin Name: Amazon Associate Filter 
Plugin URI: 
Description: Scrubs amazon links and replaces (or adds) the affiliate ID set in Amazon Aff options. <b>IMPORTANT NOTE</b>: the default affiliate ID is my affiliate ID. You <i>MUST UPDATE THE AFFILIATE ID USING THE PLUGIN SETTINGS PAGE</i>; otherwise any affiliate sales will be paid out to my account.
Author: Rajan Agaskar
Version: 0.4
Author URI: http://agaskar.com
 */

/*
 Copyright 2008 Rajan Agaskar
 
     This program is free software: you can redistribute it and/or modify
     it under the terms of the GNU General Public License as published by
     the Free Software Foundation, either version 3 of the License, or
     (at your option) any later version.

     This program is distributed in the hope that it will be useful,
     but WITHOUT ANY WARRANTY; without even the implied warranty of
     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     GNU General Public License for more details.

     You should have received a copy of the GNU General Public License
     along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */


add_filter('the_content','filter_amazon_associate_filter');
add_filter('comment_text','filter_amazon_associate_filter');

function filter_amazon_associate_filter($content) {
  $affiliate_code=get_option('amazon_associate_filter_id');

  $content=preg_replace(
    '/http:\/\/[^>]*?amazon.([^\/]*)\/([^>]*?ASIN|gp\/product|exec\/obidos\/tg\/detail\/-|[^>]*?dp)\/([0-9a-zA-Z]{10})[a-zA-Z0-9#\/\*\-\?\&\%\=\,\._;]*/i',
    'http://www.amazon.$1/dp/$3/?tag='.$affiliate_code,
    $content
  );
  return $content;
}

function set_amazon_associate_filter_options () {
  add_option("amazon_associate_filter_id","agaskarcom-20","Your Amazon Associate Code");
}
function modify_menu_amazon_associate_filter () {
  add_options_page(
    'Amazon Associate Config',         //Title
    'Amazon Associate Config',         //Sub-menu title
    'manage_options', //Security
    __FILE__,         //File to open
    'amazon_associate_filter_options'  //Function to call
  );  
}
function amazon_associate_filter_options () {
  echo '<div class="wrap"><h2>Amazon Associate ID Configuration</h2>';
  if ($_REQUEST['submit']) {
    update_amazon_associate_filter_options();
  }
  print amazon_associate_filter_form();
  echo '</div>';
}
function update_amazon_associate_filter_options() {
  $updated = false;
  if ($_REQUEST['amazon_associate_filter_id']) {
    update_option('amazon_associate_filter_id', $_REQUEST['amazon_associate_filter_id']);
    $updated = true;
  }
  if ($updated) {
    echo '<div id="message" class="updated fade">';
    echo '<p>Configuration Updated</p>';
    echo '</div>';
  } else {
    echo '<div id="message" class="error fade">';
    echo '<p>Unable to update options</p>';
    echo '</div>';
  }
}

function amazon_associate_filter_form () {
  $amazon_ass_id = get_option('amazon_associate_filter_id');
  $form='<form method="post">
    <label>Enter your Amazon Associate ID:
    <input type="text" name="amazon_associate_filter_id" value="'.$amazon_ass_id.'" />
    </label>
    <br />
    <input type="submit" name="submit" value="Submit" />
    </form>';
  return $form;
}

add_action('admin_menu','modify_menu_amazon_associate_filter');
register_activation_hook(__FILE__,"set_amazon_associate_filter_options");
register_deactivation_hook(__FILE__,"unset_amazon_associate_filter_options");

?>

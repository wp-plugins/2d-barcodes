<?php
/*
Plugin Name: 2D Barcodes
Plugin URI: http://www.tagsolute.de/developer/wordpress-plugin.html
Description: Inserts a 2D Barcode
Version: 1.0.1
Author: Christian Doerfel
Author URI: http://www.tagsolute.de
*/


add_action('admin_menu', 'barcode_config_page');
function barcode_config_page() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('2DBarcodes'), __('2DBarcodes'), 'manage_options',  __FILE__, 'barcodes_options_subpanel');

}

###pda detection;
### thanks 
function getBrowserAgentsToDetect(){
	$defaultUserAgents = "Elaine/3.0, iPhone, iPod, Palm, EudoraWeb, Blazer, AvantGo, Windows CE, Cellphone, Small, MMEF20, Danger, hiptop, Proxinet, ProxiNet, Newt, PalmOS, NetFront, SHARP-TQ-GX10, SonyEricsson, SymbianOS, UP.Browser, UP.Link, TS21i-10, MOT-V, portalmmm, DoCoMo, Opera Mini, Palm, Handspring, Nokia, Kyocera, Samsung, Motorola, Mot, Smartphone, Blackberry, WAP, SonyEricsson, PlayStation Portable, LG, MMP,OPWV, Symbian, EPOC";
	
	$browserAgents = $defaultUserAgents;
	    if(!empty($browserAgents)){
	        $browserAgents = explode(',',$browserAgents);
	        if(!empty($browserAgents)){
	           foreach ($browserAgents as $key => $value){
	               $browserAgents[$key] = trim($value);
	           }
	           return $browserAgents;
	        }
	    }
	    return array();
	}
	
	function detectPDA(){
		$browserAgent = $_SERVER['HTTP_USER_AGENT'];
		
		$userAgents = getBrowserAgentsToDetect();
		foreach ( $userAgents as $userAgent ) {
		if(eregi($userAgent,$browserAgent)){
			    return true;
		}
		}
		return false;
	}	
	



function barcodes_add_options() {
  if(function_exists('add_options_page')){
    add_options_page('barcodes', 'barcodes', 9, basename(__FILE__), 'barcodes_options_subpanel');
 }
}



switch($_POST['action']){
case 'Save':



  if(isset($_POST['betatest'])) update_option('barcodes_betatest', "yes");
  else delete_option('barcodes_betatest');
  if(isset($_POST['notme'])) update_option('barcodes_notme', "yes");
  else delete_option('barcodes_notme');
  if(isset($_POST['googtestmode'])) update_option('barcodes_googtestmode', "yes");
  else delete_option('barcodes_googtestmode');




  update_option('devkey', $_POST['devkey']);
  update_option('barcodes_lra', $_POST['barcodes_pos']);



  if($_POST['home'] == "on") update_option('barcodes_home', "checked=on");
  else update_option('barcodes_home', "");
  if($_POST['page'] == "on") update_option('barcodes_page', "checked=on");
  else update_option('barcodes_page', "");
  if($_POST['post'] == "on") update_option('barcodes_post', "checked=on");
  else update_option('barcodes_post', "");
  if($_POST['cat'] == "on") update_option('barcodes_cat', "checked=on");
  else update_option('barcodes_cat', "");
  if($_POST['archive'] == "on") update_option('barcodes_archive', "checked=on");
  else update_option('barcodes_archive', "");

  break;
}

function barcodes_options_subpanel(){

  $value_devkey = get_option('devkey');
  $ad_channel = get_option('barcodes_channel');
  $lra = get_option('barcodes_lra');
?>
<div class="wrap">
  <h2><?php _e('barcodes', 'wpai') ?></h2>
  <form name="form1" method="post">
	<input type="hidden" name="stage" value="process" />

	<fieldset class="options">
		<legend><?php _e('Options', 'wpai') ?></legend>

	<b>How to Use</b><br>
	<ul>
	<li>Get a Tagsolute DevKey at http://www.tagsolute.de/profil.</li>
		<li>Enter your account details in the boxes below.</li>


		<table width="100%" cellspacing="2" cellpadding="5" class="editform" >
		  <tr align="left" valign="top">

    <tr>
			<th width="30%" scope="row" style="text-align: left"><?php _e('Tagsolute DevKey', 'wapi') ?></th>
			<td><input type="text" name="devkey" id="devkey" style="width: 80%;" cols="50" value="<?php echo $value_devkey; ?>"></td></tr>


    <tr valign="top">
			<th width="30%" scope="row" style="text-align: left"><?php _e('2DBarcode Positioning', 'wpai') ?></th>
      <td>
      <select name='barcodes_pos'>
      <option value="left" <?php if($lra == 'left') echo "SELECTED"; ?> >left
      <option value="right" <?php if($lra == 'right') echo "SELECTED"; ?> >right
      <option value="top" <?php if($lra == 'top') echo "SELECTED"; ?> >top
      <option value="bottom" <?php if($lra == 'bottom') echo "SELECTED"; ?> >bottom
      </select></td>
		  <tr valign="top">
			<th width="30%" scope="row" style="text-align: left"><?php _e("Don't Show On These Pages", 'wpai') ?></th>

			<td>
			<INPUT TYPE=CHECKBOX NAME="home" <?php echo get_option('barcodes_home'); ?>>home page<BR>
			<INPUT TYPE=CHECKBOX NAME="page" <?php echo get_option('barcodes_page'); ?>>static pages<BR>
			<INPUT TYPE=CHECKBOX NAME="post" <?php echo get_option('barcodes_post'); ?>>post pages<BR>
			<INPUT TYPE=CHECKBOX NAME="cat" <?php echo get_option('barcodes_cat'); ?>>category pages<BR>
			<INPUT TYPE=CHECKBOX NAME="archive" <?php echo get_option('barcodes_archive'); ?>>archive pages<BR>
      </td>
      </tr>
    <tr>
    <td colspan=5><input name=betatest type=checkbox <?php if(get_option("barcodes_betatest") == "yes") echo "checked"; ?>>Only let me see the barcodes!</td>
    </tr>

    <tr>
      <td align=right colspan=5>
      <input type="submit" name="action" value="<?php _e('Save', 'wpai') ?>" />
      </td>
    </tr>
    <tr>
    <td colspan=5>
	<b>Notes</b><br>
	<ul><li>If you don't want to show any 2D Barcodes on a specific post, put &lt;!--no2dbarcode--&gt; in the post.</li>
	<li>If you want 2D Barcodes to start below a certain point, put &lt;!--barcodestart--&gt; at that point.</li>
	</ul>
    </td>
    </tr>

		</table>
	</fieldset>


  </form>
</div>
<?php

}
###doerfel ende admin bereich



add_action('admin_menu', 'barcodes_add_options');

function barcodes_install() {

  if(get_option('barcodes_client') == "") {
   update_option('barcodes_lra', "right");
  }
}
if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
   add_action('init', 'barcodes_install');
}

function barcodes_pickalign($tag){
  if($tag == "left")
    return '<div style="float: left;">';
  if($tag == "right")
    return '<div style="float: right;">';
  if(($tag == "top") || ($tag == "bottom"))
    return '<div>';
  else
    return '<div style="float: right;">';
}




###doerfel insert code!!


function barcodes_genadcode(){
  global $user_level;
  $devkey = get_option('devkey');

define("TAGSOLUTE_DEV_KEY",  $devkey);
define("TAGSOLUTE_API_URL", "http://www.tagsolute.de/cgi-bin/api.cgi");
require_once("json/JSON.php");
require_once("api.php");

$mParams = array("sUrl" => get_permalink());

	$mResult = mxApiCall("GetRedirectTag", $mParams);
	if($mResult["bSuccessful"]) {
		$TagsoluteImg='<img src="'.$mResult["jResult"].'">';;
		}
		else {
			$mResult["jResult"]["sInfo"];
		}

		print "\n";


	$TagsoluteDiv.=$TagsoluteImg;

	$retstr=$TagsoluteImg;


  return $retstr;





}

$barcodes_adsused = 0;

function barcodes_the_content($content){
global $doing_rss;
	$content_doerfel=$content;
 ### doerfel auschluss von barcodes



  if(is_feed() || $doing_rss)
    return $content;
  if(strpos($content, "<!--no2dbarcode-->") !== false) return $content;
  if( detectPDA()== true) return $content;	
  if(is_home() && get_option('barcodes_home') == "checked=on") return $content;
  if(is_page() && get_option('barcodes_page') == "checked=on") return $content;
  if(is_single() && get_option('barcodes_post') == "checked=on") return $content;
  if(is_category() && get_option('barcodes_cat') == "checked=on") return $content;
  if(is_archive() && get_option('barcodes_archive') == "checked=on") return $content;

  global $barcodes_adsused, $user_level;
  if(get_option('barcodes_betatest') == "yes" && $user_level < 8)
    return $content;
  if(get_option('barcodes_notme') == "yes" && $user_level > 8)
    return $content;

  $numads = get_option('barcodes_nads');
  if(is_single())
    $numads = get_option('barcodes_nadspp');

  $content_hold = "";
  if(strpos($content, "<!--barcodestart-->") !== false){
    $content_hold = substr($content, 0, strpos($content, "<!--barcodestart-->"));
    $content = substr_replace($content, "", 0, strpos($content, "<!--barcodestart-->"));
  }

### ende doerfel auschluss von barcodes



    $poses = array();
    $lastpos = -1;
    $repchar = "<p";
    if(strpos($content, "<p") === false)
      $repchar = "<br";

    while(strpos($content, $repchar, $lastpos+1) !== false){
      $lastpos = strpos($content, $repchar, $lastpos+1);
      $poses[] = $lastpos;
    }

    //cut the doc in half so the ads don't go past the end of the article.  It could still happen, but what the hell
    $half = sizeof($poses);
    $adsperpost = $barcodes_adsused+1;
    if(!is_single())
      $half = sizeof($poses)/2;

    while(sizeof($poses) > $half)
      array_pop($poses);

    $pickme = $poses[rand(0, sizeof($poses)-1)];

    $replacewith = barcodes_pickalign(get_option('barcodes_lra'));
    $replacewith .= barcodes_genadcode()."</div>";
	if (get_option('barcodes_lra')=="bottom") {
	 	return $content_doerfel.barcodes_genadcode()."</div>";
	} else {

    $content = substr_replace($content, $replacewith.$repchar, $pickme, 2);
	}


  return $content_hold.$content;
}

add_filter('the_content', 'barcodes_the_content');

?>
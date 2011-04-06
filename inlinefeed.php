<?php

/*
Plugin Name: InlineFeed
Plugin URI: http://kruyt.org/projects/wp-plugins/inlinefeed
Description: List a RSS-Feed in your WP-Blog, only headlines or with description, now uses the WP 2.5 shortcode.
Author: Dennis Kruyt
Version: 2.00
License: GPL
Author URI: http://kruyt.org
Update Server: http://kruyt.org/projects/wp-plugins/inlinefeed
Min WP Version: 2.5
Max WP Version: 2.5.1
*/ 

// Add a new shortcode for inlinefeed
add_shortcode('inlinefeed', 'inlinefeed_func');

require_once(ABSPATH.'wp-includes/rss-functions.php');

define('MAGPIE_CACHE_ON', false);

/* Optional Plugin Config */
/* Do use php < 4.2, then kill the next comment */
//include(MAGPIE_DIR.'array_change_key_case.php');

function RSSImport($display,$feedurl,$displaydescriptions,$truncatetitle,$newwindow) {

    $string = "<!-- start of RSS feed content by InlineFeed plugin v2.00 - http://kruyt.org -->\n";

  if ( $feedurl == 'undefined' ) {
  	$string .="This is the inlinefeed plugin speaking: please define the rss_feed_url, for help read the readme.txt or take a look at <a href=\"http://kruyt.org/projects/wp-plugins/inlinefeed\">http://kruyt.org/projects/wp-plugins/inlinefeed</a>";
   } else {
   $rss = fetch_rss( $feedurl );
	 // Display a nice error if the feed. Credits to Paul M - http://themineshaftgap.com 
	 if ($rss == false){ 
	    $string .= "[InlineFeed plugin cant retrieve: ".substr($feedurl,0,40)."...]"; 
	    return $string; 
	 }

		$string .= wptexturize("<br /><b><a href=" . $rss->channel['link'] . ">" . $rss->channel['title'] . ": " . $rss->channel['description'] . "</a></b><br />");
				foreach ($rss->items as $item) {
								if ($display == 0) {
								break;
								}
								$href = $item['link'];
								$desc = trim($item['description']);
								$item['fulltitle']=$item['title'];
									$umlaute = array('ä','ö','ü','ß','Ä','Ö','Ü','»','«',"â€œ","â€?","â€™","â€“","â€”","â€¦","&nbsp;",chr(196), chr(228),chr(214),chr(246),chr(220),chr(252),chr(223),chr(175),chr(174),utf8_encode('Ä'),utf8_encode('ä'),utf8_encode('Ö'),utf8_encode('ö'),utf8_encode('Ü'),utf8_encode('ü'),utf8_encode('ß'),utf8_encode('?'),utf8_encode('»'),utf8_encode('«'));
									$htmlcode = array('&auml;','&ouml;','&uuml;','&szlig;','&Auml;','&Ouml;','&Uuml;','&raquo;','&laquo;','"','"','´','–','—','...',' ','&raquo;','&laquo;','&Auml;','&auml;','&Ouml;','&ouml;','&Uuml;','&uuml;','&szlig;','&Auml;','&auml;','&Ouml;','&ouml;','&Uuml;','&uuml;','&szlig;','?','&raquo;','&laquo;');
									$item = str_replace($umlaute, $htmlcode, $item);
									$desc = str_replace($umlaute, $htmlcode, $desc);
								if($truncatetitle == 'true' ){
										if(strlen($item['title'])>55)
												{
														$item['title']=substr($item['title'],0,55)." ... ";
												}
								}
								if($newwindow == 'true' || $newwindow == '1') {
									$target = "_blank";
								} else {
									$target = "_self";
								}
							$string .= wptexturize('<ul><li><a href="'.$href.'" target="'.$target.'" title="'.$item['fulltitle'].'" >'.$item['title'].'</a></li></ul>');
								// Uncomment following line to also display headline description with each headline
								if($displaydescriptions == 'true' && $desc<>"") {$string .= wptexturize("<div style=\"margin-left: 40px;\">");$string .= wptexturize($desc.'</div>');}
								$display--;
				}
		 }
    $string .= "\n<!-- End of RSS feed content -->\n";
    return $string;
}

// ###

function inlinefeed_func($atts) {
	
	extract(shortcode_atts(array(
	  'rss_feed_url' => 'undefined',
		'display' => '10',
		'displaydescriptions' => true,
		'truncatetitle' => false,
		'newwindow' => false,
		
	), $atts));

	return RSSImport($display,$rss_feed_url,$displaydescriptions,$truncatetitle,$newwindow);
}
						
?>
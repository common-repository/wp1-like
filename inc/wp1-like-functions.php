<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// sanitize string and array
function sanitize($string, $trim = false){
	if (is_array($string)){
		$strng = array();
		foreach($string as $strk => $strv){
			$string1 = filter_var($strv, FILTER_SANITIZE_STRING);
			$string1 = trim($string1);
			$string1 = stripslashes($string1);
			$string1 = strip_tags($string1);
			$string1 = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string1);
			if ($trim)
				$string1 = substr($string1, 0, $trim);
			
			$strng[$strk] = $string1;
		}
		$string = $strng;
	} else {
		$string = filter_var($string, FILTER_SANITIZE_STRING);
		$string = trim($string);
		$string = stripslashes($string);
		$string = strip_tags($string);
		$string = str_replace(array('‘', '’', '“', '”'), array("'", "'", '"', '"'), $string);
		if ($trim)
			$string = substr($string, 0, $trim);
	}
	return $string;
}

// de_sanitize string and array
function decode_sanitized($str){
	if (is_array($str)){
		$strng = array();
		foreach($str as $strk => $strv){
			$strng[$strk] = html_entity_decode( $strv, ENT_QUOTES );
		}
		return $strng;
	}else{
		return html_entity_decode( $str, ENT_QUOTES );
	}
}

function wp1_like_like_this_post(){
	$data = array();
	$wp1_like_button_text 			= get_option( 'wp1_like_button_text', 'Like' );
	$wp1_like_unlike_button_text	= $wp1_like_button_text; //get_option( 'wp1_like_unlike_button_text', 'Unlike' );
	$wp1_like_show_count 			= get_option( 'wp1_like_show_count', 'Yes' );
	if( isset($_POST['like_nonce']) ){
		if( sanitize_text_field($_POST['like_nonce']) !== wp_create_nonce('Like Post')  ){
			$data["error"] = __("Oops...!!! Something went wrong. Please try again later", 'wp1_like');
		} else {
			$postid = intval($_POST['pid']);
			$current_user = wp_get_current_user();
			$likes = get_post_meta($postid, 'wp1_like_like', false);
			if( !in_array($current_user->ID, $likes) ){
				if( $postid ){
					add_post_meta($postid, 'wp1_like_like', intval($current_user->ID));
					$likes = get_post_meta($postid, 'wp1_like_like', false);
					$count = ($wp1_like_show_count == 'Yes') ? '('. intval(count($likes)) .')' : '';
					$data["message"] = __($wp1_like_unlike_button_text.' '.$count, 'wp1_like');
					$data["disabled"] = "true";
				}
			}else{
				delete_post_meta($postid, 'wp1_like_like', intval($current_user->ID));
				$likes = get_post_meta($postid, 'wp1_like_like', false);
				$count = ($wp1_like_show_count == 'Yes') ? '('.count($likes).')' : '';
				$data["message"] = __($wp1_like_button_text.' '.$count, 'wp1_like');
				$data["disabled"] = "false";
			}			
		}
	}else{
		$data["error"] = __("Oops...!!! Something went wrong. Please try again later", 'wp1_like');
	}
	echo json_encode($data);
	die();
}
add_action('wp_ajax_wp1_like_like_this_post', 'wp1_like_like_this_post');
add_action('wp_ajax_nopriv_wp1_like_like_this_post', 'wp1_like_like_this_post'); 


function wp1like_enqueue_custom_script() {
    wp_enqueue_script( 'wp1-like-js', 'assets/js/wp1-like.js', array(), WP1_LIKE_VER, true );
    $ajax_like = 'jQuery(document).on("click", ".wp1_like_like", function(event){
             event.preventDefault();
             var likeBtn = jQuery(this);
             var pid = jQuery(this).attr("data-id");
             var likeBtnSpan = jQuery(this).find("span");
             var likeBtnText = likeBtnSpan.text();
             jQuery.ajax({
                 type:"POST",
                 url: "'.home_url().'/wp-admin/admin-ajax.php",
                 data: { pid:pid, action:"wp1_like_like_this_post", like_nonce:"'.wp_create_nonce('Like Post').'" },
                 beforeSend: function() { 
                     likeBtnSpan.text("Processing..."); 
                 },
                 success: function(response) {
                     var objf = JSON.parse(response);
                     console.log(objf);
                     if( objf.error ){
                         likeBtnSpan.text( likeBtnText );
                         jQuery( "<div><small>"+objf.error+"</small></div>" ).insertAfter( likeBtn );
                     }
                     if( objf.message ){
                         likeBtnSpan.text( objf.message );
                         if( objf.disabled == "true" )
                             likeBtn.removeClass("enabled").addClass("disabled");
                         else
                             likeBtn.removeClass("disabled").addClass("enabled");
                     }
                 },
                 //complete: function() { 
                 //	likeBtnSpan.text("Like"); 
                 //}
             });
         });';
    wp_add_inline_script( 'wp1-like-js', $ajax_like );
 }
 add_action( 'wp_enqueue_scripts', 'wp1like_enqueue_custom_script' );
 

 // Display like button on front end
function wp1_like_action_after_content($content) { 
	$wp1_like_button_text 		= get_option( 'wp1_like_button_text', 'Like' );
	//$wp1_like_unlike_button_text	= get_option( 'wp1_like_unlike_button_text', 'Unlike' );
	$wp1_like_thumb_icon 		= get_option( 'wp1_like_thumb_icon', 'fa-thumbs-up' );
	$wp1_like_show_count 		= get_option( 'wp1_like_show_count', 'Yes' );
	$wp1_like_post_types        = (get_option( 'wp1_like_post_types')!='') ? get_option( 'wp1_like_post_types' ) : array();
	$disabled					= "enabled";
	$likes 						= get_post_meta(get_the_ID(), 'wp1_like_like', false); 
	$current_user 				= wp_get_current_user();
	if( in_array($current_user->ID, $likes) ){
		$disabled = 'disabled';
	}
	$btn_text = $wp1_like_button_text; //( $disabled == 'disabled' ) ? $wp1_like_unlike_button_text : $wp1_like_button_text;
	$count = ($wp1_like_show_count == 'Yes') ? '('.count($likes).')' : '';
	if( in_array(get_post_type(), $wp1_like_post_types) ){
		$new_content = __($content, 'wp1_like').'<a class="wp1_like_like '.$disabled.'" data-id="'.get_the_ID().'"><i class="fa '.$wp1_like_thumb_icon.'" aria-hidden="true"></i> <span>'.$btn_text.' '.$count.'</span></a>';
	}else{
		$new_content = __($content, 'wp1_like');
	}
	return $new_content;
}
add_action( 'the_content', 'wp1_like_action_after_content'); 
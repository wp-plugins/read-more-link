<?php
/**
 * @package Read more link
 * @version 0.5
 */
/*
Plugin Name: Read more link
Plugin URI: http://wordpress.org/extend/plugins/read-more-link/
Description: This plugin allows you to use a "[more...]" tag in a post to display a preview of it on the homepage.
The preview will display the text of the post, up to the "[more...]" tag, while the rest will be replaced by a "Read more..." link (which redirects the user to the post detail).
When viewing the post detail, the tag is removed automatically and the original, full post is displayed.
In the plugin settings you can easily configure the text of the "Read more..." link and the number of line breaks ("<br />" tags) displayed before the link.
Author: Luca Dioli
Version: 0.5
Author URI: http://lucadioli.com/
*/

$options = array(
	'read_more_link_text' => 'Read more...',
	'read_more_link_br' => 2
);


/* Settings functions */

// Add settings link on plugin page
function your_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=read-more-link/functions.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

function baw_create_menu() {
	//create new top-level menu
	//add_menu_page('Read more link Settings', 'Read more link', 'administrator', __FILE__, 'baw_settings_page',plugins_url('/images/icon.png', __FILE__));

	add_options_page('Read more link Settings', 'Read more link', 'administrator', __FILE__, 'baw_settings_page');
	
	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
	register_setting( 'read-more-links-settings-group', 'read_more_link_text' );
	register_setting( 'read-more-links-settings-group', 'read_more_link_br' );
}

function rmlGetOptions($key){
	$o = get_option($key);
	if(empty($o)) return $options[$key];
	else return get_option($key);
}

function baw_settings_page() {
	?>
	<div class="wrap">
		<h2>Read more link settings</h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'read-more-links-settings-group' ); ?>
			<?php //do_settings( 'read-more-links-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Read more link text:</th>
					<td><input type="text" name="read_more_link_text" value="<?php echo rmlGetOptions('read_more_link_text'); ?>" /></td>
				</tr>
				<!--<tr valign="top">
					<th scope="row">"&lt;br /&gt;" before link:</th>
					<td>
						<select name="read_more_link_br">
							<?php for($i=0;$i<5;$i++) echo '<option value="'.$i.'"'.((rmlGetOptions('read_more_link_br') == $i) ? ' selected="selected"' : '').'>'.$i.'</option>'; ?>
						</select>
					</td>
				</tr>-->
				<tr valign="top">
					<th scope="row">"&lt;br /&gt;" before link:</th>
					<td>
						<?php for($i=0;$i<4;$i++) echo $i.' <input type="radio" name="read_more_link_br" value="'.$i.'"'.((rmlGetOptions('read_more_link_br') == $i) ? ' checked="checked"' : '').' /><br />'; ?>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php 
}


/* Plugin functions */

function add_more_link($content){
	$tag = "[more...]";
	if(is_home()){
		$pos = stripos($content,$tag);
		if ($pos !== false) {
			$content = substr($content,0,$pos);
			$content .= get_more_link();
		}
	}
	else{
		$content = str_replace($tag,null,$content);
	}
	return $content;
}

function get_more_link(){
	$link = get_option('read_more_link_text');
	$br = '';
	if(get_option('read_more_link_br') > 0){
		for($i=0;$i<get_option('read_more_link_br');$i++){
			$br .= '<br />';
		}
	}
	
	return $br.'<a href="'.get_permalink().'" title="'.$link.'">'.$link.'</a>';
}



add_action('admin_menu', 'baw_create_menu');
add_filter('the_content','add_more_link');
add_filter("plugin_action_links_".plugin_basename(__FILE__), 'your_plugin_settings_link' );
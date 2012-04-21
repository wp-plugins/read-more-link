<?php
/**
 * @package Read more link
 * @version 1.5
 */
/*
Plugin Name: Read more link
Plugin URI: http://wordpress.org/extend/plugins/read-more-link/
Description: This plugin allows you to use a "[more...]" tag in a post to display a preview of it on the homepage.
The preview will display the text of the post, up to the "[more...]" tag, while the rest will be replaced by a "Read more..." link (which redirects the user to the post detail).
When viewing the post detail, the tag is removed automatically and the original, full post is displayed.
In the plugin settings you can easily configure the text of the "Read more..." link and the number of line breaks ("<br />" tags) displayed before the link.
Author: Luca Dioli
Version: 1.5
Author URI: http://lucadioli.com/
*/

$options = array(
	'read_more_link_text' => 'Read more...',
	'read_more_link_br' => 2,
	'read_more_link_home' => true,
	'read_more_link_search' => true,
	'read_more_link_tag' => true,
	'read_more_link_sticky' => true,
	'read_more_link_category' => true
);

$rdmPages = array(
	'home' => 'Homepage',
	'search' => 'Search result pages',
	'tag' => 'Tag pages',
	'category' => 'Category pages',
	'sticky' => 'Sticky posts'
);

/* Settings functions */

// Add settings link on plugin page
function your_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=read-more-link/functions.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

function rml_create_menu() {
	add_options_page('Read more link Settings', 'Read more link', 'administrator', __FILE__, 'rml_settings_page');
	add_action( 'admin_init', 'register_mysettings' );
}

function register_mysettings() {
	register_setting( 'read-more-links-settings-group', 'read_more_link_text' );
	register_setting( 'read-more-links-settings-group', 'read_more_link_br' );
	global $rdmPages;
	foreach($rdmPages as $id => $name){
		register_setting( 'read-more-links-settings-group', 'read_more_link_'.$id );
	}
}

function rmlGetOptions($key){
	global $options;
	$o = get_option($key);
	if(empty($o)) return $options[$key];
	else return get_option($key);
}

function rml_settings_page() {
	?>
	<div class="wrap">
		<h2>Read more link</h2>
		<p style="width: 500px;">This plugin allows you to use a "[more...]" tag in a post to display a preview of it on the homepage. The preview will display the text of the post, up to the "[more...]" tag, while the rest will be replaced by a "Read more..." link (which redirects the user to the post detail). When viewing the post detail, the tag is removed automatically and the original, full post is displayed. In the plugin settings you can easily configure the text of the "Read more..." link and the number of line breaks ("&lt;br /&gt;" tags) displayed before the link.</p>
		<br />
		<h3>Settings</h3>
		<form name="rmlForm" method="post" action="options.php">
			<?php settings_fields( 'read-more-links-settings-group' ); ?>
			<?php //do_settings( 'read-more-links-settings-group' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Pages:</th>
					<td>
						<?php 
						global $rdmPages;
						foreach($rdmPages as $id => $name) echo '<input type="checkbox" name="read_more_link_'.$id.'" value="'.$id.'"'.((rmlGetOptions('read_more_link_'.$id)) ? ' checked="checked"' : '').' /> '.$name.'<br />'; 
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Read more link text:</th>
					<td><input type="text" id="read_more_link_text" name="read_more_link_text" value="<?php echo rmlGetOptions('read_more_link_text'); ?>" /></td>
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
						<?php for($i=0;$i<4;$i++) echo '<input type="radio" name="read_more_link_br" value="'.$i.'"'.((rmlGetOptions('read_more_link_br') == $i) ? ' checked="checked"' : '').' /> '.$i.'<br />'; ?>
					</td>
				</tr>
			</table>
			<h3>Preview</h3>
			<div style="border: 2px solid #cccccc;width: 500px; padding: 15px;">
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eget libero eget felis blandit molestie non molestie purus. Suspendisse ut mi sem, nec suscipit turpis. Fusce luctus massa id orci ullamcorper et lacinia felis dictum. Donec eu felis a felis venenatis pulvinar ut ut sapien. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
				<div id="read-more-link-brs">
				<?php
				if(rmlGetOptions('read_more_link_br') > 0){
					for($i=0;$i<rmlGetOptions('read_more_link_br');$i++){
						echo '<br />';
					}
				}
				?>
				</div>
				<a href="javascript:void();" id="read-more-link-link"><?php echo rmlGetOptions('read_more_link_text'); ?></a>
			</div>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<script type="text/javascript">
		document.rmlForm.read_more_link_text.onkeyup = function (){
			var rmlLink = document.getElementById('read-more-link-link');
			rmlLink.textContent = this.value;
		};
		
		var rmlBrs = document.getElementById('read-more-link-brs');
		for(i=0;i<document.rmlForm.read_more_link_br.length;i++){
			document.rmlForm.read_more_link_br[i].onclick = function (){
				var n = this.value;
				var rmlBrs = document.getElementById('read-more-link-brs');
				var html = '';
				if(n > 0){
					for(i=0;i<n;i++){
						html += '<br />';
					}
				}
				rmlBrs.innerHTML = html;
			};
		}
	</script>
	<?php 
}


/* Plugin functions */

function add_more_link($content){
	$tag = "[more...]";
	$attach = false;
	global $rdmPages;
	foreach($rdmPages as $id => $name){
		$state = get_option('read_more_link_'.$id);
		if($state){
			switch($id){
				case 'home':
					if(is_home()) $attach = true;
				break;
				case 'search':
					if(is_search()) $attach = true;
				break;
				case 'category':
					if(is_category()) $attach = true;
				break;
				case 'tag':
					if(is_tag()) $attach = true;
				break;
				case 'sticky':
					if(is_sticky()) $attach = true;
				break;
			}
		}
		else if($id == 'sticky' && is_sticky()){
			$attach = false;
		}
	}
	if($attach){
		$pos = stripos($content,$tag);
		if ($pos !== false) {
			$content = closetags(substr($content,0,$pos));
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

function closetags ( $html ){
	preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result );
	$openedtags = $result[1];
	preg_match_all ( "#</([a-z]+)>#iU", $html, $result );
	$closedtags = $result[1];
	$len_opened = count ( $openedtags );
	if( count ( $closedtags ) == $len_opened ){
		return $html;
	}
	$openedtags = array_reverse ( $openedtags );
	for( $i = 0; $i < $len_opened; $i++ ){
		if ( !in_array ( $openedtags[$i], $closedtags ) ){
			$html .= "</" . $openedtags[$i] . ">";
		}
		else{
			unset ( $closedtags[array_search ( $openedtags[$i], $closedtags)] );
		}
	}
	return $html;
}

add_action('admin_menu', 'rml_create_menu');
add_filter('the_content','add_more_link');
add_filter("plugin_action_links_".plugin_basename(__FILE__), 'your_plugin_settings_link' );
<?php
/*
Plugin Name: Blogger Title Fix
Plugin URI: http://notions.okuda.ca/wordpress-plugins/blogger-title-fix/
Description: Replaces numerical titles (from Blogger import) with the first few words of the post.  Now tested with WP 2.5.
Version: 2.2
Author: Poco
Author URI: http://notions.okuda.ca

HISTORY:
1.1 - Public release with GPL
2.0 - Upgraded to work with Wordpress 2.5.
      Fixed issue with not being able to disable the "word boundary" setting.
	  Moved post DB query until after determining that the title needed to be replaced (duh!)
2.1 - Added single_post_title filter.

*/
/*
    Copyright (C) 2008 Kaz Okuda (http://notions.okuda.ca)

    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

    If you use this plugin or have any questions please leave a comment
    at http://notions.okuda.ca/wordpress-plugins/blogger-title-fix/
    A link to the GPL license can also be found there.
*/


class ko_blogger_title_fix
{
	var $option_name = "ko_blogger_title_fix";

	function get_default_options()
	{
		$options = array(
			"excerpt_length" => 48,
			"cut_word" => false
		);
		
		return $options;
	}
	
	function get_options()
	{
		// If the options exists in the database then retrieve them
		if ( false !== get_option($this->option_name) )
		{
			// Now we can get the options out of the database.
			$options = get_option($this->option_name);
		}
		else
		{
			// Otherwise initialize them to the defaults.
			$options = $this->get_default_options();
		}
		
		return $options;
	}
	
	function save_options($options)
	{
		update_option($this->option_name, $options);
	}

	function fragment($str, $n, $delim='...', $word_cut=false)
	{
		$len = strlen($str);
		if ($len > $n)
		{
			if ($word_cut)
			{
				// This code would limit the output to about n characters and not cut any words.
				// This looks a bit better than the default, but this is not how Blogger reacts.
				preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
				return rtrim($matches[1]) . $delim;
			}
			else
			{
				// Just take the first n characters, like Blogger does.
				return substr(trim($str), 0, $n) . $delim;
			}
		}
		else
		{
			return $str;
		}
	}

	function is_bad_title($title)
	{
		// Look for a title that is only decimal digits (or empty)
		return preg_match("/^\d*$/", $title);
	}

	function filter_title($content = '')
	{
		global $post;

		if ($this->is_bad_title($content))
		{
			$options = $this->get_options();
			$thispost = $post;
		
			// Try the post excerpt first, if it exists.
			$output = $thispost->post_excerpt;

			// If there is no excerpt then we take the content.
			if ( '' == $output )
			{
				$output = $thispost->post_content;
				$output = apply_filters('the_content', $output);
				$output = str_replace(']]>', ']]&gt;', $output);
				$output = strip_tags($output);
			}

			$newtitle = $this->fragment($output, $options["excerpt_length"], '...', $options["cut_word"]);
			return $newtitle;
		}
		else
		{
			return $content;
		}
	}

	function title_fix_page()
	{
		// Get the current value of the options.
		$options = $this->get_options();

		if (isset($_POST['reset_default']))
		{
			?><div class="updated"><p><strong><?php
				// Store the default options to the database
				$options = $this->get_default_options();
				$this->save_options($options);
				_e('Settings Reset', 'Localization name');
			?></strong></p></div><?php
		}
		else if (isset($_POST['info_update']))
		{
			?><div class="updated"><p><strong><?php

			if (isset($_POST['excerpt_length']))
			{
				$options["excerpt_length"] = $_POST['excerpt_length'];
			}
	
			if (isset($_POST['cut_word']))
			{
				$options["cut_word"] = true;
			}
			else
			{
				$options["cut_word"] = false;
			}

			// Store the new options to the database
			$this->save_options($options);
			
			_e('Settings Saved', 'Localization name');
			
			?></strong></p></div><?php
		}

		?><div class=wrap>
			<form method="post">
				<h2>Blogger Title Fix</h2>
				<fieldset class="options" name="set1">
					<legend><?php _e('Excerpt Options', 'Localization name') ?></legend>
					Number of characters from the post to use in the title: <input type="text" name="excerpt_length" value="<?php echo $options["excerpt_length"] ?>" ><br />
					Cut title at word boundary: <input type="checkbox" name="cut_word" <?php if ($options["cut_word"]) { print ('checked="checked"'); } ?> >
				</fieldset>
				<div class="submit">
					<input type="submit" name="info_update" value="<?php
					_e('Update options', 'Localization name')
					?> »" />
					<input type="submit" name="reset_default" value="<?php
					_e('Reset to Defaults', 'Localization name')
					?> »" />
				</div>
			</form>
		</div>
	<?php
	}
	
	
	function add_page()
	{
		if (function_exists('add_submenu_page'))
		{
			add_submenu_page('plugins.php', 'Blogger Title Fix', 'Blogger Title Fix', 10, basename(__FILE__), array(&$this, 'title_fix_page'));
		}
	}
}


$ko_blogger_title_fix_instance = new ko_blogger_title_fix();
add_filter('the_title', array(&$ko_blogger_title_fix_instance, 'filter_title'),10,2);
add_filter('single_post_title', array(&$ko_blogger_title_fix_instance, 'filter_title'),10,2);
add_action('admin_menu', array(&$ko_blogger_title_fix_instance, 'add_page'));

?>

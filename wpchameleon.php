<?php
/*
Plugin Name: WPChameleon
Plugin URI: http://www.thomasteisberg.com/WPChameleon/
Description: Allows users to pick from pre-defined themes for your blog using CSS
Version: 1.1
Author: Thomas Teisberg
Author URI: http://www.thomasteisberg.com/
*/

/*  Copyright 2009  Thomas Teisberg  (email : tteisberg@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// --------------------------------
// Data Structures
// --------------------------------

if (!class_exists("WPChameleonClass")) {
    class WPChameleonClass extends WP_Widget{
         var $adminOptionsName = "WPChameleonAdminOptions";
         
         function WPChameleonClass(){
            parent::WP_Widget(false, $name = 'WPChameleon Variation Chooser');	
         }
         
         // --------------------------------
         // CSS Insertion
         // --------------------------------
         
         // variationCode() - echos code for user's selected variation
         function variationCode(){
            $this->insertCss($_COOKIE['WPChameleon_varient']);
         }
         
         // insertCss($variation) - Inserts CSS for $variation
         function insertCss($variation){
            $WPChameleonAdminOptions = $this->getAdminOptions();
            echo "\n<!-- WP Chameleon Plugin -->\n";
            echo "<style type=\"text/css\">\n";
            echo $WPChameleonAdminOptions[$variation]['css'];
            echo "\n</style>\n\n";
         }
         
         // --------------------------------
         // Widget
         // --------------------------------
         
         function loadJS(){
            wp_enqueue_script('wpchameleonjs', '/wp-content/plugins/wpchameleon/js/wpchameleon.js');
         }
         
         function getPath(){
         	// echos path of WordPress installation for use in cookies
         	$url = get_bloginfo('wpurl');
         	$domain = "http://".$_SERVER['HTTP_HOST'];
         	$domain = escapeshellcmd($domain);
         	$domain = '%'.$domain.'%';
         	
         	echo preg_replace($domain, '', $url);
         }
         
         function printWidget(){
            $WPChameleonAdminOptions = $this->getAdminOptions();
            ?>
            <script type="text/javascript">WPChameleon_clearNonRootC("<?php bloginfo('wpurl'); ?>");</script>
            <?php
            for($i=1;$i<=$WPChameleonAdminOptions['numvarients'];$i++){
               ?>
                  <?php $v_alt = ($WPChameleonAdminOptions[$i][name]!='')? $WPChameleonAdminOptions[$i][name] : 'Variation'.$i; ?>
                  <div id="wpchameleon-varient-<?php echo $i; ?>" class="wpchameleon">
                  <a href="#" onClick="WPChameleon_setVarient(<?php echo $i ?>,'<?php $this->getPath(); ?>')">
                  <?php if($WPChameleonAdminOptions[$i][img]!=''): ?><img src="<?php echo $WPChameleonAdminOptions[$i]['img']; ?>" alt="<?php echo $v_alt; ?>" /><?php endif; ?>
                  <?php if($WPChameleonAdminOptions[$i][name]!=''): ?><?php echo $WPChameleonAdminOptions[$i][name]; ?><?php endif; ?></a><br />
                  </div>
               <?php               
            }
         }
         
         function printSmallWidget(){
            $WPChameleonAdminOptions = $this->getAdminOptions();
            ?>
            	<div class="wpchameleon">
            	<script type="text/javascript">WPChameleon_clearNonRootC("<?php bloginfo('wpurl'); ?>");</script>
            <?php
            for($i=1;$i<=$WPChameleonAdminOptions['numvarients'];$i++){
               ?>
                  <?php $v_alt = ($WPChameleonAdminOptions[$i][name]!='')? $WPChameleonAdminOptions[$i][name] : 'Variation'.$i; ?>
                  <a href="#" onClick="WPChameleon_setVarient(<?php echo $i ?>,'<?php $this->getPath(); ?>')">
                  <?php if($WPChameleonAdminOptions[$i][img]!=''): ?><img src="<?php echo $WPChameleonAdminOptions[$i]['img']; ?>" alt="<?php echo $v_alt; ?>" /><?php endif; ?></a>
               <?php               
            }
            ?>
            	</div>
            <?php
         }
         
          /** @see WP_Widget::widget */
          function widget($args, $instance) {		
              extract( $args );
              ?>
                    <?php echo $before_widget; ?>
                        <?php echo $before_title
                            . $instance['title']
                            . $after_title; ?>
                        <?php ($instance['small']==true)?$this->printSmallWidget():$this->printWidget(); ?>
                    <?php echo $after_widget; ?>
              <?php
          }

          /** @see WP_Widget::update */
          function update($new_instance, $old_instance) {				
              return $new_instance;
          }

          /** @see WP_Widget::form */
          function form($instance) {				
              $title = esc_attr($instance['title']);
              $small = esc_attr($instance['small']);
              ?>
                  <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
                  <p><label for="<?php echo $this->get_field_id('small'); ?>">Small version? (images only) </label><input class="checkbox" type="checkbox" <?php checked( $instance['small'], true ); ?> id="<?php echo $this->get_field_id( 'small' ); ?>" name="<?php echo $this->get_field_name( 'small' ); ?>" /></p>
              <?php 
          }
         
         // --------------------------------
         // Admin Options
         // --------------------------------
         
         // getAdminOptions() - Updates and returns the latest admin options
         function getAdminOptions() {
            $WPChameleonAdminOptions = array('numvarients' => 2);
            $WPChameleonAdminOptionsNew = get_option($this->adminOptionsName);
            if (!empty($WPChameleonAdminOptionsNew)) {
               foreach ($WPChameleonAdminOptionsNew as $key => $option) $WPChameleonAdminOptions[$key] = $option;
            }
            update_option($this->adminOptionsName, $WPChameleonAdminOptions);
            return $WPChameleonAdminOptions;
		   }
         
         // Init() - Called on plugin activation to initialize admin options
         function init(){
            $this->getAdminOptions();
         }
         
         // --------------------------------
         // Admin Menu                      
         // --------------------------------
         
		   function printAdminPage() {
            $WPChameleonAdminOptions = $this->getAdminOptions();
            if (isset($_POST['update_WPChameleonSettings'])) {
               for($i=1;$i<=$WPChameleonAdminOptions['numvarients'];$i++){
                  $WPChameleonAdminOptions[$i]['name'] = $_POST['WPChameleon_'.$i.'_name'];
                  $WPChameleonAdminOptions[$i]['img'] = $_POST['WPChameleon_'.$i.'_img'];
                  $WPChameleonAdminOptions[$i]['css'] = $_POST['WPChameleon_'.$i.'_css'];
               }
               if (isset($_POST['WPChameleon_numvarients'])){
                  $WPChameleonAdminOptions['numvarients'] = number_format($_POST['WPChameleon_numvarients'],0,'','');
               }
               update_option($this->adminOptionsName, $WPChameleonAdminOptions);
               ?>
                  <div class="updated"><p><strong><?php _e("Settings Updated.", "WPChameleon");?></strong></p></div>
               <?php
            }
            ?>
            <div class="wrap">
               <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
               <h2>WPChameleon Options</h2>
               <p>Below you can define theme variations. Your users will then be able to choose which of these variations they prefer. All fields are optional. The "name" should be a human-readable one to two word description of the variation. The  "image URL" is the full address (e.x. http://www.example.comn/image/dir/img.jpg) to an small preview image of the variation. nally, the CSS box is where you can put any CSS that you need for the variation. Style tags will be added automatically.</p>
               <p>If you want one of the variations to be exactly as the theme is now, leave the first CSS box blank.</p>
               <h3>Number of theme variations</h3>
               <input type="text" size="3" name="WPChameleon_numvarients" value="<?php echo $WPChameleonAdminOptions['numvarients']; ?>" />
               <?php for($i=1;$i<=$WPChameleonAdminOptions['numvarients'];$i++){ ?>
                     <h3>Theme Variation <?php echo $i; ?></h3>
                     <?php _e("Name: ", "WPChameleon"); ?><input type="text" size="25" name="WPChameleon_<?php echo $i; ?>_name" value="<?php echo $WPChameleonAdminOptions[$i]['name']; ?>" />
                     <?php _e("Image URL: ", "WPChameleon"); ?><input type="text" size="25" name="WPChameleon_<?php echo $i; ?>_img" value="<?php echo $WPChameleonAdminOptions[$i]['img']; ?>" />
                     <h4><?php _e("CSS: ", "WPChameleon"); ?></h4><textarea name="WPChameleon_<?php echo $i; ?>_css" style="width: 80%; height: 100px;"><?php _e(apply_filters('format_to_edit',$WPChameleonAdminOptions[$i]['css']), 'WPChameleon') ?></textarea>
               <?php } ?>
               <div class="submit">
                  <input type="submit" name="update_WPChameleonSettings" value="<?php _e('Update Settings', 'WPChameleon') ?>" />
               </div>
               </form>
            </div>
            <?php
         }

    }
}


// Create an instance of the class
if(class_exists("WPChameleonClass")) {
	$WPChameleon = new WPChameleonClass();
}

// --------------------------------
// Actions and Filters
// --------------------------------

// Initialize the admin panel
if (!function_exists("WPChameleon_ap")) {
	function WPChameleon_ap() {
		global $WPChameleon;
		if (!isset($WPChameleon)){
			return;
		}
		if (function_exists('add_options_page')) {
	      add_options_page('WPChameleon', 'WPChameleon Options', 9, basename(__FILE__), array(&$WPChameleon, 'printAdminPage'));
		}
	}
}

// Now, the real actions
if(isset($WPChameleon)){
   add_action('init', array(&$WPChameleon, 'loadJS'));
   add_action('wp_head', array(&$WPChameleon,'variationCode')); // Insert custom CSS into header
   add_action('activate_wpchameleon/wpchameleon.php',  array(&$WPChameleon, 'init')); // Call init when plugin activated
   add_action('admin_menu', 'WPChameleon_ap'); // Register the admin menu
   add_action('widgets_init', create_function('', 'return register_widget("WPChameleonClass");')); // Register the widget
}

?>

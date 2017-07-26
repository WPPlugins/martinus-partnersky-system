<?php
/*
Plugin Name: Martinus partnerský program
Plugin URI: http://wordpress.org/plugins/martinus-partnersky-system/
Description: Umožnuje pridávať bannery v rámci partnerského programu Martinus
Version: 1.1
Author: Maxo Matos
Author URI: http://matos.sk/
License: GPL2

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

class Martinus_pp extends WP_Widget {

    /**
     * Widget construction
     */
    function __construct() {
        $widget_ops = array('classname' => 'widget_martinus_pp', 'description' => 'Zobrazuje banner z partnerského programu kníhkupectva Martinus');
        $control_ops = array('width' => 400, 'height' => 350);
        parent::__construct('martinus-pp','Martinus partnerský systém', $widget_ops, $control_ops);        
    }

    /**
     * Setup the widget output
     */
    function widget( $args, $instance ) {

        if (!isset($args['widget_id'])) {
          $args['widget_id'] = null;
        }

        extract($args);

        $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
        $titleUrl = empty($instance['titleUrl']) ? '' : $instance['titleUrl'];
        $cssClass = empty($instance['cssClass']) ? '' : $instance['cssClass'];
        $text = apply_filters('widget_enhanced_text', $instance['text'], $instance);
        $hideTitle = !empty($instance['hideTitle']) ? true : false;
        $newWindow = !empty($instance['newWindow']) ? true : false;
        $filterText = !empty($instance['filter']) ? true : false;
        $bare = !empty($instance['bare']) ? true : false;
        
        $banner_type = $instance['banner_type'];
        $id_knihy = substr($titleUrl, strrpos($titleUrl, '=')+1);

        if ( $cssClass ) {
            if( strpos($before_widget, 'class') === false ) {
                $before_widget = str_replace('>', 'class="'. $cssClass . '"', $before_widget);
            } else {
                $before_widget = str_replace('class="', 'class="'. $cssClass . ' ', $before_widget);
            }
        }

        echo $bare ? '' : $before_widget;

        if ($newWindow) $newWindow = "target='_blank'";

        
            if($titleUrl) $title = "<a href='$titleUrl' $newWindow>$title</a>";
            echo $bare ? $title : $before_title . $title . $after_title;
        

        echo $bare ? '' : '<div class="textwidget widget-text">';

        // Parse the text through PHP
        ob_start();
        eval('?>' . $text);
        $text = ob_get_contents();
        ob_end_clean();
        
        $text = '<script type="text/javascript" src="http://partner.martinus.sk/banners/banner.js?type='.$banner_type.'&uItem='.$id_knihy.'&z='.get_option('martinus-kod').'"></script>';
    

        // Run text through do_shortcode
        $text = do_shortcode($text);

        // Echo the content
        echo $filterText ? wpautop($text) : $text;
        echo $bare ? '' : '</div>' . $after_widget;


    }

    function update( $new_instance, $old_instance ) {
     
        $instance = $old_instance;
    
        $instance['titleUrl'] = strip_tags($new_instance['titleUrl']);
        $instance['banner_type'] = $new_instance['banner_type'];
        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'titleUrl' => '',
            'banner_type' => ''
            
        ));
        
        $titleUrl = $instance['titleUrl'];
        $banner_type = $instance['banner_type'];
?>
        <style>
            .monospace { font-family: Consolas, Lucida Console, monospace; }
        </style>

        <p>
            <label for="<?php echo $this->get_field_id('banner_type'); ?>"><?php echo 'Typ banneru'; ?>:</label><br>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('banner_type_1'); ?>" 
                   name="<?php echo $this->get_field_name('banner_type'); ?>" 
                   type="radio" 
                   <?php echo ($banner_type == 1)? ' checked ' :''?>
                   value="1" />Banner 468x60 <br>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('banner_type_2'); ?>" 
                   name="<?php echo $this->get_field_name('banner_type'); ?>" 
                   type="radio" 
                   <?php echo ($banner_type == 2)? ' checked ' :''?>
                   value="2" />Banner 300x300 <br>
            <input class="widefat" 
                   id="<?php echo $this->get_field_id('banner_type_3'); ?>" 
                   name="<?php echo $this->get_field_name('banner_type'); ?>" 
                   type="radio" 
                   <?php echo ($banner_type == 3)? ' checked ' :''?>
                   value="3" />Banner 160x600<br>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('titleUrl'); ?>"><?php echo 'URL knihy'; ?>:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('titleUrl'); ?>" name="<?php echo $this->get_field_name('titleUrl'); ?>" type="text" value="<?php echo $titleUrl; ?>" />
        </p>
<?php
    }
}


function martinus_pp_widget_init() {
    register_widget('Martinus_pp');
}
add_action('widgets_init', 'martinus_pp_widget_init');

add_action( 'admin_menu', 'register_martinus_pp_menu' );
function register_martinus_pp_menu(){
    add_submenu_page( 'options-general.php', 'Martinus PP', 'Martinus PP','manage_options', 'martinus_pp', 'martinus_pp_admin_page' );
}

function martinus_pp_admin_page() {

  if(isset($_POST['martinus-kod'])) {  
    update_option('martinus-kod', $_POST['martinus-kod']);
  }
  ?>
        <div class="wrap" style="width: 400px; float: left">
            <h2>Partnerský systém Martinus.sk</h2>
            <br />
            <form method="post" action=""> 

              <?php
              settings_fields('my_options_group');
              do_settings_sections('my_options_group');
              ?>

              <label for="martinus-kod">Partnerský kód:</label>
              <input type="text" name="martinus-kod" value="<?php echo get_option('martinus-kod'); ?>">

               <?php submit_button(); ?>
            </form>
          </div>
          <div style="float:left; width: 300px; min-height: 200px;  margin-left: 30px; padding-left: 15px; ">
          
          <h4>Kde získať partnerský kód?</h4>
          <p>Partnerský kód a všetky potrebné informácie získate na <a href="http://partner.martinus.sk/" target="_blank">Partnerský systém Martinus.sk</a> </p>
          
        </div>

        <?php
}
function register_martinus_setting() {
	register_setting( 'my_options_group', 'kod'); 
    
        add_settings_field(
            'martinus-kod',
            'Partnersky kod',
            array( $this, 'title_callback' ),
            'martinus_pp',
            'setting_section_id'
        ); 
} 
add_action( 'admin_init', 'register_martinus_setting' );

   function title_callback()
    {
        printf(
            '<input type="text" id="title" name="my_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }

 
    
function martinus_register_button( $buttons ) {
   array_push( $buttons, "|", "martinusps" );
   return $buttons;
}

function martinus_add_plugin( $plugin_array ) {
     $plugin_array['martinusps'] = plugins_url('js/martinus-js.js', __FILE__ );
   return $plugin_array;
}

function martinus_shortcode_button() {
   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
      return;
   }

   if ( get_user_option('rich_editing') == 'true' ) {
      add_filter( 'mce_external_plugins', 'martinus_add_plugin' );
      add_filter( 'mce_buttons', 'martinus_register_button' );
   }
}

add_action('init', 'martinus_shortcode_button');

function shortcode_martinus( $atts, $content = null ) {
  
 $id_knihy = substr($atts['url'], strrpos($atts['url'], '=')+1);
 $banner_class = $atts['type'];
 $zarovnanie = $atts['align'];
 
 switch ($banner_class) {
    case 'banner468':
      $banner_type = 1;
     break;
case 'banner300':
      $banner_type = 2;
     break;
   case 'banner160':
      $banner_type = 3;
     break;
    default:
     break;
 }
 
 $out = '<div id="martinus" class="' .$banner_class . ' ' . $zarovnanie . '">';
 $out.= '<script type="text/javascript" src="http://partner.martinus.sk/banners/banner.js?type='.$banner_type.'&uItem='.$id_knihy.'&z='.get_option('martinus-kod').'"></script>';
 $out.= '</div>';
 return $out;
}
add_shortcode('martinus', 'shortcode_martinus');

add_action( 'wp_enqueue_scripts', 'add_martinus_stylesheet' );
function add_martinus_stylesheet() {
    $css_path = plugins_url('martinus.css', __FILE__ );
    wp_register_style( 'myStyleSheets', $css_path );
    wp_enqueue_style( 'myStyleSheets' );
}

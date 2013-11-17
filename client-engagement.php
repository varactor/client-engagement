<?
/**
 * Plugin Name: Client Engagement
 * Plugin URI: http://www.olivermgrech.com/client-engagement-wordpress-plugin
 * Description: A Sticky box which will make it easy for your prospects to send you their contact inforamtion.
 * Version: 0.1
 * Author: Oliver M Grech
 * Author URI: http://www.olivermgrech.com
 * License: GPL2
 */
/**
 *  Copyright 2013  Oliver M Grech  (email : varactor@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/ 

function ce_get_defaults() {  
    $options = array(  
        'title' => 'Get In Touch!',  
        'content' => 'Get in touch today for further information.',  
        'photo' => ''  
    );  
    return $options;  
} 

function ce_init(){

	if (!is_admin()) {
		//wp_deregister_script('jquery');

		// load the local copy of jQuery in the footer
		//wp_register_script('jquery', '/wp-includes/js/jquery/jquery.js', false, '1.3.2', true);

		// or load the Google API copy in the footer
		//wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js', false, '1.3.2', true);

		wp_enqueue_script('jquery');
	}

} 


function omg_ce(){


  $options = ce_get_defaults();

  $plugin_path = plugin_dir_path( __FILE__ );
  
  $content = file_get_contents($plugin_path.'client-engagement.html');
  
  /**
   * Replace Placeholders with Content from the Settings Page (options)
   */         
  
  
  $optTitle = get_option('ce_title');
  $optTitle = ($optTitle) ? $optTitle : $options['title'];
  
  $optContent =  stripslashes(base64_decode(get_option('ce_content')));
  $optContent = ($optContent) ? $optContent : $options['content'];
  
  $content = str_replace("{title}", $optTitle, $content);
  $content = str_replace("{content}", $optContent, $content);

  echo apply_filters('the_content', $content);  

}
function omg_ce_scripts() {
  
  wp_register_style( 'client-engagement', plugins_url('client-engagement.css', __FILE__) );
	wp_enqueue_style( 'client-engagement', get_stylesheet_uri() );
	//wp_enqueue_script( 'script-name', get_template_directory_uri() . '/js/example.js', array(), '1.0.0', true );
}

function ce_admin_menu() {
	add_options_page( 'Client Engagement', 'Client Engagement', 'manage_options', 'ce_admin_page', 'ce_admin_options' );
}

function ce_admin_options() {

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 
    $hidden_field_name = 'mt_submit_hidden';
    $title_field_name = 'ce_title';
    $content_field_name = 'ce_content';

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $ptitle_field_val = $_POST[ $title_field_name ];
        $pcontent_field_val = $_POST[ $content_field_name ];

        // Save the posted value in the database
        update_option( $title_field_name, $ptitle_field_val );
        update_option( $content_field_name, base64_encode($pcontent_field_val) );


?>
<div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
<?php

    }
    
    // Read in existing option value from database
    $title_field_val = get_option( $title_field_name );
    $content_field_val = base64_decode(get_option( $content_field_name ));
    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'Client Engagement Settings', 'client-engagement' ) . "</h2>";

    // settings form
    
    ?>

<form name="ce_form_settings" class="ce_form_settings" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

  <div>
    <div class="ce_form_label">
      <h3><?php _e("Title:", 'client-engagement' ); ?></h3>
    </div> 
    <div class="ce_form_item">
    <input type="text" name="<?php echo $title_field_name; ?>" value="<?php echo $title_field_val; ?>" size="20"></div>
  </div>

  <div class="ce_form_label">
  <h3><?php _e("Content:", 'client-engagement' ); ?></h3> 
  </div>
  <div class="ce_form_item">
    <textarea type="text" name="<?php echo $content_field_name; ?>" size="20"><?php echo stripslashes($content_field_val); ?></textarea>
  </div>

<hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>
<style>

.ce_form_item textarea {

  width: 300px;

}

</style>
<?php
 
}

add_action('init', 'ce_init');

add_action( 'admin_menu', 'ce_admin_menu' );

add_action( 'wp_enqueue_scripts', 'omg_ce_scripts' );

add_action ( 'wp_footer', 'omg_ce' );



 ?>
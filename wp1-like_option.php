<div class="wrap">
    <?php echo "<h1>" . __( 'WP1 Like Options', 'wp1_like' ) . "</h1>"; ?>

    <?php 
	if ( isset( $_GET['reset_nonce'] ) && wp_verify_nonce( $_GET['reset_nonce'], 'Reset WP1 LIKE' ) ) {
		$default_post_types = array_map('sanitize_text_field', array('post'));
        update_option( 'wp1_like_button_text', 'Like' );
        update_option( 'wp1_like_button_color', '002e62' );
        update_option( 'wp1_like_button_hover_color', 'd11142' );
        update_option( 'wp1_like_button_disabled_color', '999999' );
        update_option( 'wp1_like_show_count', 'Yes' );
        update_option( 'wp1_like_thumb_icon', 'fa-thumbs-up' );
        update_option( 'wp1_like_post_types', $default_post_types );
    } else if ( isset( $_GET['reset_nonce'] ) && !wp_verify_nonce( $_GET['reset_nonce'], 'Reset WP1 LIKE' ) ) { ?>
        <div id="message" class="notice notice-error is-dismissible">
            <p><?php _e( 'Sorry something went wrong.', 'wp1_like' ); ?></p>
        </div><?php
	}

    if ( isset( $_POST['update_like_button'] ) && ! wp_verify_nonce( $_POST['update_like_button'], 'Like Button' ) ) { ?>
        <div id="message" class="notice notice-error is-dismissible">
            <p><?php _e( 'Sorry something went wrong.', 'wp1_like' ); ?></p>
        </div><?php
    } else if ( isset( $_POST['update_like_button'] ) && wp_verify_nonce( $_POST['update_like_button'], 'Like Button' ) ) {
        $wp1_like_button_text            = sanitize_text_field($_POST['wp1_like_button_text']);
        $wp1_like_button_color           = sanitize_text_field($_POST['wp1_like_button_color']);
        $wp1_like_button_hover_color     = sanitize_text_field($_POST['wp1_like_button_hover_color']);
        $wp1_like_button_disabled_color  = sanitize_text_field($_POST['wp1_like_button_disabled_color']);
        $wp1_like_post_types             = array_map('sanitize_text_field', $_POST['wp1_like_post_types']);
        $wp1_like_show_count             = sanitize_text_field($_POST['wp1_like_show_count']);
        $wp1_like_thumb_icon             = sanitize_text_field($_POST['wp1_like_thumb_icon']);

        update_option( 'wp1_like_button_text', $wp1_like_button_text );
        update_option( 'wp1_like_button_color', $wp1_like_button_color );
        update_option( 'wp1_like_button_hover_color', $wp1_like_button_hover_color );
        update_option( 'wp1_like_button_disabled_color', $wp1_like_button_disabled_color );
        update_option( 'wp1_like_post_types', $wp1_like_post_types );
        update_option( 'wp1_like_show_count', $wp1_like_show_count );
        update_option( 'wp1_like_thumb_icon', $wp1_like_thumb_icon );
    }else{
        $wp1_like_button_text            = __( get_option( 'wp1_like_button_text', true ), 'wp1_like' );
        $wp1_like_button_color           = get_option( 'wp1_like_button_color', true );
        $wp1_like_button_hover_color     = get_option( 'wp1_like_button_hover_color', true );
        $wp1_like_button_disabled_color  = get_option( 'wp1_like_button_disabled_color', true );
        $wp1_like_post_types             = (get_option( 'wp1_like_post_types', false )!='') ? get_option( 'wp1_like_post_types', true ) : array();
        $wp1_like_show_count             = get_option( 'wp1_like_show_count', true );
        $wp1_like_thumb_icon             = get_option( 'wp1_like_thumb_icon', true );
    }
	?>

    <form method="post" action="options-general.php?page=wp1_like_admin" novalidate="novalidate">
        <?php
        $exclude = array('attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block');
        $post_types = array();
        $avail_post_types = get_post_types( array('public' => true ), 'objects' );
        foreach ( $avail_post_types as $post_type ) {
            if( !in_array($post_type, $exclude ) ){
                ob_start();
                print_r($post_type);
                $cnt = ob_get_contents();
                ob_get_clean();
                $post_types[$post_type->name] = $post_type->label;
            }
        }
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wp1_like_button_text"><?php _e('Like Button Text', 'wp1_like'); ?></label></th>
                <td><input name="wp1_like_button_text" type="text" id="wp1_like_button_text" value="<?php _e( isset($wp1_like_button_text) ? $wp1_like_button_text : 'Like', 'wp1_like' ) ;?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label for="wp1_like_button_color"><?php _e('Button Color', 'wp1_like'); ?></label></th>
                <td><input name="wp1_like_button_color" type="text" id="wp1_like_button_color" value="<?php _e( isset($wp1_like_button_color) ? $wp1_like_button_color : '002e62', 'wp1_like' ) ;?>" class="jscolor" readonly></td>
            </tr>
            <tr>
                <th scope="row"><label for="wp1_like_button_hover_color"><?php _e('Hover Button Color', 'wp1_like'); ?></label></th>
                <td><input name="wp1_like_button_hover_color" type="text" id="wp1_like_button_hover_color" value="<?php echo isset($wp1_like_button_hover_color) ? $wp1_like_button_hover_color : 'd11142';?>" class="jscolor" readonly></td>
            </tr>
            <tr>
                <th scope="row"><label for="wp1_like_button_disabled_color"><?php _e('Unlike Button Color', 'wp1_like'); ?></label></th>
                <td><input name="wp1_like_button_disabled_color" type="text" id="wp1_like_button_disabled_color" value="<?php echo isset($wp1_like_button_disabled_color) ? $wp1_like_button_disabled_color : '999999';?>" class="jscolor" readonly></td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Post Type(s)', 'wp1_like'); ?></th>
                <td>
                    <fieldset>
                        <?php
                        $n = 0;
                        foreach( $post_types as $opt_k => $opt_v ){
                            $n++;
                            echo '<label>
                                    <input type="checkbox" name="wp1_like_post_types[]" value="'.$opt_k.'" '.
                                    ( in_array($opt_k, $wp1_like_post_types) ? 'checked="checked"' : ''  )
                                    .'> 
                                    <span>'.__($opt_v, 'wp1_like').'</span>
                                </label>';
                            echo $n < count($post_types) ? '<br>' : '';
                        }
                        ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Count', 'wp1_like'); ?></th>
                <td>
                    <fieldset>
                        <?php
                        $options = array('Yes','No');
                        $n = 0;
                        foreach( $options as $opt ){
                            $n++;
                            echo '<label>
                                    <input type="radio" name="wp1_like_show_count" value="'.$opt.'" '.
                                    ( ($wp1_like_show_count == $opt) ? 'checked="checked"' : ''  )
                                    .'> 
                                    <span>'.$opt.'</span>
                                </label>';
                            echo $n < count($options) ? '<br>' : '';
                        }
                        ?>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Like Icon', 'wp1_like'); ?></th>
                <td>
                    <fieldset>
                        <?php
                        $opts = array('fa-thumbs-up','fa-thumbs-o-up','fa-heart','fa-heart-o');
                        $m = 0;
                        foreach( $opts as $opt ){
                            $m++;
                            echo '<label>
                                    <input type="radio" name="wp1_like_thumb_icon" value="'.$opt.'" '.
                                    ( ($wp1_like_thumb_icon == $opt) ? 'checked="checked"' : ''  )
                                    .'> 
                                    <span><i class="fa '.$opt.'" aria-hidden="true"></i></span>
                                </label>';
                            echo $m < count($opts) ? '<br>' : '';
                        }
                        ?>
                    </fieldset>
                </td>
            </tr>
        </table>
        <?php wp_nonce_field( 'Like Button', 'update_like_button' ); ?>
        <p><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Settings', 'wp1_like'); ?>"> &nbsp; 
            <a class="button button-primary" href="options-general.php?page=wp1_like_admin&reset_settings=1&reset_nonce=<?php _e(wp_create_nonce( 'Reset WP1 LIKE' ), 'wp1_like') ?>"><?php _e('Reset Settings', 'wp1_like'); ?></a></p>
    </form>
</div>
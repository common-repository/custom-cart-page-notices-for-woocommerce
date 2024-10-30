<?php

/*
 Plugin Name: Custom cart page notices for WooCommerce
 Plugin URI: https://profiles.wordpress.org/rynald0s
 Description: This plugin lets you change the default cart page notices for WooCommerce. 
 Author: Rynaldo Stoltz
 Author URI: http:rynaldo.com
 Version: 1.0
 License: GPLv3 or later License
 URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

/**
 * Check if WooCommerce is active
 **/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

/**
 * Add settings
 */

function ccpn_section( $sections ) {
    $sections['ccpn_section'] = __( 'Custom cart page notices', 'woocommerce' );
    return $sections;
}

add_filter( 'woocommerce_get_sections_products', 'ccpn_section' );


function ccpn_settings( $settings, $current_section ) {

    
    /**
     * Check the current section is what we want
     **/

    if ( 'ccpn_section' === $current_section ) {

        $ccpn_settings[] = array( 'title' => __( 'Change the default cart page notices', 'woocommerce' ), 'type' => 'title', 'id' => 'wc_cart_notice_modifier' );

        $ccpn_settings[] = array(
                'title'    => __( 'Your custom "Added to cart" success notice', 'woocommerce' ),
                'desc' => 'This will change the default "added to cart" success notice',
                'id'       => 'ccpn_added_text',
                'type'     => 'text',
                'placeholder' => 'has been successfully added to your cart',
                'css'      => 'min-width:350px;',
            );

        $ccpn_settings[] = array(
                'title'    => __( 'Your custom "Removed from cart" success notice', 'woocommerce' ),
                'desc' => 'This will change the default "removed from cart" success notice',
                'id'       => 'ccpn_removed_text',
                'type'     => 'text',
                'placeholder' => 'has been successfully removed from your cart',
                'css'      => 'min-width:350px;',
            );

        $ccpn_settings[] = array(
                'title'    => __( 'Your custom "Cart is empty" notice', 'woocommerce' ),
                'desc' => 'This will change the default "cart is empty" notice',
                'id'       => 'ccpn_empty_text',
                'type'     => 'text',
                'placeholder' => 'Your cart is currently empty',
                'css'      => 'min-width:350px;',
            );

        $ccpn_settings[] = array( 'type' => 'sectionend', 'id' => 'wc_cart_notice_modifier' );
        return $ccpn_settings;
} else {
        return $settings;
    }

}

add_filter( 'wc_add_to_cart_message_html', 'ccpn_added_notice', 10, 2 ); 

function ccpn_added_notice( $message, $products ) { 

$count = 0;
    $titles = array();
    foreach ( $products as $product_id => $qty ) {
        $titles[] = ( $qty > 1 ? absint( $qty ) . ' &times; ' : '' ) . sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), strip_tags( get_the_title( $product_id ) ) );
        $count += $qty;
    }

    $titles     = array_filter( $titles );
    $added_text = sprintf( _n(
        '%s ', // Singular
        '%s ', // Plural
        $count, // Number of products added
        'woocommerce' // Textdomain
    ), wc_format_list_of_items( $titles ) );

$message   = sprintf( '%s <a href="%s" class="button">%s</a>',
               esc_html( $added_text ),
               esc_url( wc_get_page_permalink( 'cart' ) ),
               esc_html__( 'View Cart', 'woocommerce' ));

return __( $message . $options = ccpn_get_settings( 'ccpn_added_text'), 'woocommerce' );

    }
}

// lets get rid of that pesky "removed." bit here

add_filter('gettext', 'ccpn_remove_removed', 10, 3);

function ccpn_remove_removed ( $translation, $text, $domain ) {
    if ($domain == 'woocommerce') {
        if ($text == '%s removed.') {
            $translation = '%s ';
        }
    }

    return $translation;
}

add_filter( 'woocommerce_cart_item_removed_title', 'ccpn_removed_notice', 12, 2);

function ccpn_removed_notice( $message, $cart_item ) {
    $product = wc_get_product( $cart_item['product_id'] );
    $message = sprintf( __('%s '), $product->get_name() );
    return __( $message . $options = ccpn_get_settings( 'ccpn_removed_text'), 'woocommerce ');
}

add_filter( 'wc_empty_cart_message', 'ccpn_empty_notice' );

function ccpn_empty_notice() {
  return __( $options = ccpn_get_settings( 'ccpn_empty_text'), 'woocommerce' );
}

add_filter( 'woocommerce_get_settings_products','ccpn_settings', 10, 2 );

function ccpn_get_settings( $key ) {
    $saved = get_option( $key );
    if( $saved && '' != $saved ) {
        return $saved;
    }
    return '';
}

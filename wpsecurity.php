<?php
/*
Plugin Name: wpsecurity
Description: Security Features for WordPress
Version: 1.2
Author: Corporation 9
Author URI: https://corporation9.com

wpsecurity - Security Features for WordPress
Copyright (C) 2019  Corporation 9 AB

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
*/

// remove unnecessary header information
function ev_wpsec_remove_header_info() {
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'start_post_rel_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'parent_post_rel_link', 10, 0);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head',10,0); // for WordPress >= 3.0
}
add_action('init', 'ev_wpsec_remove_header_info');

// remove wp version meta tag and from rss feed
add_filter('the_generator', '__return_false');
//
//Security Fixes
//
// remove wp version param from any enqueued scripts
function ev_wpsec_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
add_filter( 'style_loader_src', 'ev_wpsec_remove_wp_ver_css_js', 9999 );
add_filter( 'script_loader_src', 'ev_wpsec_remove_wp_ver_css_js', 9999 );

/*Disable ping back scanner and complete xmlrpc class. */
add_filter( 'wp_xmlrpc_server_class', '__return_false' );
add_filter('xmlrpc_enabled', '__return_false');

//Remove error mesage in login
add_filter('login_errors',create_function('$a', "return 'Invalid Input';"));

// remove various feeds

function ev_wpsec_fb_disable_feed() {
wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
}

add_action('do_feed', 'ev_wpsec_fb_disable_feed', 1);
add_action('do_feed_rdf', 'ev_wpsec_fb_disable_feed', 1);
add_action('do_feed_rss', 'ev_wpsec_fb_disable_feed', 1);
#add_action('do_feed_rss2', 'ev_wpsec_fb_disable_feed', 1);
add_action('do_feed_atom', 'ev_wpsec_fb_disable_feed', 1);
add_action('do_feed_rss2_comments', 'ev_wpsec_fb_disable_feed', 1);
add_action('do_feed_atom_comments', 'ev_wpsec_fb_disable_feed', 1);

show_admin_bar( false );


#disable redirect to login page :
#http://wordpress.stackexchange.com/questions/85529/how-to-disable-multisite-sign-up-page
function ev_wpsec_prevent_multisite_signup()
{
    wp_redirect( site_url() );
    die();
}
add_action( 'signup_header', 'ev_wpsec_prevent_multisite_signup' );


//remove xpingback header
function ev_wpsec_remove_x_pingback($headers) {
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'ev_wpsec_remove_x_pingback');

//Prevent local and remote file inclusion exploitation
ini_set('allow_url_fopen', 'False');
ini_set('allow_url_include', 'False');



?>
<?php
/**
 * Plugin Name: Trumarx Applications
 * Description: Registers Custom Post Types for Internship and Job Applications with REST API support.
 * Version: 1.0
 * Author: Trumarx Tech Team
 */

function trumarx_register_cpts() {
    register_post_type('internship_app', array(
        'labels' => array(
            'name' => 'Internship Apps',
            'singular_name' => 'Internship App',
            'menu_name' => 'Internships',
            'all_items' => 'All Applications',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Application',
            'edit_item' => 'Edit Application',
            'new_item' => 'New Application',
            'view_item' => 'View Application',
            'search_items' => 'Search Applications',
            'not_found' => 'No applications found',
        ),
        'public' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => array('title', 'editor', 'custom-fields', 'author'),
        'capability_type' => 'post',
    ));

    register_post_type('job_app', array(
        'labels' => array(
            'name' => 'Job Applications',
            'singular_name' => 'Job App',
            'menu_name' => 'Job Apps',
            'all_items' => 'All Applications',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Application',
            'edit_item' => 'Edit Application',
            'new_item' => 'New Application',
            'view_item' => 'View Application',
            'search_items' => 'Search Applications',
            'not_found' => 'No applications found',
        ),
        'public' => true,
        'has_archive' => false,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-businessperson',
        'supports' => array('title', 'editor', 'custom-fields', 'author'),
        'capability_type' => 'post',
    ));
}
add_action('init', 'trumarx_register_cpts');

// We don't strictly need to register meta fields if we just add them as post meta manually
// But for REST API direct access, it helps. For this simple case, we'll let WordPress handle meta.
?>

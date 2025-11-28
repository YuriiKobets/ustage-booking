<?php

if (! defined('ABSPATH')) {
    exit;
}

function ustage_register_entertainment_taxonomies() {

    $object_types = [ 'booking', 'user' ];     
    register_taxonomy(
        'ustage_entertainment_category',
        $object_types,
        [
            'labels' => [
                'name'          => 'Entertainment Categories',
                'singular_name' => 'Entertainment Category',
                'menu_name'     => 'Entertainment Categories',
            ],
            'public'            => false,
            'show_ui'           => true,
            'show_admin_column' => false,
            'hierarchical'      => false,
            'show_in_rest'      => true,
            'capabilities'      => [
                'manage_terms' => 'manage_options',
                'edit_terms'   => 'manage_options',
                'delete_terms' => 'manage_options',
                'assign_terms' => 'read',
            ],
        ]
    );

 
    register_taxonomy(
        'ustage_entertainment_genre',
        $object_types,
        [
            'labels' => [
                'name'          => 'Entertainment Genres',
                'singular_name' => 'Entertainment Genre',
                'menu_name'     => 'Entertainment Genres',
            ],
            'public'            => false,
            'show_ui'           => true,
            'show_admin_column' => false,
            'hierarchical'      => false,
            'show_in_rest'      => true,
            'capabilities'      => [
                'manage_terms' => 'manage_options',
                'edit_terms'   => 'manage_options',
                'delete_terms' => 'manage_options',
                'assign_terms' => 'read',
            ],
        ]
    );
}
add_action( 'init', 'ustage_register_entertainment_taxonomies' );



function ustage_register_booking_cpt()
{

    $labels = [
        'name'               => 'Bookings',
        'singular_name'      => 'Booking',
        'menu_name'          => 'Bookings',
        'name_admin_bar'     => 'Booking',
        'add_new'            => 'Add Booking',
        'add_new_item'       => 'Add New Booking',
        'edit_item'          => 'Edit Booking',
        'new_item'           => 'New Booking',
        'view_item'          => 'View Booking',
        'search_items'       => 'Search Bookings',
        'not_found'          => 'No bookings found',
        'not_found_in_trash' => 'No bookings found in Trash',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-calendar-alt',
        'capability_type'    => 'post',
        'supports'           => ['title'],
        'has_archive'        => false,
        'rewrite'            => false,
    ];

    register_post_type('booking', $args);
}
add_action('init', 'ustage_register_booking_cpt');

<?php

$post_type = 'newsletterrecipient'; // type MUST == class name!!!

$recipient = New NewsletterRecipient();
$recipient->asset_url = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/';
$recipient->post_type = array(
    array(
        'name' => 'Recipient',
        'type' => $post_type,
        'rewrite' => array(
            'slug' => 'newsletter-recipient'
            ),
        'supports' => array(
            'title'
        ),
        'taxonomies' => array(
            'list'
            ),
        // 'public' => false,
        'show_ui' => false,
        'show_in_nav_menus' => false,
        'show_in_admin_bar' => false
    )
);

$recipient->taxonomy = array(
    array(
        'name' => 'list',
        'post_type' => $post_type,
        'menu_name' => 'List'
        )
);

// @todo remove 'name', derive, see method 'metaSectionRender'
$recipient->meta_sections['settings'] = array(
    'name' => 'settings',
    'label' => __('Name'),
    'fields' => array(
        array(
            'label' => 'First Name',
            'type' => 'text'
            ),
        array(
            'label' => 'Last Name',
            'type' => 'text'
            )
    )
);
<?php

$emails = New Newsletter();

$emails->asset_url = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/';
$emails->post_type = array(
    array(
        'name' => 'Newsletters',
        'type' => 'newsletter',
        'rewrite' => array(
            'slug' => 'newsletter'
            ),
        'supports' => array(
            'title',
            'editor',
            'excerpt'
        )
    )
);

if ( isset( $_GET['post'] ) )
    $sent = $emails->lastSentTime( $_GET['post'] );
else
    $sent = null;

$emails->meta_sections['stats'] = array(
    'name' => 'stats',
    'label' => __('Stats','myplugin_textdomain'),
    'context' => 'side',
    'fields' => array(
        array(
            'type' => 'description',
            'value' => '<div class="misc-pub-section">Open rate <strong>60%</strong> sent to 100</div><div class="misc-pub-section curtime misc-pub-section-last">Last sent on <strong>'.$sent.'</strong></div>'
        )
    )
);
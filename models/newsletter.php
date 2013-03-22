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

/** Want a better way, but this works for now. */
ob_start();?>
<div id="taxonomy-type" class="categorydiv">
    <ul id="type-tabs" class="category-tabs">
        <li class="tabs"><a href="#type-all" tabindex="3">Select a List </a></li>
    </ul>
    <div class="tabs-panel">
        <ul>
            <li><label><input type="checkbox" checked="checked"> All</label></li>
            <li><label><input type="checkbox"> Track Officials</label></li>
        </ul>
    </div>
</div>
<input type="button" value="Preview" class="button preview-handle" data-action="previewEmail"/>
<input name="deploy_test_email" type="button" class="button " id="dim" value="Send to test Email" />
<a href="#" class="button deploy-handle">Deploy</a>
<label>Test Email</label>
<input name="bmx_re_test_email" id="bmx_re_test_email" type="text" value="<?php print get_option('bmx_re_test_email'); ?>">
<div class="zm-status-updated status-target" style="display: none;"></div><iframe src="" class="preview-target" style="display: none;width: 100%; min-height: 50%; border: 1px dashed #CCC;"></iframe>
<?php $tmp = ob_get_clean();

$emails->meta_sections['deploy'] = array(
    'name' => 'deploy',
    'label' => __('Deploy Newsletter'),
    'fields' => array(
        array(
            'type' => 'description',
            'value' => $tmp
                )
        )
);


<?php $terms = get_terms( 'list', array( 'hide_empty' => false ) ) ;?>
<?php if ( $terms ) : ?>
    <ul class="deploy-meta-box-list">
    <?php foreach( $terms as $term ) : ?>
        <li>
            <input type="checkbox" name="list[][<?php print $term->slug; ?>]" value="<?php print $term->term_id; ?>" id="<?php print $term->slug; ?>" />
            <label for="<?php print $term->slug;?>"><?php print $term->name; ?> (<?php print $term->count; ?>)</label>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif;?>

<input type="button" value="Preview" class="button preview-handle" data-action="previewEmail"/>
<input name="deploy_test_email" type="button" class="button " id="dim" value="Send to test Email" />
<a href="#" class="button deploy-handle">Deploy</a>

<label>Test Email</label>
<input name="bmx_re_test_email" id="bmx_re_test_email" type="text" value="<?php print get_option('bmx_re_test_email'); ?>">

<?php print Newsletter::templateDropDown(); ?>

<div class="zm-status-updated status-target" style="display: none;"></div>
<iframe src="" class="preview-target" style="display: none;width: 100%; min-height: 50%; border: 1px dashed #CCC;"></iframe>
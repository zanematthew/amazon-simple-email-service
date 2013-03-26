<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>Deploy Settings</h2>
    <form action="#" method="POST">
        <table class="form-table">
            <tr>
                <th scope="row">Template</th>
                <td><?php print Newsletter::templateDropDown(); ?></td>
            </tr>
            <tr valign="top">
                <th scope="row">Deploy to the following List</th>
                <td>
                    <?php $terms = get_terms( 'list', array( 'hide_empty' => false ) ) ;?>
                    <?php if ( $terms ) : ?>
                        <?php foreach( $terms as $term ) : ?>
                            <input type="checkbox" class="deploy-list-checkbox" name="list[][<?php print $term->slug; ?>]" value="<?php print $term->term_id; ?>" id="<?php print $term->slug; ?>" />
                            <label for="<?php print $term->slug;?>"><?php print $term->name; ?> (<?php print $term->count; ?>)</label>
                            <br />
                        <?php endforeach; ?>
                    <?php endif;?>
                </td>
            </tr>
            <tr>
                <th scope="row">Test Email</th>
                <td>
                    <input name="bmx_re_test_email" id="bmx_re_test_email" class="regular-text" type="text" value="<?php print get_option('bmx_re_test_email'); ?>">
                    <p><input name="deploy_test_email" type="button" class="button " id="dim" value="Send to test Email" /></p>
                </td>
            </tr>
            <tr>
                <th scope="row">Preview Email</th>
                <td>
                    <p>You can preview how the selected template will look when viewed by recipients. Each email client may varies in how the render <code>HTML</code> in emails. Its best to use the <strong>Sent to test Email</strong> setting to get an accurate represenation on how the email will look.</p>
                    <input type="button" value="Preview" class="button preview-handle" data-action="previewEmail"/>
                    <div class="zm-status-updated status-target" style="display: none;"></div>
                    <div class="deploy-preview-container">
                        <iframe src="" class="preview-target" style="display: none; min-height: 500px; width: 640px; border: 1px dashed #CCC; width: 640px; min-height: 500px;"></iframe>
                    </div>
                </td>
            </tr>
        </table>
        <a href="#" class="button-primary deploy-handle">Deploy</a>
    </form>
</div>
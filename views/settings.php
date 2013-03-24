<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>Settings</h2>
    <form action="options.php" method="post" class="form newsletter-settings-form">
        <?php settings_fields('wpmc_plugin_options'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Amazon Security Settings</th>
                <td><input name="bmx_re_key" id="bmx_re_key" type="text" value="<?php print get_option('bmx_re_key'); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th scope="row">Secret</th>
                <td><input name="bmx_re_secret" id="bmx_re_secret" type="text" value="<?php print get_option('bmx_re_secret'); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th scope="row">Source Address</th>
                <td><input name="bmx_re_source" id="bmx_re_source" type="text" value="<?php print get_option('bmx_re_source'); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th scope="row">Default Email Footer Text</th>
                <td>
                    <p><textarea name="bmx_re_emails_footer" rows="10" cols="70" class="larget-text code"><?php print Newsletter::defaultFooterText(); ?></textarea></p>
                    <p>You may use the following template tags <code>{site_name}</code>, <code>{subscribe_link}</code> &amp; <code>{unsubscribe_link}</code></p>
                </td>
            </tr>
        </table>
        <p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" /></p>
    </form>
</div>
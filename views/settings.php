<div class="wrap">
    <h2>Settings</h2>
    <form action="options.php" method="post" class="form newsletter-settings-form">
        <?php settings_fields('wpmc_plugin_options'); ?>
        <fieldset>
            <legend>Amazon Security Settings</legend>
            <div class="control-group">
                <label class="control-label">Key</label>
                <div class="controls">
                    <input name="bmx_re_key" id="bmx_re_key" type="text" value="<?php print get_option('bmx_re_key'); ?>" class="input-large" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Secret</label>
                <div class="controls">
                    <input name="bmx_re_secret" id="bmx_re_secret" type="text" value="<?php print get_option('bmx_re_secret'); ?>" class="input-large" />
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Test Settings</legend>
            <div class="control-group">
                <label class="control-label">Source Address</label>
                <div class="controls">
                    <input name="bmx_re_source" id="bmx_re_source" type="text" value="<?php print get_option('bmx_re_source'); ?>" class="input-large" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Test Address</label>
                <div class="controls">
                    <input name="bmx_re_test_email" id="bmx_re_test_email" type="text" value="<?php print get_option('bmx_re_test_email'); ?>" class="input-large" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">Template</label>
                <div class="controls">
                    <?= Newsletter::templateDropDown(); ?>
                </div>
            </div>
        </fielset>

        <fieldset>
            <legend>Defaults</legend>
            <div class="control-group">
                <label class="control-label">Footer</label>
                <div class="controls">
                    <textarea name="bmx_re_emails_footer" rows="8"><?= Newsletter::defaultFooterText(); ?></textarea>

                <div class="meta-container">
                    <div class="content">
                        You may use the following template tags <code>{site_name}</code>, <code>{subscribe_link}</code> &amp; <code>{unsubscribe_link}</code>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="button-container">
            <input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
            <input name="deploy_emails" type="button" class="button deploy-handle" value="<?php esc_attr_e('Deploy Emails'); ?>" />
        </div>
    </form>
</div>
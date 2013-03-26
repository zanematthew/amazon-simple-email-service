<?php

$recipients_obj = New recipient;

?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br></div>
    <h2>Recipients &amp; List Settings</h2>
    <br />
    <form class="form-inline add-recipient-form" method="POST" action="#">
        <?php wp_nonce_field('addRecipient','security'); ?>
        <table class="form-table">
            <tr>
                <th scope="row">Add New Recipient</th>
                <td>
                    <input type="text" class="input-medium" placeholder="First Name" name="first_name" />
                    <input type="text" class="input-medium" placeholder="Last Name" name="last_name" />
                    <input type="text" class="input-large" placeholder="Email" name="email" />
                    <input type="submit" class="button-primary" value="Add" />
                </td>
            </tr>
        </table>
    </form>
    <br /><br />
    <table class="table table-condensed table-hover recipient-table">
        <thead>
            <tr>
                <th style="width: 10px;"><input type="checkbox"></th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>List</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach( $recipients_obj->recipientList() as $recipient ) : ?>
            <tr>
                <td><input type="checkbox" name="first_name" value="<?php print $recipient['ID']; ?>" /></td>
                <td><?php print $recipient['first_name']; ?></td>
                <td><?php print $recipient['last_name']; ?></td>
                <td><?php print $recipient['email']; ?></td>
                <td><?php
                foreach( $recipient['list'] as $list ) print $list->name . ', ';
                // $recipients_obj->recipientListSelect( $recipient['list'] );
                ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php

$recipients_obj = New newsletterRecipient;

?>

<form class="form-inline add-recipient-form" method="POST" action="#">
    <h2>Add New Recipient</h2>
    <?php wp_nonce_field('addRecipient','security'); ?>
    <input type="text" class="input-medium" placeholder="First Name" name="first_name" /><sup class="req">&#42;</sup>
    <input type="text" class="input-medium" placeholder="Last Name" name="last_name" /><sup class="req">&#42;</sup>
    <input type="text" class="input-large" placeholder="Email" name="email" /><sup class="req">&#42;</sup>
    <input type="submit" class="button-primary" value="Add" />
</form>

<table class="table table-condensed table-hover recipient-table">
    <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email Name</th>
            <th>List</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach( $recipients_obj->recipientList() as $recipient ) : ?>
        <tr>
            <td><input type="checkbox" name="first_name" value="<?php print $recipient['ID']; ?>" /><?php print $recipient['first_name']; ?></td>
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
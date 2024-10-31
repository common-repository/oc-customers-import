<?php

defined( 'ABSPATH' ) or http_response_code(404);

function occi_add_customer($customer) {
    global $wpdb;

    $find_query = sprintf("SELECT * FROM `$wpdb->users` WHERE `user_email` = '%s'", $customer->email);
    $insert_query = sprintf("INSERT INTO `$wpdb->users` (`user_login`, `user_pass`, `display_name`, `user_email`, `user_registered`) VALUES ('%s', '%s', '%s', '%s', '%s')",
        $customer->email, // set email as username
        $customer->password . '+' . $customer->salt,
        $customer->firstname,
        $customer->email,
        $customer->date_added
    );
    // Email Check;
    $count = $wpdb->query($find_query);
    if ($count > 0) {
        echo sprintf("<p>User already exist! email: %s</p>", $customer->email);
        return false;
    }
    // Insert & Get User ID
    $wpdb->query($insert_query);
    $user_id = $wpdb->insert_id;
    // Set User As a Customer
    add_user_meta($user_id, 'first_name', $customer->firstname);
    add_user_meta($user_id, 'last_name', $customer->lastname);
    add_user_meta($user_id, 'wp_capabilities', [
        'customer' => true
    ]);
    return $user_id;
}

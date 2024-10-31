<?php
/*
Plugin Name: OC Customers Import
Plugin URI: http://wordpress.org/plugins/oc-customers-import
Description: Import OpenCart Customers
Author: Mostafa Saeed
Version: 1.1.0
Author URI: mailto:mostafa.saeed543@gmail.com
*/

defined( 'ABSPATH' ) or http_response_code(404);

require_once('import_form.php');
require_once('add_customer.php');

function occi_is_sha1($str) {
    return (bool) preg_match('/^[0-9a-f]{40}$/i', $str);
}

// Clean JSON String From Any Comments
function occi_json_clean_decode($json, $assoc = false, $depth = 512, $options = 0) {
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
    if (version_compare(phpversion(), '5.4.0', '>=')) { 
        return json_decode($json, $assoc, $depth, $options);
    }
    elseif (version_compare(phpversion(), '5.3.0', '>=')) { 
        return json_decode($json, $assoc, $depth);
    }
    else {
        return json_decode($json, $assoc);
    }
}

// Extract Customers Array From JSON
function occi_extract_customers($json) {
    // If not array or empty return []
    if (!is_array($json) || empty($json)) {
        return [];
    }

    // If first element has 'customer_id' return
    if (isset($json[0]->customer_id)) {
        return $json;
    }

    // Loop and find customers table
    foreach ($json as $entity) {
        if ($entity->type == 'table' && strpos($entity->name, '_customer') !== false) {
            return $entity->data;
        }
    }
    
}

// Login check
add_filter('authenticate', 'occi_check_login', 10, 3);
function occi_check_login($user, $email, $password) {
    // Get user by the email
    $userinfo = get_user_by('email', $email);
    if (!$userinfo) return false;
    // Check if the password is imported using the plugin
    if ($userinfo->data->user_pass[40] != '+') return false;
    // Check if the password is sha1
    $hashed_password = explode('+', $userinfo->data->user_pass);
    $hash = $hashed_password[0];
    $salt = $hashed_password[1];
    if (occi_is_sha1($hash)) {
        // Check if user entered password matches the hashed password
        $oc_password = sha1($salt . sha1($salt . sha1($password)));
        if ($oc_password == $hash) {
            // Update the user
            wp_set_password($password, $userinfo->ID);
        }
    }
}

// Register Import Form
add_action ('admin_menu', 'occi_register_import_page');
function occi_register_import_page() {
    return add_users_page (
        'OpenCart Customers Import',
        'OpenCart Customers Import',
        'manage_options',
        'op-customers-import',
        'occi_import_form'
      );
}

<?php

defined( 'ABSPATH' ) or http_response_code(404);

function occi_import_form() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $file = $_FILES['customers'];
        // Validate Input
        if ($file['type'] == 'application/json' && $file['error'] == 0) {
            // Read File Contents
            $file_content = file_get_contents($file['tmp_name']);
            // Parse JSON Data
            $parsed_json = occi_json_clean_decode($file_content);
            // Get The Customers Array
            $customers = occi_extract_customers($parsed_json);
            // Insert Each Customer
            $added = array_filter($customers, 'occi_add_customer');

            echo sprintf("Added %d of %d.", count($added), count($customers));
        }
        else {
            echo 'Wrong File Format!';
        }
    }
    ?>

    <h2>OpenCart Customers Import</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" id="customers" name="customers" accept="application/json">
        <input type="submit" value="submit">
        <label style="display: block;" for="customers">Export oc_customers table from your OpenCart database as JSON & Upload oc_customers.json file here</label>
    </form>

    <?php
}

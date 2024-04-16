<?php
/*
Plugin Name: CSV Importer
Description: This plugin allows importing CSV files and updating data in the WordPress database.
Version: 1.0
Author: Your Name
*/

// Activation hook
register_activation_hook(__FILE__, 'csv_importer_activate');
function csv_importer_activate() {
    // Include WordPress upgrade file for dbDelta function
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    global $wpdb;

    // Table name to create
    $table_name = $wpdb->prefix . 'tamilcalender'; // Replace 'your_table_name' with your actual table name

    // SQL query to create the table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        year INT(11),
        tamildate INT(11),
        tamilmonth VARCHAR(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        ttmonth VARCHAR(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        tamilyear VARCHAR(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        ttyear VARCHAR(100),
        dow VARCHAR(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        englishdate INT(11),
        engmonth VARCHAR(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Tithi VARCHAR(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        tithi1 VARCHAR(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Tithihrs VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        tithifreetext VARCHAR(248) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        festival VARCHAR(184) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        desc1 VARCHAR(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        desc2 VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        desc3 VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        desc4 VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        file VARCHAR(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Nakshatra VARCHAR(26) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Nakhrs VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Nakshatra1 VARCHAR(23) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Nak1hrs VARCHAR(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Tithi2 VARCHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Tithi2hrs VARCHAR(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Yoga VARCHAR(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Yogahrs VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Yoga1 VARCHAR(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Yoga1hrs VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Karana VARCHAR(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Karanahrs VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Karana1 VARCHAR(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Karana1hrs VARCHAR(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Karana2 VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        Karana2hrs VARCHAR(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    

    // Execute SQL query
    dbDelta($sql);
}

// Function to handle CSV import
function handle_csv_import() {
    if (isset($_FILES['csv_file'])) {
        $csv_file = $_FILES['csv_file']['tmp_name'];

        // Check if file is a valid CSV file
        if (is_uploaded_file($csv_file) && pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) === 'csv') {
            // Parse CSV file
            $csv_data = array_map('str_getcsv', file($csv_file));

            // Assuming the first row contains column headers
            $headers = array_shift($csv_data);

            global $wpdb;

            // Table name to insert data into
            $table_name = $wpdb->prefix . 'tamilcalender'; // Replace 'your_table_name' with your actual table name

            foreach ($csv_data as $row) {
                $data = array_combine($headers, $row);
                // Insert data into database
                $wpdb->insert($table_name, $data);
            }

            echo '<div class="updated"><p>CSV file imported successfully!</p></div>';
        } else {
            echo '<div class="error"><p>Invalid CSV file. Please upload a valid CSV file.</p></div>';
        }
    }
}

// Function to add plugin to dashboard menu
function csv_importer_add_to_menu() {
    add_submenu_page(
        'tools.php', // Parent menu slug
        'CSV Importer', // Page title
        'CSV Importer', // Menu title
        'manage_options', // Capability required
        'csv-importer', // Menu slug
        'csv_importer_page' // Callback function to render the page
    );
}

// Callback function to render the menu page
function csv_importer_page() {
    // Display HTML form for file upload
    ?>
    <div class="wrap">
        <h2>CSV Importer</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv">
            <input type="submit" name="submit" value="Import CSV">
        </form>
    </div>
    <?php

    // Handle CSV import when form is submitted
    handle_csv_import();
}

// Hook the function to add menu item into WordPress
add_action('admin_menu', 'csv_importer_add_to_menu');

?>

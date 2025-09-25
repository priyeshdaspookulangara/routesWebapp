<?php
// Include config file
require_once "../config/database.php";

// Function to get the database connection
function get_db_connection() {
    global $link;
    return $link;
}
?>
<?php
//delete_note.php

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "project";

// Establish a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the note_id is set and numeric
if (isset($_POST['note_id']) && is_numeric($_POST['note_id'])) {
    $noteID = $_POST['note_id'];

    // Delete the note from the database
    $sql = "DELETE FROM notes WHERE note_id = $noteID";
    $conn->query($sql);
}

// Close the database connection
$conn->close();
?>


<?php
//update_note.php

$noteID = $_POST['note_id'];
$title = $_POST['note_title'];
$content = $_POST['note_content'];

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "project";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update the note in the database
$sql = "UPDATE notes SET title='$title', content='$content' WHERE note_id=$noteID";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

// Close the database connection
$conn->close();
?>



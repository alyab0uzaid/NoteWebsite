<?php
// home.php
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

// Start the session
session_start();

// Check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Retrieve user information from the session
$username = $_SESSION['username'];
$userID = $_SESSION['user_id'];

// Handle New Note
if (isset($_GET['new_note'])) {
    $newNoteID = createNewNote($conn, $userID);
    header("Location: home.php?note_id=$newNoteID");
    exit();
}

// Handle Saving Note
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $noteID = $_POST['note_id'];
    $title = $_POST['note_title'];
    $content = $_POST['note_content'];
    
    saveNote($conn, $userID, $noteID, $title, $content);
}

// Fetch notes from the database
$sql = "SELECT note_id, title, content FROM notes WHERE user_id = $userID";
$result = $conn->query($sql);

$notes = [];
while ($row = $result->fetch_assoc()) {
    $notes[] = $row;
}

// Determine the selected note
$selectedNoteID = isset($_GET['note_id']) ? $_GET['note_id'] : null;

// Close the database connection
$conn->close();

function createNewNote($conn, $userID) {
    $sql = "INSERT INTO notes (user_id, title, content) VALUES ($userID, '', '')";
    $conn->query($sql);
    return $conn->insert_id;
}

function saveNote($conn, $userID, $noteID, $title, $content) {
    $sql = "UPDATE notes SET title='$title', content='$content' WHERE note_id=$noteID AND user_id=$userID";
    $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Note Taking App</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>

<div class="container">

<!-- Sidebar -->
<div class="sidebar">
    <button class="new-note-btn" onclick="location.href='home.php?new_note'">
    <img src="newnote.png" class = "noteimg" >New Note</button>
    <ul class="notes-list">
        <?php foreach ($notes as $note): ?>
            <li class="note-item">
                <button class="note-button <?php echo ($note['note_id'] == $selectedNoteID) ? 'selected' : ''; ?>" onclick="openNoteEditor(<?php echo $note['note_id']; ?>)">
                    <span class="note-title"><?php echo empty($note['title']) ? 'Untitled' : htmlspecialchars($note['title']); ?></span>
                    <span class="delete-button" onclick="deleteNote(<?php echo $note['note_id']; ?>)">×</span>
                </button>
            </li>
        <?php endforeach; ?>
    </ul>
</div>


<script>
    function openNoteEditor(noteId) {
        // Implement the logic to open the note editor for the specified noteId
        window.location.href = 'home.php?note_id=' + noteId;
    }

    function deleteNote(noteId) {
        // Implement the logic to delete the note with the specified noteId
        if (confirm('Are you sure you want to delete this note?')) {
            // Send an AJAX request to delete the note
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_note.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // After successful deletion, reload the page or update the sidebar as needed
                    window.location.reload();
                }
            };
            xhr.send("note_id=" + noteId);
        }
    }
</script>

        <!-- Main content area -->
        <div class="main-content">

            <?php foreach ($notes as $note): ?>
                <?php $isCurrentNoteSelected = ($note['note_id'] == $selectedNoteID); ?>

                <!-- Note input form -->
                <form class="note-form" data-note-id="<?php echo $note['note_id']; ?>" <?php echo $isCurrentNoteSelected ? '' : 'style="display: none;"'; ?>>
                    <input type="hidden" name="note_id" value="<?php echo $note['note_id']; ?>">
                    <input type="text" name="note_title" class="note-title-input" value="<?php echo htmlspecialchars($note['title']); ?>" placeholder="Untitled" required>
                    <textarea name="note_content" class="note-content-textarea" placeholder="Type here..." required><?php echo htmlspecialchars($note['content']); ?></textarea>
                </form>

            <?php endforeach; ?>

        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Listen for input changes in note forms
            $('.note-form textarea, .note-form input').on('input', function() {
                // Get the note form data
                var formData = $(this).closest('form').serialize();

                // Send an AJAX request to update the note
                $.ajax({
                    type: 'POST',
                    url: 'update_note.php', // Replace with the actual endpoint for updating notes
                    data: formData,
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(error) {
                        console.error('Error updating note:', error);
                    }
                });
            });
        });
    </script>

</body>
</html>

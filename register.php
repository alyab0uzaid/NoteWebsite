<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "project";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'registration' table exists
$tableCheck = "SHOW TABLES LIKE 'registration'";
$result = $conn->query($tableCheck);

if ($result->num_rows == 0) {
    // The 'registration' table does not exist, so create it
    $createTableSQL = "CREATE TABLE registration (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL
    )";

    if ($conn->query($createTableSQL) === TRUE) {
        echo "Table 'registration' created successfully. ";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

// Check if the 'notes' table exists
$tableCheck = "SHOW TABLES LIKE 'notes'";
$result = $conn->query($tableCheck);

if ($result->num_rows == 0) {
    // The 'notes' table does not exist, so create it
    $createTableSQL = "CREATE TABLE notes (
        note_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES registration(id)
    )";

    if ($conn->query($createTableSQL) === TRUE) {
        echo "Table 'notes' created successfully. ";
    } else {
        echo "Error creating table: " . $conn->error;
    }
}

$successMsg = $errorMsg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    $checkUsernameQuery = "SELECT * FROM registration WHERE username = '$username'";
    $result = $conn->query($checkUsernameQuery);

    if ($result->num_rows > 0) {
        $errorMsg = "Username is already taken.";
    } else {
        $sql = "INSERT INTO registration (username, password) VALUES ('$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            header("Location: home.php");
            exit();
        } else {
            $errorMsg = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Page</title>
    <link rel="stylesheet" href="stylelogin.css">
    <style>
        .error-message {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-content">
            <p class="welcome-text">Secure Clearance Application</p>
            <form action="register.php" method="post" autocomplete="off">
                <div>
                    <p class="mb-1">Username</p>
                    <input type="text" name="username" autocomplete="new-password">
                </div>
                <div>
                    <p class="mb-1">Password</p>
                    <input type="password" name="password" autocomplete="new-password">
                </div>

                <?php
                if (!empty($errorMsg)) {
                    echo '<p class="error-message">' . $errorMsg . '</p>';
                }
                ?>

                <div class="text-center my-4">
                    <button class="submit" type="submit">SUBMIT</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

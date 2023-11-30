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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input from the form
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate the login credentials
    $sql = "SELECT * FROM registration WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User is registered, check password
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        if (password_verify($password, $hashedPassword)) {
            // Password is correct, set a session variable and redirect
            session_start();
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $row['id']; // Set the user_id in the session
            header("Location: home.php");
            exit();
        } else {
            // Password is incorrect, display error message
            $errorMsg = "Invalid password. Please try again.";
        }
    } else {
        // User is not registered, display error message
        $errorMsg = "Invalid username. Please try again or register.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="stylelogin.css">
</head>
<body>
    <div class="screen">
        <div class="login-box">
            <div class="login-content">
                <p class="welcome-text">Welcome HABIBI</p>
                <form action="index.php" method="post" autocomplete="off">
                    <div>
                        <p class="mb-1">Username</p>
                        <input type="text" name="username" autocomplete="new-password">
                    </div>
                    <div>
                        <p class="mb-1">Password</p>
                        <input type="password" name="password" autocomplete="new-password">
                    </div>
                    <div class="text-center my-4">
                        <a class="register" href="register.php">Not registered?</a>
                    </div>
                    <div class="text-center my-4">
                        <button class="submit" type="submit">OK</button>
                    </div>

                    <?php
                    // Display the error message if it is set
                    if (isset($errorMsg)) {
                        echo '<p class="error-message">' . $errorMsg . '</p>';
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

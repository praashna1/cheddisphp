<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "cheddis";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $input_username = htmlspecialchars($_POST['username']);
    $input_email = htmlspecialchars($_POST['email']);
    $input_password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = 'factory'; // Assuming all signups from this page are factories
}
    if (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $input_username, $input_email, $input_password, $role);
        if ($stmt->execute()) {
            echo "Signup successful!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    
    mysqli_close($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
</head>
<body>
    <form action="sign.php" method="post">
        Username: <input type="text" name="username" required><br>
        Email: <input type="email" name="email" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" value="Signup">
    </form>
</body>
</html>

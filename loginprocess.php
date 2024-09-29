<?php
require 'includes/validate.php';

require 'includes/database.php';

$conn = getDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = getUser($conn, $username);
    if (empty($username) || empty($password)) {
        echo 'Username or Password is empty';
    } else {
        $conn = getDB();
        $sql = "SELECT * FROM user WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        
        if ($stmt === false) {
            echo mysqli_error($conn);
        } elseif(!$user){
            echo 'no user found';
        }else {

            if ($username == $user['username'] && $password == $user['password']) {
                // Successfully authenticated
                echo "Login successful";
                echo $user['username'];
                // Start session and redirect to the homepage or dashboard
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit();
            } else {
                $error= "Invalid username or password";
                header("Location: login.php?error=" . urlencode($error));
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
    }
}
session_start();

?>
<?php
require 'includes/validate.php';

require 'includes/database.php';

$conn = getDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $user = getFactory($conn, $name);
    if (empty($user) || empty($password)) {
        echo 'Username or Password is empty';
    } else {
        $conn = getDB();
        $sql = "SELECT * FROM factory WHERE name = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        
        if ($stmt === false) {
            echo mysqli_error($conn);
        } elseif(!$user){
            echo 'no user found';
        }else {

            if ($name == $user['name'] && $password == $user['password']) {
                // Successfully authenticated
                echo "Login successful";
                echo $user['user'];
                // Start session and redirect to the homepage or dashboard
                session_start();
                $_SESSION['factory_id'] = $user['factory_id'];
                $_SESSION['name'] = $user['name'];
                header("Location: layout.php");
                exit();
               
            } else {
                $error= "Invalid username or password";
                header("Location: factlogin.php?error=" . urlencode($error));
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
    }
}
session_start();

?>
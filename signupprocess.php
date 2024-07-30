<?php
require 'includes/validate.php';

require 'includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST['username'];
    $email = $_POST['email'];
   
    $password = $_POST['password'];
   
    $confirmPassword = $_POST['confirmPassword'];
   

    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo 'One or more fields are empty';
    } elseif ($password !== $confirmPassword) {
        echo "Passwords do not match";
    } elseif(!validateEmail($email)){
        echo "Invalid Email";
    }else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $conn = getDB();

        $sql = "INSERT INTO user(username, email, password) VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
        mysqli_stmt_execute($stmt);

        session_start();
        $_SESSION['user_id']=$user['user_id'];
        $_SESSION['username']=$user['username'];
        header("Location:login.php");
        exit();
        
        if ($stmt === false) {
            echo mysqli_error($conn);
        } else {
            
            echo "Signup successful";
            mysqli_stmt_close($stmt);
        }
        mysqli_close($conn);
    }
}

?>
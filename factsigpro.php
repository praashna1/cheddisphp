<?php
require 'includes/validate.php';

require 'includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = $_POST['name'];
    $email = $_POST['email'];
   
    $password = $_POST['password'];
   
    $confirmPassword = $_POST['confirmPassword'];
   

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo 'One or more fields are empty';
    } elseif ($password !== $confirmPassword) {
        echo "Passwords do not match";
    } elseif(!validateEmail($email)){
        echo "Invalid Email";
    }else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $conn = getDB();

        $sql = "INSERT INTO factory(name, email, password) VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
        mysqli_stmt_execute($stmt);

        session_start();
        $_SESSION['factory_id']=$user['factory_id'];
        $_SESSION['name']=$user['name'];
        header("Location:factsign.php");
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
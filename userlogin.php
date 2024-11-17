<?php 

session_start();
require 'includes/validate.php';

require 'includes/database.php';

$conn=getDB();

if (isset($_POST['email'])) {
    $user= getUser($conn, $_POST['email']);
    if ($user) {
        $username=$user['username'];
        $email=$user['email'];
        $password=$user['password'];
       
    }
    else{
        die("User not found");
    }
}
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $formEmail=$_POST['email'];
    $formPass=$_POST['password'];
    if ($formEmail=='' || $formPass=='') {
        echo 'One or more fields are empty';
    }
    elseif ($formPass==$password) {
        session_start();

        $_SESSION['loggedIn'] = true;
        $_SESSION['email'] = $email;
        $_SESSION['userId']=$user_id;
       
        header("Location:index.php");
        exit;
    }else{
        echo 'Invalid credentials';
    }
}

$conn->close();

?>
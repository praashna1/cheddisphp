<?php
require_once 'includes/validate.php';
require_once 'includes/database.php';

$conn = getDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user = getFactory($conn, $email);

    if (empty($email) || empty($password)) {
        $error = 'Username or Password is empty';
        header("Location: factlogin.php?error=" . urlencode($error));
        exit();
    } else {
        $sql = "SELECT * FROM factory WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$user || $result->num_rows === 0) {
            $error = 'No user found';
            header("Location: factlogin.php?error=" . urlencode($error));
        } else {
            $user = mysqli_fetch_assoc($result);
            if ($email== $user['email'] && $password == $user['password']) {
                session_start();
                $_SESSION['factory_id'] = $user['factory_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['name'] = $user['name']; 

                header("Location: layout.php");
            } else {
                $error = "Invalid username or password";
                header("Location: factlogin.php?error=" . urlencode($error));
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

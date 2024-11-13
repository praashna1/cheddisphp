<?php
require_once 'includes/validate.php';

require_once 'includes/database.php';

$conn = getDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $user = getFactory($conn, $name);
    if (empty($user) || empty($password)) {
        $error= 'Username or Password is empty';
        header("Location: factlogin.php?error=" . urlencode($error));
        exit();
    } else {
        $conn = getDB();
        $sql = "SELECT * FROM factory WHERE name = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$user || $result->num_rows === 0) {
            $error = 'No user found';
            header("Location: factlogin.php?error=" . urlencode($error));
        } else {

            $user = mysqli_fetch_assoc($result);
            if ($name == $user['name'] && $password == $user['password']) {
                session_start();
                $_SESSION['factory_id'] = $user['factory_id'];
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
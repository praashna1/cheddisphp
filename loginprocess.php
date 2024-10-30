<?php
require_once 'includes/validate.php';
require_once 'includes/database.php';

$conn = getDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = getUser($conn, $username);

    if (empty($username) || empty($password)) {
        $error = 'Username or Password is empty';
        header("Location: login.php?error=" . urlencode($error));
        exit();
    } else {
        $sql = "SELECT * FROM user WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (!$user || $result->num_rows === 0) {
            $error = 'No user found';
            header("Location: login.php?error=" . urlencode($error));
        } else {
            $user = mysqli_fetch_assoc($result);
            if ($username == $user['username'] && $password == $user['password']) {
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
            } else {
                $error = "Invalid username or password";
                header("Location: login.php?error=" . urlencode($error));
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}
?>

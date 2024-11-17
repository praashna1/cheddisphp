<?php
require_once 'includes/validate.php';
require_once 'includes/database.php';

$conn = getDB();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
    $errors = [];

    // Validation checks
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $errors[] = 'All fields are required.';
    }
    if (!validateUsername($username)) {
        $errors[] = 'Invalid username format. Only alphanumeric characters are allowed.';
    }
    if (!validateEmail($email)) {
        $errors[] = 'Invalid email format.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }
    if (!validatePassword($password)) {
        $errors[] = 'Password must be at least 8 characters long, contain an uppercase letter, a number, and a special character.';
    }

    if (empty($errors)) {
        $existingUser = getUse($conn, $username);
        if ($existingUser) {
            $errors[] = 'Username already exists. Please choose a different username.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO user (username, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $passwordHash);

            if (mysqli_stmt_execute($stmt)) {
                session_start();
                $_SESSION['user_id'] = mysqli_insert_id($conn);
                $_SESSION['username'] = $username;
                header("Location: login.php");
                exit();
            } else {
                $errors[] = 'An error occurred while creating your account. Please try again later.';
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<section>
    <div class="signin">
        <div class="content">
            <h2>Sign Up</h2>
            <div class="form">
                <?php
                if (!empty($errors)) {
                    echo '<div class="error-message">';
                    foreach ($errors as $error) {
                        echo '<p>' . htmlspecialchars($error) . '</p>';
                    }
                    echo '</div>';
                }
                ?>
                <form action="signupprocess.php" method="post" id="signup-form">
                    <div class="inputBox">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                    </div>
                    <div class="inputBox">
                        <label for="email">Email:</label>
                        <input type="text" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                    </div>
                    <div class="inputBox">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="inputBox">
                        <label for="confirmPassword">Confirm Password:</label>
                        <input type="password" name="confirmPassword" id="confirmPassword" required>
                    </div>
                    <div class="links">
                        <a href="login.php">Already have an account? Login</a>
                    </div>
                    <div class="inputBox">
                        <input type="submit" value="Sign Up">
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</body>
</html>

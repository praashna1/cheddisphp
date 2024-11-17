<?php
require_once 'includes/database.php'; 
$conn = getDB(); 

function getUser($conn, $email){
    $sql = "SELECT * FROM user WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result); // User exists
            } else {
                return null; // User does not exist
            }
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}
function getUse($conn, $username) {
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $user;
}


function getFactory($conn, $email){
    $sql = "SELECT * FROM factory WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
        
            if (mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result); // Factory exists
            } else {
                return null; // Factory does not exist
            }
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

function validateEmail($email) {
    $pattern = '/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    
    if (preg_match($pattern, $email)) {
        return true; // Email is valid
    } else {
        return false; // Email is not valid
    }
}

// Function to validate username
function validateUsername($username) {
    
    return preg_match('/^[a-zA-Z]+$/', $username);
}
//for factory
function validateName($name) {
    // Allow only alphabetic characters and spaces
    return preg_match('/^[a-zA-Z\s]+$/', $name);
}

// Function to validate password
function validatePassword($password) {
    $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[!@#$%^&*()_+{}:"<>?[\];\',.\/`~\\|-]).{8,}$/';
    
    if (preg_match($pattern, $password)) {
        return true; // Password is valid
    } else {
        return false; // Password is not valid
    }
}

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
//     $username = isset($_POST['username']) ? $_POST['username'] : '';
//     $email = isset($_POST['email']) ? $_POST['email'] : '';

//     // Validate username
//     if (!validateUsername($username)) {
//         echo "Invalid username format. Only alphanumeric characters are allowed.";
//     } elseif (!validateEmail($email)) {
//         echo "Invalid email format.";
//     } else {
//         // Proceed with user or factory registration based on logic
//         $user = getUser($conn, $username);
//         if ($user) {
//             echo "Username already exists.";
//         } else {
           
//         }
//     }
// }
?>

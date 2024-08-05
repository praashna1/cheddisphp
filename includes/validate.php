<?php


function getUser($conn, $email){
    $sql = "SELECT * FROM user WHERE username=?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            // Check if any rows were returned
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

function getFactory($conn, $email){
    $sql = "SELECT * FROM factory WHERE name=?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            
            // Check if any rows were returned
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

function validateEmail($email) {
    // Regular expression pattern for email validation
    $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    
    // Validate email address
    if (preg_match($pattern, $email)) {
        return true; // Email is valid
    } else {
        return false; // Email is not valid
    }
}

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $username = $_POST['username'];
//     $email = $_POST['email'];
    
//     if (!validateUsername($username)) {
//         echo "Invalid username format. Only alphanumeric characters are allowed.";
//     } elseif (!validateEmail($email)) {
//         echo "Invalid email format.";
//     } else {
//         // Proceed with registration
//         $user = getUser($conn, $username);
//         if ($user) {
//             echo "Username already exists.";
//         } else {
//             // Register the user
//             // Insert code to handle registration
//         }
//     }
// }

?>
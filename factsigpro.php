<?php
require 'includes/validate.php';

require 'includes/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $name = $_POST['name'];
    $email = $_POST['email'];
   
    $password = $_POST['password'];
   
    $confirmPassword = $_POST['confirmPassword'];
   

  
try{
        $stmt= $conn->prepare("INSERT INTO factory(name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param($stmt, "sss", $name, $email, $password);
        if($stmt_execute()){
        header("Location:factsign.php");
        exit();
    }else{
        throw new Exception("failed to execute");
    }
}catch(mysqli_sql_exception $e){
        $error= "Invalid email";
        header("Location: factsign.php?error=" . urlencode($error));
        exit();
    }catch(Exception $e){
        $error= "Invalid email";
        header("Location: factsign.php?error=" . urlencode($error));
    }
        finally{
            $stmt->close();
            $conn->close();
        }
    }


?>

<?php
require 'header.php';
require 'includes/database.php';
require 'includes/validate.php';

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $fname=$_POST['fname'];
    $lname=$_POST['lname'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $confirmPass=$_POST['confirmPass'];
    
    if ($fname=='' || $lname=='' || $email=='' || $password=='' || $confirmPass=='') {
        echo 'One or more fields are empty';
    }
    elseif(validateEmail($email)==false){
        echo "invalid email";
    }
    elseif($password==$confirmPass){
            $conn=getDB();
            $sql="UPDATE login SET fname=?,lname=?,email=?,password=? WHERE fname=? ";//?is a placeholder for record item
            $stmt=mysqli_prepare($conn,$sql);
            if($stmt===false){
                echo mysqli_error($conn);
    
            }else{
                mysqli_stmt_bind_param($stmt,"sssss",$fname,$lname,$email,$password,$username);
                // here ss is to pass string values and i is to pass integer values
                mysqli_stmt_execute($stmt);
  
                }    
               
            }
        }

        $conn = getDB();
        $sql = 'SELECT * FROM customer WHERE FullName=?';
        $stmt=mysqli_prepare($conn,$sql);
            if($stmt===false){
                echo mysqli_error($conn);
            }else{
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);
            }

?>
<!-- <div class="form-container">
<form action="" method="post">
<div>
            <label for="fname">First Name:</label>
            <input type="text" name="fname" id="fname" value="<?php echo $user['fname'] ?? ''; ?>">
        </div>
        <div>
            <label for="lname">Last Name:</label>
            <input type="text" name="lname" id="lname" value="<?php echo $user['lname'] ?? ''; ?>">
        </div>
    <div>
        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="<?php echo $user['email'] ?? ''; ?>">
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" value="<?php echo $user['password'] ?? ''; ?>">
    </div>
    <div>
        <label for="confirmPass">Confirm Password:</label>
        <input type="password" name="confirmPass" id="confirmPass" value="<?php echo $user['password'] ?? ''; ?>">
    </div><br>

    <button class="save" name="save">Save</button><br>
</form><br>
</div>
<?php require 'footer.php'; ?> -->

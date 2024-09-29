
<link rel="stylesheet" href="style.css">
<body>
    <section>
    <div class="signin">
        <div class="content">
            <h2>Login</h2>
            <div class="form">
                <div class="inputBox">
                <form action="LoginProcess.php" method="post" class="form" id="login-form">
                    <div>
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="links">
              <a href="#">Already have an account?</a>
              <a href="signup.php">Signup</a>
            </div>
            <div class="inputBox">
              <input type="submit" value="Login" />
            </div>  
            <?php 
            if(isset($_GET['error'])){
                echo '<div class="error-message">'.htmlspecialchars($_GET['error']).'</div>';
            }
            ?> 
                </form>
            </div>
        </div>
    </div>
</div>
</section>
</body>

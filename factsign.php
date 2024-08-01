
<link rel="stylesheet" href="styles.css">
<body>
<section>
<div class="signin">
    <div class="content">
       <h2>Sign In </h2>
            <div class="form">
                <div class="inputBox">
                    <form action="factsigpro.php" method="post" id="signup-form">
                        <div class="inputBox">
                            <label for="name">User Name:</label>
                            <input type="text" name="name" id="name" required>
                        </div>
                        <div class="inputBox">
                            <label for="email">Email:</label>
                            <input type="text" name="email" id="email" required>
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
              <a href="#">Already have an account?</a>
              <a href="factlogin.php">Login</a>
            </div>
            <div class="inputBox">
              <input type="submit" value="Signup" />
            </div>    
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
</section>
</body>

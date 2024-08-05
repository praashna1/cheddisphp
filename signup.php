
<link rel="stylesheet" href="style.css">
<body>
<section>
<div class="signin">
    <div class="content">
       <h2>Sign In </h2>
            <div class="form">
                <div class="inputBox">
                    <form action="SignupProcess.php" method="post" id="signup-form">
                        <div class="inputBox">
                            <label for="username">User Name:</label>
                            <input type="text" name="username" id="username" required>
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
<!--                     
                        <script>
    document.getElementById("username").addEventListener("input", function () {
        var username = this.value;
        var pattern = /^[a-zA-Z0-9]+$/;
        if (!pattern.test(username)) {
            alert("Username should not contain special characters or spaces.");
            this.value = "";
        }
    });

    document.getElementById("email").addEventListener("input", function () {
        var email = this.value;
        var pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!pattern.test(email)) {
            alert("Please enter a valid email address.");
        }
    });
</script> -->
                        
                        <div class="links">
              <a href="#">Already have an account?</a>
              <a href="login.php">Login</a>
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

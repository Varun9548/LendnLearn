<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - LendnLearn</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header>
        <h2>User Login</h2>
    </header>
    <main>
        <div class="login-container"> <!-- Added a container div here -->
            <form id="loginForm" action="login_verify.php" method="POST">
                <input type="hidden" name="login_type" value="user">
                <label for="Email">Email</label>
                <input type="text" id="email" name="email" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit" id="loginButton">Login</button>
            </form>

            <!-- Registration prompt placed outside of the form -->
            <p style="text-align: center; margin-top: 20px;">
                New User? <a href="registration.html" id="registerLink">Register Here</a>
            </p>

        </div> <!-- Closing the container div -->
        <?php if(isset($_REQUEST["msg"])){ ?>
        <div class="form-group">
            <div class="col-sm-12 text-center alert-danger">
                <?php echo $_REQUEST["msg"];?>
            </div>
        </div>
        <?php }?>
    </main>

    <!--<script src="login.js"></script>-->
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - E-Library</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header>
        <h2>Admin Login</h2>
    </header>
    <main>
        <div class="login-container">
            <form action="login_verify.php" method="POST">
                <input type="hidden" name="login_type" value="admin">

                <label for="email">Admin Email</label>
                <input type="text" id="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login as Admin</button>
            </form>


        </div>

        <?php if(isset($_REQUEST['msg'])){ ?>
        <div class="form-group">
            <div class="col-sm-12 text-center alert-danger">
                <?php echo htmlspecialchars($_REQUEST['msg']); ?>
            </div>
        </div>
        <?php } ?>
    </main>
</body>
</html>

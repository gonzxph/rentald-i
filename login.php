<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php' ?>
    <title>Login you account</title>
</head>
<body>
    <?php include 'includes/nav.php' ?>

    <div class="container">
        <div class="card mx-auto mt-5" style="max-width: 350px;">
            <h2 class="mx-auto mt-3">Login</h2>
            <form action="./backend/login_handler.php" method="POST" class="m-3">
                 <!-- Display Error Message -->
                 <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
                <?php elseif (isset($_GET['success'])): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group mb-3">
                    <label class="form-label" for="email">Email address</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="john.doe@email.com"/>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="*******" />
                </div>
                <a href="#" class="float-end mb-3">Forgot password?</a><br>
                <button type="submit" class="btn btn-primary mt-3 mx-auto d-block w-100">Login</button>

            </form>
        </div>
    </div>

</body>
<?php include 'includes/scripts.php' ?>
</html>

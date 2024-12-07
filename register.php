<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'includes/head.php' ?>
    <title>Create account</title>
</head>
<body>

    <?php include 'includes/nav.php' ?>
    
    <div class="container">
            <div class="card mx-auto mt-5" style="max-width: 390px;">
                <h2 class="mx-auto mt-3">Create an account</h2>
                <form action="./backend/register_handler.php" method="POST" class="m-3">
                     <!-- Display Error or Success Messages -->
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
                        <label class="form-label" for="firstname">Firstname</label>
                        <input type="text" id="firstname" name="firstname" class="form-control" placeholder="John"/>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="lastname">Lastname</label>
                        <input type="text" id="lastname" name="lastname" class="form-control" placeholder="Doe"/>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="john.doe@email.com"/>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="*******" />
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label" for="confirm_pass">Confirm Password</label>
                        <input type="password" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="*******" />
                    </div>
                    <div id="passErr" class="text-danger" style="display: none;">
                        Passwords do not match.
                    </div>
                    <div id="emptyErr" class="text-danger" style="display: none;">
                        Make sure no field is empty.
                    </div>
                    <button type="submit" class="btn btn-primary mt-5 mx-auto d-block w-100">Sign up</button>

                </form>
        </div>
    </div>

</body>
</html>
<?php
session_start();
include 'includes/header.php';
?>

<div class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if (isset($_SESSION['message']))
                {
                    ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Hey!</strong> <?php echo $_SESSION['message']; ?>.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php
                    unset($_SESSION['message']);
                }
                ?>
                <div class="card">
                    <div class="card-header">
                        <h4>Registration Form</h4>
                    </div>
                    <div class="card-body">
                        <form action="functions/authcode.php" method="POST"">
                        <div class="mb-3">
                            <label for="inputName" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" id="inputName" placeholder="Enter your name" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label for="inputPhone" class="form-label">Phone</label>
                            <input type="number" name="phone" class="form-control" id="inputPhone" placeholder="Enter your phone number" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Enter your email address" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputPassword" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Enter your password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmedPassword" class="form-label">Confirm Password</label>
                            <input type="password" name="confirmed_password" class="form-control" id="confirmedPassword" placeholder="Confirm your password" required>
                        </div>
                        <button type="submit" name="registration_btn" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<?php include 'includes/footer.php'; ?>

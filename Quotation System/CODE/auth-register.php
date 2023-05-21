<?php
// Include config file
require_once "layouts/config.php";



// Define variables and initialize with empty values
$useremail = $username =  $password = $confirm_password = $useraddress = $userphone  = "";
$useremail_err = $username_err = $password_err = $confirm_password_err = $useraddress_err = $userphone_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate useremail
    if (empty(trim($_POST["useremail"]))) {
        $useremail_err = "Please enter a useremail.";
    } elseif (!filter_var($_POST["useremail"], FILTER_VALIDATE_EMAIL)) {
        $useremail_err = "Invalid email format";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE useremail = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_useremail);

            // Set parameters
            $param_useremail = trim($_POST["useremail"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $useremail_err = "This useremail is already taken.";
                } else {
                    $useremail = trim($_POST["useremail"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please enter a confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Validate address
    if (empty(trim($_POST["useraddress"]))) {
        $useraddress_err = "Please enter address.";
    } else {
        $useraddress = trim($_POST["useraddress"]);
    }

     // Validate phone number
    if (empty(trim($_POST["userphone"]))) {
        $userphone_err = "Please enter phone number.";
    } elseif (strlen(trim($_POST["userphone"])) < 9 ) {
        $userphone_err = "Phone number must have atleast 9 characters.";
    } elseif (strlen(trim($_POST["userphone"])) > 12 ) {
        $userphone_err = "Phone number must have less than 12 characters.";
    } else {
        $userphone = trim($_POST["userphone"]);
    }

    // Check input errors before inserting in database
    if (empty($useremail_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($useraddress_err) && empty ($userphone_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (useremail, username, password, useraddress, userphone, u_type, token) VALUES (?,?,?,?,?,2,?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_useremail, $param_username, $param_password, $param_useraddress, $param_userphone,  $param_token);

            // Set parameters
            $param_useremail = $useremail;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_useraddress = $useraddress;
            $param_userphone = $userphone;
            $param_token = bin2hex(random_bytes(50)); // generate unique token

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
            
                // Redirect to login page
                echo "<script>
alert('Register process successful!');
window.location.href='auth-login.php';
</script>";
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>
<?php include 'layouts/head-main.php'; ?>

<head>
    <title>Powerec | Register</title>
    <?php include 'layouts/head.php'; ?>
    <?php include 'layouts/head-style.php'; ?>
</head>

<body>

    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-primary p-4">
                                        <h5 class="text-primary"><?php echo $language["Free Register"]; ?></h5>
                                        <p><?php echo $language["Register to Powerec now."]; ?></p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div>
                                <a href="index.php">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <span class="avatar-title rounded-circle bg-light">
                                            <img src="assets/images/Powereclogo.jpeg" alt="" class="rounded-circle" height="70">
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="p-2">
                                <form class="needs-validation" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                                    <div class="mb-3 <?php echo (!empty($useremail_err)) ? 'has-error' : ''; ?>">
                                        <label for="useremail" class="form-label"><?php echo $language["Email"]; ?></label>
                                        <input type="email" class="form-control" id="useremail" name="useremail" placeholder="Enter email" value="<?php echo $useremail; ?>">
                                        <span class="text-danger"><?php echo $useremail_err; ?></span>
                                    </div>

                                    <div class="mb-3 <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                                        <label for="username" class="form-label"><?php echo $language["Fullname"]; ?></label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your full name" value="<?php echo $username; ?>">
                                        <span class="text-danger"><?php echo $username_err; ?></span>
                                    </div>

                                    <div class="mb-3 <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                                        <label for="userpassword" class="form-label"><?php echo $language["Password"]; ?></label>
                                        <input type="password" class="form-control" id="userpassword" name="password" placeholder="Enter password" value="<?php echo $password; ?>">
                                        <span class="text-danger"><?php echo $password_err; ?></span>
                                    </div>

                                    <div class="mb-3 <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                                        <label for="confirm_password" class="form-label"><?php echo $language["Confirm password"]; ?></label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Enter confirm password" value="<?php echo $confirm_password; ?>">
                                        <span class="text-danger"><?php echo $confirm_password_err; ?></span>
                                    </div>

                                    <div class="mb-3 <?php echo (!empty($useraddress_err)) ? 'has-error' : ''; ?>">
                                        <label for="useraddress" class="form-label"><?php echo $language["Address"]; ?></label>
                                        <textarea type="text" class="form-control" id="useraddress" name="useraddress" row='4' placeholder="Enter home or office address" value="<?php echo $useraddress; ?>"></textarea>
                                        <span class="text-danger"><?php echo $useraddress_err; ?></span>
                                    </div>

                                    <div class="mb-3 <?php echo (!empty($userphone_err)) ? 'has-error' : ''; ?>">
                                        <label for="userphone" class="form-label"><?php echo $language["Phone Number"]; ?></label>
                                        <input type="number" class="form-control" id="userphone" name="userphone" placeholder="Enter phone number" value="<?php echo $userphone; ?>">
                                        <span class="text-danger"><?php echo $userphone_err; ?></span>
                                    </div>

                                    <div class="mt-4 d-grid">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit"><?php echo $language["Register"]; ?></button>
                                    </div>


                                    <div class="mt-4 text-center">
                                        <p class="mb-0">By registering you agree to the Powerec's <a href="#" class="text-primary">Terms of Use</a></p>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="mt-5 text-center">

                        <div>
                            <p>Already have an account ?<a href="auth-login.php" class="fw-medium text-primary"><?php echo $language["Login"]; ?></a> </p>
                            <p>© <script>
                                    document.write(new Date().getFullYear())
                                </script> Powerec. Crafted with <i class="mdi mdi-heart text-danger"></i> by SamVerse</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>

    <!-- App js -->
    <script src="assets/js/app.js"></script>

</body>

</html>
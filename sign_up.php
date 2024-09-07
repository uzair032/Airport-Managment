<?php
include 'dbconnect.php' ;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $CNIC = $_POST['CNIC'];
        if (!preg_match('/^\d{5}-\d{7}-\d$/', $CNIC)) {
            $errorMessage = "Invalid CNIC format. Please use the format XXXXX-XXXXXXX-X.";
            echo '<div class="alert alert-danger text-center">' . $errorMessage . '</div>';
        }
        else{
                $email = $_POST['email'];
                $user_type = $_POST['user_type'];
                $user_name = $_POST['txtusername'];
                $user_password = $_POST['txtpassword'];
                $confirmpassword = $_POST["ConfirmPassword"];

                if ($user_password == $confirmpassword) {
                $query_check = "SELECT * FROM `user_t` WHERE `CNIC` = '$CNIC';";

                $check = mysqli_query($conn, $query_check);
                if (mysqli_num_rows($check) > 0) {
                    // echo "CNIC already exists. Please try again with a different CNIC.";
                    $errorMessage = "CNIC already exists. Please try again with a different CNIC.";
                    echo '<div class="alert alert-danger text-center">' . $errorMessage . '</div>';
                    // exit;
                } 
                else{
                    $query = "INSERT INTO `user_t` (`firstname`, `lastname`, `CNIC`, `email`, `user_type`, `Username`, `User_password`) VALUES ('$firstname', '$lastname', '$CNIC', '$email', '$user_type','$user_name', '$user_password')";
                    $check = mysqli_query($conn, $query);
                    if ($check) {
                        echo 'Data inserted';
                        header("Location: login.php");
                        mysqli_close($conn);
                        exit;
                    }
                    else{
                        echo "<p>Error generated. " . mysqli_error($conn) . "</p>";
                    }
                }
                } else {
                    $message = "Passwords do not match.";
                }
        }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style>
        body {
            background-color: #343a40; /* Dark background for contrast */
        }

        .container {
            max-width: 600px;
            background: rgba(248, 248, 255, 0.5450980392);
            border-radius: 10px;
            padding: 20px;
            margin-top: 50px;
        }

        .text-center h2 {
            color: white;
        }

        .text-danger {
            display: none; /* Initially hide error messages */
        }
    </style>
</head>
<body class="scrollbar-hide">
    <div class="container">
        <h2 class="text-center mt-2">New User Login</h2>
        <div id="lblmessage" class="text-danger"></div> <!-- Message for feedback -->

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtFirstName" class="form-label">First Name:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" id="txtFirstName" class="form-control" placeholder="First name" name="firstname" required>
                        </div>
                        <div class="text-danger" id="rfvtxtFirstName">First name required</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtLastName" class="form-label">Last Name:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <input type="text" id="txtLastName" class="form-control" placeholder="Last name" name="lastname" required>
                        </div>
                        <div class="text-danger" id="rfctxtLastName">Last name required</div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtcnic" class="form-label">CNIC:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-id-card"></i></span>
                            <input type="text" id="txtcnic" class="form-control" placeholder="XXXX-XXXXXXX-X" name="CNIC" required>
                        </div>
                        <div class="text-danger" id="rfctxtcnic">CNIC is required</div>
                        <div class="text-danger" id="revtxtcnic">Invalid CNIC format</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtEmail" class="form-label">Email:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="far fa-envelope"></i></span>
                            <input type="email" id="txtEmail" class="form-control" placeholder="user@example.com" name="email" required>
                        </div>
                        <div class="text-danger" id="rfctxtEmail">Email address required</div>
                        <div class="text-danger" id="revtxtEmail">Invalid Email Address</div>
                    </div>
                </div>
            </div>
            

            <div class="row mt-2">
                <div class="form-group">
                    <label for="user_type" class="form-label">Sign up as: </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <select id="user_type" class="form-select" name="user_type">
                            <option value="passenger">Passenger</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="text-danger" id="rfctxtUsername">Username required</div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="form-group">
                    <label for="txtUsername" class="form-label">Username:</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" id="txtUsername" class="form-control" placeholder="Username" name="txtusername" required>
                    </div>
                    <div class="text-danger" id="rfctxtUsername">Username required</div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtPassword" class="form-label">Password:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="txtPassword" class="form-control" placeholder="Password" name="txtpassword" required>
                        </div>
                        <div class="text-danger" id="rfctxtPassword">Password required</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="txtConfirmPassword" class="form-label">Confirm Password:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" id="txtConfirmPassword" class="form-control" placeholder="Confirm Password" name="ConfirmPassword" required>
                        </div>
                    </div>
                </div>
            </div>

            <div id="errorMessage" class="text-danger text-center"></div>

            <div class="row mt-4 mb-2">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-success" style="width: 150px;">Sign Up</button>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12 text-center">
                    <span>Already Have an account?</span>
                    <a href="login.php" class="text-primary">Sign In</a>
                </div>
            </div>
            
        </form> 
            
    
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YTXcFn3E4QVRlNPl2gG0WOV5OB5UtSzG1hRZx62GRmndYfZCq4hwn6AfsBd0gl2k" crossorigin="anonymous"></script>
    <script>
        // Add client-side validation if needed
        document.getElementById('form1').onsubmit = function() {
            var valid = true;
            // Example validation logic
            // Show error messages if fields are empty
            if (!document.getElementById('txtFirstName').value) {
                document.getElementById('rfvtxtFirstName').style.display = 'block';
                valid = false;
            }
            if (!document.getElementById('txtLastName').value) {
                document.getElementById('rfctxtLastName').style.display = 'block';
                valid = false;
            }
            if (!document.getElementById('txtUsername').value) {
                document.getElementById('rfctxtUsername').style.display = 'block';
                valid = false;
            }
            if (!document.getElementById('txtPassword').value) {
                document.getElementById('rfctxtPassword').style.display = 'block';
                valid = false;
            }
            // Add more validation checks as needed

            return valid; // Prevent form submission if invalid
        };
        
    </script>
</body>
</html>
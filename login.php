<?php 
session_start();

include 'dbconnect.php' ;
$errorMessage = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_name = trim($_POST['txtusername']);
    $user_password = trim($_POST['txtpassword']);
    
// echo "<pre>";
// print_r($_POST); // This will show you all the submitted data
// echo "</pre>";

    // Validate username and password
    if (empty($user_name)) {
        $errorMessage = "Username required.";
    } elseif (empty($user_password)) {
        $errorMessage = "Password required.";
    } else {
        $sql = "SELECT user_id, User_password, user_type FROM user_t WHERE Username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $user_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $dbPassword = $row["User_password"];
            $dbuser = $row["user_id"];
            $userType = $row["user_type"];

            // Use password verification if passwords are hashed
            if ($user_password == $dbPassword) {
                // Clear the session
                session_unset();
                session_destroy();
                session_start();

                // Set the new session variable
                $_SESSION["user_id"] = $dbuser;
                $_SESSION['Auth'] = true;

                // Redirect based on user type
                if ($userType == "passenger") {
                    header("Location: airport.php");
                } elseif($userType == "admin") {
                    $_SESSION["is_admin"] = true;
                    header("Location: options.php");

                }
                exit; // Make sure to exit after header
            } else {
                $errorMessage = "Not verified.";
            }
        } else {
            $errorMessage = "Invalid username or password.";
        }
    }
}

if (isset($_POST["btnSign_up"])) {
    header("Location: sign_up.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            height: 100vh;
            background-image: url('./resources/signIn.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        #btnSign_up {
            text-decoration: none;
            color: #4361ee;
        }

        .form-control:focus {
            border-width: 1px; 
            box-shadow: none; 
        }

        a {
            text-decoration: none;
            color: white;
        }

        a:hover {
            color: cornflowerblue;
        }

        h2 {
            color: whitesmoke;
            margin-bottom: 20px; 
        }
        .logout-button-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    
    <div class="logout-button-container">
        <a href="Home.php" class="btn btn-danger">Back</a>
    </div>
    <form id="form1" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <h2 >Fly High with Our Premier Airline Reservation Hub!</h2>
        <div class="container p-3" style="max-width: 400px; background: rgba(248, 248, 255, 0.5450980392) !important; border-radius: 10px">
            
            <div class="row mt-2">
                <div class="form-group">
                    <label for="txtusername" class="form-label col-md-4">Username: </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" id="txtusername" name="txtusername" class="form-control" placeholder="username" required>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="form-group">
                    <label for="txtpassword" class="form-label col-md-4">Password: </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="txtpassword" name="txtpassword" class="form-control" placeholder="password" required>
                    </div>
                </div>
            </div>

            <div class="row mt-4 mb-2">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-success" style="width: 150px;">Sign In</button>
                </div>
            </div>

            <div class="row mt-4 mb-2">
                <div class="col-md-12 text-center">
                    <span>Don't Have an account?</span>
                    <a href="sign_up.php" class="text-primary">Sign Up</a>
                </div>
            </div>
        </div>
    </form>
</body>
</html>
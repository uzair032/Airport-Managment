<?php 
session_start(); // Start the session

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != "True") {
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger text-center" role="alert">';
    echo '<h1>You must be logged in as an admin to access this page.</h1>';
    echo '<a href="login.php" class="alert-link">Click here to sign in.</a>';
    echo '</div>';
    echo '</div>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Options</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .logout-button-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body class="bg-dark text-white">

    <div class="logout-button-container">
        <a href="logout.php" class="btn btn-danger">Log out</a>
    </div>
    <div class="container my-5 text-center">
        <h1>AirGate Solutions</h1>
        <p>Please select an option from the menu below to navigate to different pages.</p>
    </div>

    <div class="container my-5">
        <ul class="list-group text-center">
            <li class="list-group-item bg-dark text-white">
                <a class="text-white" href="update_airline.php">Change Airline Data</a>
            </li>
            <li class="list-group-item bg-dark text-white">
                <a class="text-white" href="update_city.php">Change City Data</a>
            </li>
            <li class="list-group-item bg-dark text-white">
                <a class="text-white" href="update_seat.php">Change Seat Data</a>
            </li>
            <li class="list-group-item bg-dark text-white">
                <a class="text-white" href="update_payment.php">Change Payment Data</a>
            </li>
            <li class="list-group-item bg-dark text-white">
                <a class="text-white" href="Check_confirm_Flights.php">Check Confirm flight</a>
            </li>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
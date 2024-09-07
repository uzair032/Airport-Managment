<?php
session_start();
include 'dbconnect.php'; // Include your database connection file

// Check if user is authenticated
// if (!isset($_SESSION['Auth']) || $_SESSION['Auth'] == false) {
//     header("Location: login.php");
//     exit();
// }

// Redirect if payment details are not entered
if (!isset($_SESSION['PaymentDetailsEntered']) || $_SESSION['PaymentDetailsEntered'] !== true) {
    header("Location: payment.php");
    exit();
}

// Set session variable for the checkout page
$_SESSION['AtCheckoutPage'] = true;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        loadTicketDetails($conn);
    
}

// Function to load ticket details
function loadTicketDetails($conn) {
    // Retrieve the Ticket_ID from the session
    $userid = $_SESSION['user_id'];
    $Ticket_ID = $_SESSION['Ticket_ID'];
    $payment_ID = $_SESSION['payment_ID'];
    $flight_ID = $_SESSION['flight_ID'];
    
    $query = "SELECT * FROM CHECKOUT WHERE user_id = ? AND flight_ID = ? AND Payment_ID = ? AND Ticket_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $userid, $flight_ID, $payment_ID, $Ticket_ID);
    $stmt->execute();
    $result = $stmt->get_result();
 
    if ($result->num_rows > 0) {
        // Display the results in a vertical format
        while ($row = $result->fetch_assoc()) {
            echo '<div class="row justify-content-center">';
            echo '<div class="col-md-8">';
            echo '<div class="card mb-3">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title text-center">Ticket Details</h5>';
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<p><strong>User ID:</strong> ' . htmlspecialchars($row['user_id']) . '</p>';
            echo '<p><strong>Full Name:</strong> ' . htmlspecialchars($row['Firstname']) . " " . htmlspecialchars($row['Lastname']) . '</p>';
            echo '<p><strong>CNIC:</strong> ' . htmlspecialchars($row['CNIC']) . '</p>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<p><strong>Airport Name:</strong> ' . htmlspecialchars($row['Airport_name']) . '</p>';
            echo '<p><strong>Destination:</strong> ' . htmlspecialchars($row['Dest']) . '</p>';
            echo '<p><strong>Airline:</strong> ' . htmlspecialchars($row['Airline']) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<p><strong>Flight No:</strong> ' . htmlspecialchars($row['Flight_ID']) . '</p>';
            echo '<p><strong>Departure Date:</strong> ' . htmlspecialchars($row['deperaturedate']) . '</p>';
            echo '<p><strong>Arrival Date:</strong> ' . htmlspecialchars($row['arrivaldate']) . '</p>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<p><strong>Payment ID:</strong> ' . htmlspecialchars($row['Payment_ID']) . '</p>';
            echo '<p><strong>Payment Method:</strong> ' . htmlspecialchars($row['Paymentmethod']) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<p><strong>Ticket Number:</strong> ' . htmlspecialchars($row['Ticket_ID']) . '</p>';
            echo '</div>';
            echo '<div class="col-md-6">';
            echo '<p><strong>Total Price:</strong> ' . htmlspecialchars($row['Total_price']) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo "No ticket details found.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style>
        body {
            height: 100vh;
            background-image: url('./resources/checkout.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .logout-button-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="logout-button-container">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
        <h2 class="text-center form-header mb-3" style="color: white">Ticket Details</h2>
        

        <div class="text-center mb-3">
            <button type="submit" id="btnCheck" class="btn btn-primary">Display bill</button>
        </div>

        <div class="text-center">
            <label id="lblmessage" class="text-success"></label>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YTXcFn3E4QVRlNPl2gG0WOV5OB5UtSzG1hRZx62GRmndYfZCq4hwn6AfsBd0gl2k" crossorigin="anonymous"></script>
</body>
</html>
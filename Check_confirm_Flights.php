<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style>
        body {
            /* height: 100vh; */
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
    
        <div class="logout-button-container">
            <a href="options.php" class="btn btn-danger">Back</a>
        </div>
        <?php
        session_start();
        include 'dbconnect.php'; // Include your database connection file
        $query = "SELECT * FROM CHECKOUT";
        $stmt = $conn->prepare($query);
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
        }
        ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YTXcFn3E4QVRlNPl2gG0WOV5OB5UtSzG1hRZx62GRmndYfZCq4hwn6AfsBd0gl2k" crossorigin="anonymous"></script>
</body>
</html>
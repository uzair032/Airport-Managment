<?php
include 'dbconnect.php' ;
session_start();

// Redirect if not authenticated
if (!isset($_SESSION['Auth']) || $_SESSION['Auth'] != "True") {
    header("Location: login.php");
    exit();
}

// Redirect if airport details are not entered
if (!isset($_SESSION['AirportDetailsEntered']) || $_SESSION['AirportDetailsEntered'] !== true) {
    header("Location: airport.php");
    exit();
}

$_SESSION['AtFlightsPage'] = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Storing Departure date in array
    if (isset($_POST['txtDepartureDate'])) {
        // Retrieve the date from the form
        $departureDate = $_POST['txtDepartureDate'];

        // Create a DateTime object from the input date
        $datedeparture = DateTime::createFromFormat('Y-m-d', $departureDate);

        // Check if the date was created successfully
        if ($datedeparture) {
            // Store the date in an array
            $dates_departure_Array = [];
            $dates_departure_Array[] = $datedeparture;

            // Output the stored date
            echo "Stored Departure Date: " . $dates_departure_Array[0]->format('Y-m-d') . "<br>";
            
            // Format for SQL
            $deperaturedate = $dates_departure_Array[0]->format('Y-m-d');
        } else {
            echo "Invalid date format.";
        }
    } else {
        echo "Departure date is not set.";
    }

    // Storing Arrival date in array
    if (isset($_POST['txtArrivalDate'])) {
        // Retrieve the date from the form
        $arrivalDate = $_POST['txtArrivalDate'];

        // Create a DateTime object from the input date
        $datearrival = DateTime::createFromFormat('Y-m-d', $arrivalDate);

        // Check if the date was created successfully
        if ($datearrival) {
            // Store the date in an array
            $dates_arrival_Array = [];
            $dates_arrival_Array[] = $datearrival;

            // Output the stored date
            echo "Stored Arrival Date: " . $dates_arrival_Array[0]->format('Y-m-d') . "<br>";
            
            // Format for SQL
            $arrivaldate = $dates_arrival_Array[0]->format('Y-m-d');
        } else {
            echo "Invalid date format.";
        }
    } else {
        echo "Arrival date is not set.";
    }

// Debugging: Show submitted data
// echo "<pre>";
// print_r($_POST); // This will show you all the submitted data
// echo "</pre>";

    $seat = isset($_POST['chkClass']) ? $_POST['chkClass'] : '';

    // Generate a unique Flight_ID
    $flight_ID = rand(1, 100);
    $insertedAt = gmdate('Y-m-d H:i:s');
    // $dbuser = $_SESSION['user_id'];
    $airport_ID = $_SESSION['airport_ID'];

    // Insert flight data
    // Check if Flight ID already exists
    $checkQuery = "SELECT COUNT(*) FROM FLIGHT_t WHERE Flight_ID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $flight_ID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return "Flight ID already exists. Please use a unique ID.";
    }

    // $query = "INSERT INTO FLIGHT_t (Flight_ID, Airport_ID, CNIC, deperaturedate, arrivaldate, seat, InsertedAt) 
    //         VALUES ('$flight_ID', '$airport_ID', '$dbcnic', '$deperaturedate', '$arrivaldate', '$seat', '$insertedAt')";

    $query = "INSERT INTO FLIGHT_t (Flight_ID, Airport_ID, deperaturedate, arrivaldate, seat, InsertedAt) 
    VALUES ('$flight_ID', '$airport_ID', '$deperaturedate', '$arrivaldate', '$seat', '$insertedAt')";

    if (mysqli_query($conn, $query)) {
        $total_bill = add_to_bill($seat,$conn);
        $_SESSION['FlightDetailsEntered'] = true;
        $_SESSION['flight_ID'] = $flight_ID;
        // print_r($_SESSION["total_bill"]);
        // Redirect to Payment page
        // header("Location: Payment.php");
        header("Location: Paymentstripe.php");
        exit();
    } else {
        $_SESSION["message"] = "An error occurred while adding the passenger: " . mysqli_error($conn);
    }

}

function add_to_bill($seat,$conn) {
    $total_bill = 0;
    // Query to get seat price
    $seat_sql = "SELECT Seat_Price FROM Seat_data WHERE Seat_category = '$seat'";
    $seat_check = mysqli_query($conn, $seat_sql);

    // Check if the query was successful
    if ($seat_check === false) {
        // Log error or handle it
        error_log("Database query failed: " . mysqli_error($conn));
        return 0; // Return 0 if there's an error
    }

    // Process seat prices
    if (mysqli_num_rows($seat_check) > 0) {
        while ($row = mysqli_fetch_assoc($seat_check)) {
            $seat_price = $row["Seat_Price"];
            $total_bill += $seat_price;
        }
    }

    $_SESSION["total_bill"] += $total_bill; // Store the total bill in session
    // echo $_SESSION["total_bill"];
    return $total_bill; // Return 0 if no data found
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style>
        body {
            background-image: url('./resources/banner2.png');
            background-repeat: no-repeat; 
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        form {
            background: rgba(248, 248, 255, 0.5450980392);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            margin-top: 70px; 
            margin-bottom: 30px;
        }
        .container {
            width: 500px;
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
        <div class="container">
            <div class="logout-button-container">
                <!-- Logout control can be implemented with a simple button or link -->
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
            <h1 class="text-center mb-4">Flight Details</h1>
            <div class="form-group mb-3">
                <label for="txtDepartureDate" class="form-label">Departure Date:</label>
                <input type="date" id="txtDepartureDate" class="form-control" name="txtDepartureDate" required>
                <div class="text-danger" id="departureDateError" style="display:none;">Please enter a Departure date.</div>
            </div>

            <div class="form-group mb-3">
                <label for="txtArrivalDate" class="form-label">Arrival Date:</label>
                <input type="date" id="txtArrivalDate" class="form-control" name="txtArrivalDate" required>
                <div class="text-danger" id="arrivalDateError" style="display:none;">Please enter an Arrival date.</div>
            </div>

            <div class="form-group mb-3">
                <label for="chkClass" class="form-label">Class:</label>
                <select id="chkClass" class="form-control" name="chkClass">
                    <option value="Economy">Economy</option>
                    <option value="Premium_Economy">Premium Economy Class</option>
                    <option value="Business">Business</option>
                    <option value="First_Class">First Class</option>
                </select>
            </div>

            <div class="form-group text-center mt-3">
                <button type="submit" class="btn btn-primary">Proceed</button>
            </div>
            
            <div class="form-group text-center">
                <label id="lblMessage" class="text-success"></label>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('flightForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission for validation
            
            // Clear previous error messages
            document.getElementById('departureDateError').style.display = 'none';
            document.getElementById('arrivalDateError').style.display = 'none';
            document.getElementById('lblMessage').innerText = '';

            // Validate input
            let departureDate = document.getElementById('txtDepartureDate').value;
            let arrivalDate = document.getElementById('txtArrivalDate').value;

            if (!departureDate) {
                document.getElementById('departureDateError').style.display = 'block';
                return;
            }

            if (!arrivalDate) {
                document.getElementById('arrivalDateError').style.display = 'block';
                return;
            }

            // Proceed with form submission logic (e.g., AJAX call or redirect)
            document.getElementById('lblMessage').innerText = 'Form submitted successfully!'; // Example message
        });

        function logout() {
            // Implement logout functionality here
            alert('Logout functionality not implemented.');
        }
    </script>
</body>
</html>
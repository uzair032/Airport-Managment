<?php
include 'dbconnect.php' ;
session_start();
// echo "<pre>";
// print_r($_SESSION);exit;
// Redirect if not authenticated
if (!isset($_SESSION['Auth']) || $_SESSION['Auth'] != "True") {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $airport_ID = rand(1, 100);
    $airport = $_POST["Airport_name"];
    $dest = $_POST["Destination"];
    $airline = $_POST["Airline_name"];
    $dbuser = $_SESSION["user_id"]; 
    $insertedAt = date("Y-m-d H:i:s");

    // $query = "INSERT INTO AIRPORT_t (Airport_ID, CNIC, Airport_name, Dest, Airline, InsertedAt) VALUES ('$airport_ID', '$dbcnic', '$airport', '$dest', '$airline', '$insertedAt')";
    $query = "INSERT INTO AIRPORT_t (Airport_ID, user_id, Airport_name, Dest, Airline, InsertedAt) VALUES ('$airport_ID', '$dbuser', '$airport', '$dest', '$airline', '$insertedAt')";
      
    if (mysqli_query($conn, $query)) {
            $total_bill = add_to_bill($dest, $airline, $conn);
            // print_r($total_bill);
            $_SESSION["AirportDetailsEntered"] = true;
            $_SESSION["airport_ID"] = $airport_ID;
            
            header("Location: Flight.php");
            exit;
    } else {
        $_SESSION["message"] = "An error occurred while adding the passenger.";
    }
}

function add_to_bill($dest, $airline, $conn) {
    $total_bill = 0;

    // Query to get destination price
    $city_sql = "SELECT Dest_Price FROM city_data WHERE Destination = '$dest'";
    $city_check = mysqli_query($conn, $city_sql);

    // Check if the query was successful
    if ($city_check === false) {
        // Log error or handle it
        error_log("Database query failed: " . mysqli_error($conn));
        return 0; // Return 0 if there's an error
    }

    // Process destination prices
    if (mysqli_num_rows($city_check) > 0) {
        while ($row = mysqli_fetch_assoc($city_check)) {
            $dest_price = $row["Dest_Price"];
            $total_bill += $dest_price;
        }
    }

    // Query to get airline price
    $airline_sql = "SELECT Air_Price FROM airline_data WHERE Airline_name = '$airline'";
    $airline_check = mysqli_query($conn, $airline_sql);

    // Check if the query was successful
    if ($airline_check === false) {
        // Log error or handle it
        error_log("Database query failed: " . mysqli_error($conn));
        return 0; // Return 0 if there's an error
    }

    // Process airline prices
    if (mysqli_num_rows($airline_check) > 0) {
        while ($row = mysqli_fetch_assoc($airline_check)) {
            $airline_price = $row["Air_Price"];
            $total_bill += $airline_price;
        }
    }

    $_SESSION["total_bill"] = $total_bill; // Store the total bill in session

    return $total_bill; // Return 0 if no data found
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Airport Details</title>
    <style>
        .logout-button-container {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
        
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <div class="grid-container">
        <div class="logout-button-container">
            <a href="logout.php" class="btn btn-danger">Logout</a>
           
        </div>
        <div class="left-container">
            <h1 style="color:#4D73FC">Welcome</h1>
            <h3 style="color:black">Please select the required fields</h3>
        </div>

        <div class="right-container">
            <div class="form-container">
                <!-- <img src="./resources/airport.png" class="img-fluid" alt="Responsive image" /> -->
                <div class="form-content">
                    <div class="mb-3">
                        <label for="Airport_name" class="form-label">Airports Available: </label>
                        <select id="Airport_name" class="form-select" name="Airport_name">
                            <option value="JIA">Jinnah International Airport</option>
                            <option value="MIA">Multan International Airport</option>
                            <option value="LIA">Lahore International Airport</option>
                            <option value="IIA">Islamabad International Airport</option>
                            <option value="GIA">Gwadar International Airport</option>
                            <option value="QIA">Quetta International Airport</option>
                        </select>
                    </div>
                    
                    <?php 
                        include 'dbconnect.php'; // Include your database connection file
                        $query = "SELECT * FROM city_data";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                        echo '<div class="mb-3">' ;
                        echo '<label for="Destination" class="form-label">Destination: </label>' ;
                        echo '<select id="Destination" class="form-select" name="Destination">' ;
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['Destination']) . '">' . htmlspecialchars($row['Destination']) . '</option>' ;
                        }
                        echo  '</select>' ;
                        echo  '</div>' ;
                        } else {
                            echo '<div class="container mt-5"><p class="text-center">No Data found.</p></div>';
                        }
                        $stmt->close();
                    ?>
                    <?php 
                    include 'dbconnect.php'; // Include your database connection file
                    $query = "SELECT * FROM airline_data";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows > 0) {
                        // Display the results in a table format
                        echo '<div class="mb-3">' ;
                        echo '<label for="Airline_name" class="form-label">Airline Available: </label>' ;
                        echo '<select id="Airline_name" class="form-select" name="Airline_name">' ;
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['Airline_name']) . '">' . htmlspecialchars($row['Airline_name']) . '</option>' ;
                        }
                        echo  '</select>' ;
                        echo  '</div>' ;
                    } else {
                        echo '<div class="container mt-5"><p class="text-center">No Data found.</p></div>';
                    }
                    $stmt->close();
                    ?>
                    
                    <div class="mb-3 text-center">
                        <button id="btnProceed" class="btn btn-primary">Proceed To Airline Selection</button>
                    </div>
                    <div class="text-center">
                        <p id="lblmessage" class="text-danger"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
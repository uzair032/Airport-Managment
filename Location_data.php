<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Details</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background-image: url('./resources/checkout.jpg');
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        .main_body{
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .navbar {
            background-color: #007bff; /* Navbar background color */
        }
        
        .table {
            background-color: #fff; /* White table background */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        
        .table thead {
            background-color: #007bff; /* Header background color */
            color: #fff; /* Header text color */
        }
        
        .table th, .table td {
            padding: 12px 15px; /* Padding for table cells */
        }
        
        .table-hover tbody tr:hover {
            background-color: #f5f5f5; /* Hover effect */
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="./Home.php">AirGate Solutions</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./login.php">Book a Flight</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./Location_data.php">Destination</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./Airline_data.php">Airlines Available</a>
                </li>
            </ul>
        </div>
    </div>
    </nav>
<br><br>
<div class="main_body">
    <?php 
    include 'dbconnect.php'; // Include your database connection file

    $query = "SELECT * FROM city_data";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Display the results in a table format
        echo '<h1 class="text-center mb-4" style="color: white;">City Data</h1>'; // Table title
        echo '<div class="container mt-5">';
        echo '<div class="row justify-content-center">';
        echo '<div class="col-md-8">';
        echo '<table class="table table-bordered table-striped table-hover text-center">'; // Center text in the table
        echo '<thead class="thead-dark">';
        echo '<tr>';
        echo '<th>City ID</th>';
        echo '<th>Destination</th>';
        echo '<th>Charges</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['City_id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Destination']) . '</td>';
            echo '<td>' . "Rs " . htmlspecialchars($row['Dest_Price']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // End of col-md-8
        echo '</div>'; // End of row
        echo '</div>'; // End of container
    } else {
        echo '<div class="container mt-5"><p class="text-center">No Data found.</p></div>';
    }

    $stmt->close();
    ?>
</div>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
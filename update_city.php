<?php 
session_start(); // Start the session

include 'dbconnect.php'; // Include your database connection file


// Check if the user is an admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != "True") {
    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger text-center" role="alert">';
    echo '<h1>You must be logged in as an admin to access this page. </h1>';
    echo '<a href="login.php" class="alert-link">Click here to sign in.</a>';
    echo '</div>';
    echo '</div>';
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        // Check for duplicate destination
        if ($action == 'insert') {
            $Destination = $_POST['Destination'];
            $Dest_price = $_POST['Dest_price'];

            // Check if destination already exists
            $check_query = "SELECT * FROM city_data WHERE Destination = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $Destination);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<div class="alert alert-danger text-center">Destination already exists. Please choose a different destination.</div>';
            } else {
                // Insert new destination data
                $insert_query = "INSERT INTO city_data (Destination, Dest_Price) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("sd", $Destination, $Dest_price);
                $stmt->execute();
                $stmt->close();
                echo '<div class="alert alert-success text-center">Destination inserted successfully.</div>';
            }
            
            // Clear form fields
            $Destination = "";
            $Dest_price = "";
        } elseif ($action == 'update' || $action == 'delete') {
            // For update and delete, we need the City_id
            $City_id = $_POST['City_id'];

            // Check if City_id exists
            $check_id_query = "SELECT * FROM city_data WHERE City_id = ?";
            $stmt = $conn->prepare($check_id_query);
            $stmt->bind_param("i", $City_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                echo '<div class="alert alert-danger text-center">City ID does not exist.</div>';
            } else {
                if ($action == 'update') {
                    $Destination = $_POST['Destination'];
                    $Dest_price = $_POST['Dest_price'];

                    // Check for duplicate destination during update
                    $check_update_query = "SELECT * FROM city_data WHERE Destination = ? AND City_id != ?";
                    $stmt = $conn->prepare($check_update_query);
                    $stmt->bind_param("si", $Destination, $City_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo '<div class="alert alert-danger text-center">Destination already exists. Please choose a different destination.</div>';
                    } else {
                        // Update destination data
                        $update_query = "UPDATE city_data SET Destination = ?, Dest_Price = ? WHERE City_id = ?";
                        $stmt = $conn->prepare($update_query);
                        $stmt->bind_param("sdi", $Destination, $Dest_price, $City_id);
                        $stmt->execute();
                        $stmt->close();
                        echo '<div class="alert alert-success text-center">Destination updated successfully.</div>';
                    }

                    // Clear form fields
                    $Destination = "";
                    $Dest_price = "";
                } elseif ($action == 'delete') {
                    // Delete destination data
                    $delete_query = "DELETE FROM city_data WHERE City_id = ?";
                    $stmt = $conn->prepare($delete_query);
                    $stmt->bind_param("i", $City_id);
                    $stmt->execute();
                    $stmt->close();
                    echo '<div class="alert alert-success text-center">Destination deleted successfully.</div>';
                }
            }
        }
    }
}

// Fetch destination data
$query = "SELECT * FROM city_data";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Display the results in a table format
    echo '<h1 class="text-center mb-4" style="color: white;">Destinations Available</h1>'; // Table title
    echo '<div class="container mt-5">';
    echo '<div class="row justify-content-center">';
    echo '<div class="col-md-8">';
    echo '<table class="table table-bordered table-striped table-hover text-center">'; // Center text in the table
    echo '<thead class="thead-dark">';
    echo '<tr>';
    echo '<th>City ID</th>';
    echo '<th>Destination</th>';
    echo '<th>Destination Price</th>';
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations</title>
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
        <a href="logout.php" class="btn btn-danger">Back</a>
    </div>
    <div class="container my-5">
        <h2 class="text-center">What do you want to do?</h2>
        <form method="POST" class="text-center">
            <div class="mb-3">
                <select name="action" class="form-select" required>
                    <option value="" disabled selected>Select an action</option>
                    <option value="insert">Insert New Destination</option>
                    <option value="update">Update Existing Destination</option>
                    <option value="delete">Delete Destination</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="City_id" class="form-label">City ID (for update/delete):</label>
                <input type="number" name="City_id" class="form-control" value="<?php echo isset($City_id) ? $City_id : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="Destination" class="form-label">Destination:</label>
                <input type="text" name="Destination" class="form-control" value="<?php echo isset($Destination) ? $Destination : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="Dest_price" class="form-label">Destination Price:</label>
                <input type="number" name="Dest_price" class="form-control" step="0.01" value="<?php echo isset($Dest_price) ? $Dest_price : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
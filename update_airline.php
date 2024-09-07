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

        // Check for duplicate airline name
        if ($action == 'insert') {
            $airline_name = $_POST['airline_name'];
            $air_price = $_POST['air_price'];

            // Check if airline name already exists
            $check_query = "SELECT * FROM airline_data WHERE Airline_name = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $airline_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<div class="alert alert-danger text-center">Airline name already exists. Please choose a different name.</div>';
            } else {
                // Insert new airline data
                $insert_query = "INSERT INTO airline_data (Airline_name, Air_Price) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("sd", $airline_name, $air_price);
                $stmt->execute();
                $stmt->close();
                echo '<div class="alert alert-success text-center">Airline inserted successfully.</div>';
            }
            
            // Clear form fields
            $airline_name = "";
            $air_price = "";
        } elseif ($action == 'update' || $action == 'delete') {
            // For update and delete, we need the airline_id
            $airline_id = $_POST['airline_id'];

            // Check if airline_id exists
            $check_id_query = "SELECT * FROM airline_data WHERE Airline_id = ?";
            $stmt = $conn->prepare($check_id_query);
            $stmt->bind_param("i", $airline_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                echo '<div class="alert alert-danger text-center">Airline ID does not exist.</div>';
            } else {
                if ($action == 'update') {
                    $airline_name = $_POST['airline_name'];
                    $air_price = $_POST['air_price'];

                    // Check for duplicate airline name during update
                    $check_update_query = "SELECT * FROM airline_data WHERE Airline_name = ? AND Airline_id != ?";
                    $stmt = $conn->prepare($check_update_query);
                    $stmt->bind_param("si", $airline_name, $airline_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo '<div class="alert alert-danger text-center">Airline name already exists. Please choose a different name.</div>';
                    } else {
                        // Update airline data
                        $update_query = "UPDATE airline_data SET Airline_name = ?, Air_Price = ? WHERE Airline_id = ?";
                        $stmt = $conn->prepare($update_query);
                        $stmt->bind_param("sdi", $airline_name, $air_price, $airline_id);
                        $stmt->execute();
                        $stmt->close();
                        echo '<div class="alert alert-success text-center">Airline updated successfully.</div>';
                    }

                    // Clear form fields
                    $airline_name = "";
                    $air_price = "";
                } elseif ($action == 'delete') {
                    // Delete airline data
                    $delete_query = "DELETE FROM airline_data WHERE Airline_id = ?";
                    $stmt = $conn->prepare($delete_query);
                    $stmt->bind_param("i", $airline_id);
                    $stmt->execute();
                    $stmt->close();
                    echo '<div class="alert alert-success text-center">Airline deleted successfully.</div>';
                }
            }
        }
    }
}


// Fetch airline data
$query = "SELECT * FROM airline_data";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Display the results in a table format
    echo '<h1 class="text-center mb-4" style="color: white;">Airlines Available</h1>'; // Table title
    echo '<div class="container mt-5">';
    echo '<div class="row justify-content-center">';
    echo '<div class="col-md-8">';
    echo '<table class="table table-bordered table-striped table-hover text-center">'; // Center text in the table
    echo '<thead class="thead-dark">';
    echo '<tr>';
    echo '<th>Airline ID</th>';
    echo '<th>Airline Name</th>';
    echo '<th>Airline Price</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['Airline_id']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Airline_name']) . '</td>';
        echo '<td>' . "Rs " . htmlspecialchars($row['Air_Price']) . '</td>';
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
    <title>Manage Airlines</title>
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
                    <option value="insert">Insert New Airline</option>
                    <option value="update">Update Existing Airline</option>
                    <option value="delete">Delete Airline</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="airline_id" class="form-label">Airline ID (for update/delete):</label>
                <input type="number" name="airline_id" class="form-control" value="<?php echo isset($airline_id) ? $airline_id : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="airline_name" class="form-label">Airline Name:</label>
                <input type="text" name="airline_name" class="form-control" value="<?php echo isset($airline_name) ? $airline_name : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="air_price" class="form-label">Airline Charges:</label>
                <input type="number" name="air_price" class="form-control" step="0.01" value="<?php echo isset($air_price) ? $air_price : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
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

        // Check for duplicate seat category
        if ($action == 'insert') {
            $Seat_category = $_POST['Seat_category'];
            $Seat_price = $_POST['Seat_price'];

            // Check if seat category already exists
            $check_query = "SELECT * FROM seat_data WHERE Seat_category = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $Seat_category);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<div class="alert alert-danger text-center">Seat category already exists. Please choose a different category.</div>';
            } else {
                // Insert new seat data
                $insert_query = "INSERT INTO seat_data (Seat_category, Seat_Price) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("sd", $Seat_category, $Seat_price);
                $stmt->execute();
                $stmt->close();
                echo '<div class="alert alert-success text-center">Seat category inserted successfully.</div>';
            }
            
            // Clear form fields
            $Seat_category = "";
            $Seat_price = "";
        } elseif ($action == 'update' || $action == 'delete') {
            // For update and delete, we need the Seat_ID
            $Seat_ID = $_POST['Seat_ID'];

            // Check if Seat_ID exists
            $check_id_query = "SELECT * FROM seat_data WHERE Seat_ID = ?";
            $stmt = $conn->prepare($check_id_query);
            $stmt->bind_param("i", $Seat_ID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                echo '<div class="alert alert-danger text-center">Seat ID does not exist.</div>';
            } else {
                if ($action == 'update') {
                    $Seat_category = $_POST['Seat_category'];
                    $Seat_price = $_POST['Seat_price'];

                    // Check for duplicate seat category during update
                    $check_update_query = "SELECT * FROM seat_data WHERE Seat_category = ? AND Seat_ID != ?";
                    $stmt = $conn->prepare($check_update_query);
                    $stmt->bind_param("si", $Seat_category, $Seat_ID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo '<div class="alert alert-danger text-center">Seat category already exists. Please choose a different category.</div>';
                    } else {
                        // Update seat data
                        $update_query = "UPDATE seat_data SET Seat_category = ?, Seat_Price = ? WHERE Seat_ID = ?";
                        $stmt = $conn->prepare($update_query);
                        $stmt->bind_param("sdi", $Seat_category, $Seat_price, $Seat_ID);
                        $stmt->execute();
                        $stmt->close();
                        echo '<div class="alert alert-success text-center">Seat category updated successfully.</div>';
                    }

                    // Clear form fields
                    $Seat_category = "";
                    $Seat_price = "";
                } elseif ($action == 'delete') {
                    // Delete seat data
                    $delete_query = "DELETE FROM seat_data WHERE Seat_ID = ?";
                    $stmt = $conn->prepare($delete_query);
                    $stmt->bind_param("i", $Seat_ID);
                    $stmt->execute();
                    $stmt->close();
                    echo '<div class="alert alert-success text-center">Seat category deleted successfully.</div>';
                }
            }
        }
    }
}

// Fetch seat data
$query = "SELECT * FROM seat_data";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Display the results in a table format
    echo '<h1 class="text-center mb-4" style="color: white;">Seat Categories Available</h1>'; // Table title
    echo '<div class="container mt-5">';
    echo '<div class="row justify-content-center">';
    echo '<div class="col-md-8">';
    echo '<table class="table table-bordered table-striped table-hover text-center">'; // Center text in the table
    echo '<thead class="thead-dark">';
    echo '<tr>';
    echo '<th>Seat ID</th>';
    echo '<th>Seat Category</th>';
    echo '<th>Seat Price</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['Seat_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Seat_category']) . '</td>';
        echo '<td>' . "Rs " . htmlspecialchars($row['Seat_Price']) . '</td>';
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
    <title>Manage Seats</title>
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
                    <option value="insert">Insert New Seat Category</option>
                    <option value="update">Update Existing Seat Category</option>
                    <option value="delete">Delete Seat Category</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="Seat_ID" class="form-label">Seat ID (for update/delete):</label>
                <input type="number" name="Seat_ID" class="form-control" value="<?php echo isset($Seat_ID) ? $Seat_ID : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="Seat_category" class="form-label">Seat Category:</label>
                <input type="text" name="Seat_category" class="form-control" value="<?php echo isset($Seat_category) ? $Seat_category : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="Seat_price" class="form-label">Seat Price:</label>
                <input type="number" name="Seat_price" class="form-control" step="0.01" value="<?php echo isset($Seat_price) ? $Seat_price : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
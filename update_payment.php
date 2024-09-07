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

        // Check for duplicate payment method
        if ($action == 'insert') {
            $Payment_name = $_POST['Payment_name'];
            $Payment_price = $_POST['Payment_price'];

            // Check if payment method already exists
            $check_query = "SELECT * FROM payment_method WHERE Payment_name = ?";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("s", $Payment_name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<div class="alert alert-danger text-center">Payment method already exists. Please choose a different method.</div>';
            } else {
                // Insert new payment method data
                $insert_query = "INSERT INTO payment_method (Payment_name, Payment_price) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_query);
                $stmt->bind_param("sd", $Payment_name, $Payment_price);
                $stmt->execute();
                $stmt->close();
                echo '<div class="alert alert-success text-center">Payment method inserted successfully.</div>';
            }
            
            // Clear form fields
            $Payment_name = "";
            $Payment_price = "";
        } elseif ($action == 'update' || $action == 'delete') {
            // For update and delete, we need the Payment_ID
            $Payment_ID = $_POST['Payment_ID'];

            // Check if Payment_ID exists
            $check_id_query = "SELECT * FROM payment_method WHERE Payment_ID = ?";
            $stmt = $conn->prepare($check_id_query);
            $stmt->bind_param("i", $Payment_ID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                echo '<div class="alert alert-danger text-center">Payment ID does not exist.</div>';
            } else {
                if ($action == 'update') {
                    $Payment_name = $_POST['Payment_name'];
                    $Payment_price = $_POST['Payment_price'];

                    // Check for duplicate payment method during update
                    $check_update_query = "SELECT * FROM payment_method WHERE Payment_name = ? AND Payment_ID != ?";
                    $stmt = $conn->prepare($check_update_query);
                    $stmt->bind_param("si", $Payment_name, $Payment_ID);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo '<div class="alert alert-danger text-center">Payment method already exists. Please choose a different method.</div>';
                    } else {
                        // Update payment method data
                        $update_query = "UPDATE payment_method SET Payment_name = ?, Payment_price = ? WHERE Payment_ID = ?";
                        $stmt = $conn->prepare($update_query);
                        $stmt->bind_param("sdi", $Payment_name, $Payment_price, $Payment_ID);
                        $stmt->execute();
                        $stmt->close();
                        echo '<div class="alert alert-success text-center">Payment method updated successfully.</div>';
                    }

                    // Clear form fields
                    $Payment_name = "";
                    $Payment_price = "";
                } elseif ($action == 'delete') {
                    // Delete payment method data
                    $delete_query = "DELETE FROM payment_method WHERE Payment_ID = ?";
                    $stmt = $conn->prepare($delete_query);
                    $stmt->bind_param("i", $Payment_ID);
                    $stmt->execute();
                    $stmt->close();
                    echo '<div class="alert alert-success text-center">Payment method deleted successfully.</div>';
                }
            }
        }
    }
}

// Fetch payment method data
$query = "SELECT * FROM payment_method";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Display the results in a table format
    echo '<h1 class="text-center mb-4" style="color: white;">Payment Methods Available</h1>'; // Table title
    echo '<div class="container mt-5">';
    echo '<div class="row justify-content-center">';
    echo '<div class="col-md-8">';
    echo '<table class="table table-bordered table-striped table-hover text-center">'; // Center text in the table
    echo '<thead class="thead-dark">';
    echo '<tr>';
    echo '<th>Payment ID</th>';
    echo '<th>Payment Method</th>';
    echo '<th>Payment Price</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['Payment_ID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['Payment_name']) . '</td>';
        echo '<td>' . "Rs " . htmlspecialchars($row['Payment_price']) . '</td>';
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
    <title>Manage Payments</title>
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
                    <option value="insert">Insert New Payment Method</option>
                    <option value="update">Update Existing Payment Method</option>
                    <option value="delete">Delete Payment Method</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="Payment_ID" class="form-label">Payment ID (for update/delete):</label>
                <input type="number" name="Payment_ID" class="form-control" value="<?php echo isset($Payment_ID) ? $Payment_ID : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="Payment_name" class="form-label">Payment Method:</label>
                <input type="text" name="Payment_name" class="form-control" value="<?php echo isset($Payment_name) ? $Payment_name : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="Payment_price" class="form-label">Payment Price:</label>
                <input type="number" name="Payment_price" class="form-control" step="0.01" value="<?php echo isset($Payment_price) ? $Payment_price : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
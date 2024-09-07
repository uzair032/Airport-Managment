<?php 
session_start();
include 'dbconnect.php';
// Check if user is authenticated
if (!isset($_SESSION['Auth']) || $_SESSION['Auth'] == false) {
    header("Location: login.php");
    exit();
}

// Check if flight details are entered
if (!isset($_SESSION['FlightDetailsEntered']) || !$_SESSION['FlightDetailsEntered']) {
    header("Location: Flight.php");
    exit();
}

// Set session variable for the payment page
$_SESSION['AtPaymentPage'] = true;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_ID = rand(1, 100); // Generate random payment ID

    $paymentMethod = isset($_POST['Payment_Method']) ? $_POST['Payment_Method'] : '';
    $accountNumber = $_POST['txtaccount'];
    $cvv = $_POST['txtCVV'];
    $expirationDate = $_POST['txtExpiration'];
    $cardholderName = $_POST['txtCardholderName'];
    $billingAddress = $_POST['txtBillingAddress'];
    $insertedAt = gmdate('Y-m-d H:i:s');
    // $dbuser = $_SESSION['user_id'];
    $airport_ID = $_SESSION['airport_ID'];
    $flight_ID = $_SESSION['flight_ID'];

    $checkQuery = "SELECT COUNT(*) FROM PAYMENT_t WHERE Payment_ID = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $payment_ID);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        return "Payment ID already exists. Please use a unique ID.";
    }

    // $query = "INSERT INTO PAYMENT_t (Payment_ID, Flight_ID, Airport_ID, CNIC, Paymentmethod, Accountnumber, CVV, expirationdate, Cardholder, Billing_address, InsertedAt) 
    //           VALUES ('$payment_ID', '$flight_ID', '$airport_ID', '$dbcnic', '$paymentMethod', '$accountNumber', '$cvv', '$expirationDate', '$cardholderName', '$billingAddress', '$insertedAt')";
    
    $query = "INSERT INTO PAYMENT_t (Payment_ID, Flight_ID, Airport_ID, Paymentmethod, Accountnumber, CVV, expirationdate, Cardholder, Billing_address, InsertedAt) 
    VALUES ('$payment_ID', '$flight_ID', '$airport_ID', '$paymentMethod', '$accountNumber', '$cvv', '$expirationDate', '$cardholderName', '$billingAddress', '$insertedAt')";

    if (mysqli_query($conn, $query)) {
        $total_bill = add_to_bill($paymentMethod,$conn);
        $_SESSION['PaymentDetailsEntered'] = true;
        $_SESSION['payment_ID'] = $payment_ID;
        make_ticket($conn);
        // Redirect to Checkout page
        header("Location: Checkout.php");
        exit();
    } else {
        $_SESSION["message"] = "An error occurred while adding the passenger: " . mysqli_error($conn);
    }

    exit();
}

function make_ticket($conn){
    $Ticket_ID = rand(1, 100); // Generate random Ticket ID
    // $dbuser = $_SESSION['user_id'];
    $total_bill = $_SESSION['total_bill'];
    $dbpayment_ID = $_SESSION['payment_ID'];
   
    // Insert ticket data
    $query = "INSERT INTO TICKET_t (Ticket_ID, Payment_ID, Total_price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $Ticket_ID, $dbpayment_ID, $total_bill);

    if ($stmt->execute()) {
        // Set the Ticket_ID session variable
        $_SESSION['Ticket_ID'] = $Ticket_ID;
        
    } else {
        echo "An error occurred while adding the ticket: " . $stmt->error;
    }
}

function add_to_bill($paymentMethod,$conn) {
    $total_bill = 0;
    // Query to get seat price
    $payment_sql = "SELECT Payment_price FROM Payment_method WHERE Payment_name = '$paymentMethod'";
    $payment_check = mysqli_query($conn, $payment_sql);

    // Check if the query was successful
    if ($payment_check === false) {
        // Log error or handle it
        error_log("Database query failed: " . mysqli_error($conn));
        return 0; // Return 0 if there's an error
    }

    // Process payment prices
    if (mysqli_num_rows($payment_check) > 0) {
        while ($row = mysqli_fetch_assoc($payment_check)) {
            $payment_price = $row["Payment_price"];
            $total_bill += $payment_price;
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
    <title>Final Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('./resources/airport.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            flex-direction: column;
        }
        form {
            background: rgba(248, 248, 255, 0.5450980392);
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            margin-bottom: 5px;
            padding: 20px;
        }
        .container {
            width: 700px;
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
        <!-- Logout control can be implemented with a simple button or link -->
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="container">
            <h1 class="text-center mb-3">Payment Details</h1>
            <div class="form-group mb-3">
                <label class="form-label">Accepted Cards:</label>
                <img src="./resources/imgcards.png" class="img-fluid" alt="Responsive image" />
            </div>
            <div class="form-group mb-3">
                <label for="Payment_Method" class="form-label">Payment Method:</label>
                <select id="Payment_Method" class="form-select" name="Payment_Method">
                    <option value="visa">Visa</option>
                    <option value="master_card">Master Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="credit_card">Credit Card</option>
                </select>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="txtaccount" class="form-label">Account/Card Number:</label>
                        <input type="text" id="txtaccount" class="form-control" placeholder="Enter 16-digit Account/Card Number" name="txtaccount" required pattern="\d{16}">
                        <div class="text-danger" id="rfvAccountNumber" style="display:none;">Account/Card Number is required</div>
                        <div class="text-danger" id="revAccountNumber" style="display:none;">Invalid Account/Card Number format</div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="txtCVV" class="form-label">CVV:</label>
                        <input type="text" id="txtCVV" class="form-control" placeholder="Enter 3-digit CVV" name="txtCVV" required pattern="\d{3}">
                        <div class="text-danger" id="RequiredFieldValidator1" style="display:none;">CVV is required</div>
                        <div class="text-danger" id="RegularExpressionValidator1" style="display:none;">Invalid CVV format</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="txtExpiration" class="form-label">Expiration Date:</label>
                        <input type="text" id="txtExpiration" class="form-control" placeholder="MM/YY" name="txtExpiration" required pattern="^(0[1-9]|1[0-2])\/\d{2}$">
                        <div class="text-danger" id="RequiredFieldValidator2" style="display:none;">Expiration Date is required</div>
                        <div class="text-danger" id="RegularExpressionValidator2" style="display:none;">Invalid Expiration Date format (MM/YY)</div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group mb-3">
                        <label for="txtCardholderName" class="form-label">Cardholder Name:</label>
                        <input type="text" id="txtCardholderName" class="form-control" name="txtCardholderName" required>
                        <div class="text-danger" id="RequiredFieldValidator3" style="display:none;">Cardholder Name is required</div>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="txtBillingAddress" class="form-label">Billing Address:</label>
                <input type="text" id="txtBillingAddress" class="form-control" name="txtBillingAddress" required>
                <div class="text-danger" id="RequiredFieldValidator4" style="display:none;">Billing Address is required</div>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Submit Payment</button>
            </div>

            <div class="form-group text-center">
                <label id="lblmessage" class="text-success"></label>
            </div>
        </div>
    </form>
</body>
</html>
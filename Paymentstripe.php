<?php
session_start();
include 'dbconnect.php';

require __DIR__ . "/vendor/autoload.php";
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

$stripe_secret_key = "sk_test_51PslmpCP2DlfiumElREv8jeR4Pn2lry8IZcM8sEOiUXEtVzaAewARno1DQmVQT0vBkVhFsvaA80VgORP04s1Lflg00FCxl2LUV";

\Stripe\Stripe::setApiKey($stripe_secret_key);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_ID = rand(1, 100); // Generate random payment ID

   // Retrieve the payment method ID from the form submission
   $paymentMethodId = $_POST['payment_method_id']; // This should be sent from the client-side

   // Retrieve other necessary data from the session or form
   $paymentMethod = isset($_POST['Payment_Method']) ? $_POST['Payment_Method'] : '';
//    $paymentMethods = isset($_POST['Payment_Method']) ? $_POST['Payment_Method'] : [];
   $cardholderName = $_POST['txtCardholderName'];
   $billingAddress = $_POST['txtBillingAddress'];
   $insertedAt = gmdate('Y-m-d H:i:s');
   $airport_ID = $_SESSION['airport_ID'];
   $flight_ID = $_SESSION['flight_ID'];

   // Calculate the total bill based on selected payment methods
   $total_bill = add_to_bill($paymentMethod, $conn); // Pass the array of payment methods

   // Convert to cents if necessary (assuming the price is in PKR)
   $total_bill = $total_bill * 100; // Convert to cents

    // print_r($total_bill);
    // Create a PaymentIntent
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $total_bill,
        'currency' => 'pkr',
        'payment_method' => $paymentMethodId,
        'confirmation_method' => 'manual',
        'confirm' => true,
        'return_url' => 'http://localhost/projects/airline/checkout.php', // Replace with your actual success URL
    ]);

    // Handle the payment result
    if ($paymentIntent->status === 'succeeded') {
        // Payment was successful
        // $query = "INSERT INTO PAYMENT_t (Payment_ID, Flight_ID, Airport_ID, Paymentmethod, Accountnumber, CVV, expirationdate, Cardholder, Billing_address, InsertedAt) 
        //           VALUES ('$payment_ID', '$flight_ID', '$airport_ID', '$paymentMethod', '$accountNumber', '$cvv', '$expirationDate', '$cardholderName', '$billingAddress', '$insertedAt')";

        // print_r()
        $query = "INSERT INTO PAYMENT_t (Payment_ID, Flight_ID, Airport_ID, Paymentmethod, Cardholder, Billing_address, InsertedAt) 
                  VALUES ('$payment_ID', '$flight_ID', '$airport_ID', '$paymentMethod', '$cardholderName', '$billingAddress', '$insertedAt')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['PaymentDetailsEntered'] = true;
            $_SESSION['payment_ID'] = $payment_ID;
            make_ticket($conn);
            // Redirect to Checkout page
            header("Location: Checkout.php");
            exit();
        } else {
            $_SESSION["message"] = "An error occurred while adding the passenger: " . mysqli_error($conn);
        }
    } else {
        // Payment failed
        $_SESSION["message"] = "Payment failed: " . $paymentIntent->last_payment_error->message;
    }
}

function make_ticket($conn) {
    $Ticket_ID = rand(1, 100); // Generate random Ticket ID
    $total_bill = $_SESSION['total_bill'];
    $dbpayment_ID = $_SESSION['payment_ID'];

    $query = "INSERT INTO TICKET_t (Ticket_ID, Payment_ID, Total_price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $Ticket_ID, $dbpayment_ID, $total_bill);

    if ($stmt->execute()) {
        $_SESSION['Ticket_ID'] = $Ticket_ID;
    } else {
        echo "An error occurred while adding the ticket: " . $stmt->error;
    }
}

function add_to_bill($paymentMethod, $conn) {
    $total_bill = 0;
    $final_bill = 0;
    // Query to get seat price
    $payment_sql = "SELECT Payment_price FROM Payment_method WHERE Payment_name = ?";
    $stmt = $conn->prepare($payment_sql);
    $stmt->bind_param("s", $paymentMethod);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $payment_price = $row["Payment_price"];
            $total_bill += $payment_price;
        }
    }

    // print_r($_SESSION["total_bill"]);
    $_SESSION["total_bill"] += $total_bill; // Store the total bill in session
    $final_bill += $_SESSION["total_bill"];
    // print_r($_SESSION["total_bill"]);
    return $final_bill;
}

// function add_to_bill($paymentMethods, $conn) {
//     $total_bill = 0;

//     // Loop through each payment method and get the corresponding price
//     foreach ($paymentMethods as $paymentMethod) {
//         // Query to get payment price for each method
//         $payment_sql = "SELECT Payment_price FROM Payment_method WHERE Payment_name = ?";
//         $stmt = $conn->prepare($payment_sql);
//         $stmt->bind_param("s", $paymentMethod);
//         $stmt->execute();
//         $result = $stmt->get_result();

//         if ($result->num_rows > 0) {
//             while ($row = $result->fetch_assoc()) {
//                 $payment_price = $row["Payment_price"];
//                 $total_bill += $payment_price; // Add the price to the total bill
//             }
//         }
//     }

//     $_SESSION["total_bill"] = $total_bill; // Store the total bill in session
//     return $total_bill; // Return the total bill
// }
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
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <form id="payment-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="container">
            <h1 class="text-center mb-3">Payment Details</h1>
            <div class="form-group mb-3">
                <label class="form-label">Accepted Cards:</label>
                <img src="./resources/imgcards.png" class="img-fluid" alt="Responsive image" />
            </div>
            <div class="form-group mb-3">
                <label for="Payment_Method" class="form-label">Payment Method:</label>
                <select id="Payment_Method" class="form-select" name="Payment_Method">
                    <option value="Visa">Visa</option>
                    <option value="Master_card">Master Card</option>
                    <option value="Paypal">PayPal</option>
                    <option value="Bank_transfer">Bank Transfer</option>
                    <option value="Credit_card">Credit Card</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="card-element" class="form-label">Credit or Debit Card</label>
                <div id="card-element"></div>
                <div id="card-errors" role="alert" class="text-danger"></div>
            </div>

            <div class="form-group mb-3">
                <label for="txtCardholderName" class="form-label">Cardholder Name:</label>
                <input type="text" id="txtCardholderName" class="form-control" name="txtCardholderName" required>
                <div class="text-danger" id="RequiredFieldValidator3" style="display:none;">Cardholder Name is required</div>
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
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Initialize Stripe
        const stripe = Stripe('pk_test_51PslmpCP2DlfiumEyEmCQofwG2xuGMe2rhuhyOWbzqErECpQjJmtL6DEuyTsBfMgnEf7QC1HrvzphGTWCQXCcW0A004PSmL8hf'); // Replace with your public key
        const elements = stripe.elements();

        // Create an instance of the card Element
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const { paymentMethod, error } = await stripe.createPaymentMethod({
                type: 'card',
                card: cardElement,
                billing_details: {
                    name: document.getElementById('txtCardholderName').value,
                    address: {
                        line1: document.getElementById('txtBillingAddress').value,
                    },
                },
            });

            if (error) {
                // Show error in #card-errors
                document.getElementById('card-errors').textContent = error.message;
            } else {
                // Send paymentMethod.id to your server (process_payment.php)
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'payment_method_id');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        });
    </script>
</body>
</html>
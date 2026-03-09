<?php
session_start();

// Include Razorpay SDK
require 'config.php';
require_once __DIR__ . '/razorpay-php-master/Razorpay.php';
use Razorpay\Api\Api;

// Razorpay test keys (replace with your actual keys)
$razorpay_key_id     = $_ENV['RAZORPAY_KEY_ID'];
$razorpay_key_secret = $_ENV['RAZORPAY_KEY_SECRET'];

// At the top of checkout.php, after session_start()
function getCart() {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        return $_SESSION['cart'];
    }
    if (isset($_COOKIE['plant_hub_cart'])) {
        return json_decode($_COOKIE['plant_hub_cart'], true) ?: [];
    }
    return [];
}

$cart = getCart();

$total_price = 0;
foreach ($cart as $item) {
    $total_price += floatval($item['price'] ?? 0);
}


// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=planthub', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize variables
$errors          = [];
$success_message = '';
$order_id        = null;
$cart            = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
// $total_price     = 0;

// Calculate total price
foreach ($cart as $item) {
    $total_price += floatval($item['price']);
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_checkout'])) {
    $user_name     = trim($_POST['user_name'] ?? '');
    $user_phone    = trim($_POST['user_phone'] ?? '');
    $user_location = trim($_POST['user_location'] ?? '');
    $user_zip      = trim($_POST['user_zip'] ?? '');
    $user_state    = trim($_POST['user_state'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'cod';

    // Validate input
    if (empty($user_name)) $errors[] = "Name is required.";
    if (empty($user_phone) || !preg_match('/^[0-9]{10,15}$/', $user_phone)) $errors[] = "Valid phone number (10-15 digits) is required.";
    if (empty($user_location)) $errors[] = "Location/Address is required.";
    if (empty($user_zip) || !preg_match('/^[0-9]{6}$/', $user_zip)) $errors[] = "Valid 6-digit ZIP code is required.";
    if (empty($user_state)) $errors[] = "State is required.";
    if (empty($cart)) $errors[] = "Cart is empty.";
    if (!in_array($payment_method, ['cod', 'online'])) $errors[] = "Invalid payment method.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Generate unique order reference
            do {
                $order_reference = 'ORD-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 10));
                $check = $pdo->prepare("SELECT id FROM orders WHERE order_reference = ?");
                $check->execute([$order_reference]);
            } while ($check->fetch());

            // Insert into orders
            $stmt = $pdo->prepare("
                INSERT INTO orders 
                (user_id, order_reference, user_name, user_phone, user_location, user_zip, user_state, total_amount, payment_method, payment_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $user_id,
                $order_reference,
                $user_name,
                $user_phone,
                $user_location,
                $user_zip,
                $user_state,
                $total_price,
                $payment_method
            ]);
            $order_id = $pdo->lastInsertId();

            // Insert order items
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_name, price, image, payment_status)
                VALUES (?, ?, ?, ?, 'pending')
            ");
            foreach ($cart as $item) {
                $stmt->execute([
                    $order_id,
                    $item['name'],
                    $item['price'],
                    $item['image']
                ]);
            }

            $pdo->commit();

            // For COD: success immediately
            if ($payment_method === 'cod') {
                $_SESSION['cart'] = [];
                setcookie('plant_hub_cart', '', time() - 3600, "/");
                $success_message = "Order placed successfully via COD! Order Reference: <strong>$order_reference</strong>";
            }

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Prepare Razorpay data for online payment
$razorpay_order_id = null;
$razorpay_options  = null;

if (!empty($order_id) && isset($_POST['payment_method']) && $_POST['payment_method'] === 'online' && empty($errors)) {
    try {
        $api = new Api($razorpay_key_id, $razorpay_key_secret);

        $razorpayOrder = $api->order->create([
            'receipt'         => "order_$order_id",
            'amount'          => $total_price * 100, // in paise
            'currency'        => 'INR',
            'payment_capture' => 1
        ]);

        $razorpay_order_id = $razorpayOrder['id'];

        $razorpay_options = [
            'key'         => $razorpay_key_id,
            'amount'      => $total_price * 100,
            'currency'    => 'INR',
            'name'        => 'Plant-Hub',
            'description' => 'Order #' . $order_reference,
            'image'       => 'plant.png',
            'order_id'    => $razorpay_order_id,
            'prefill'     => [
                'name'    => $user_name,
                'email'   => $_SESSION['email'] ?? 'guest@example.com',
                'contact' => $user_phone
            ],
            'notes'       => [
                'order_id'       => $order_id,
                'order_reference'=> $order_reference
            ],
            'theme'       => ['color' => '#22c55e']
        ];
    } catch (Exception $e) {
        $errors[] = "Razorpay order creation failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="plant.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <title>Checkout - Plant-Hub</title>
    <style>
        .error { color: red; }
        #successModal, #paymentModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center; align-items: center;
            z-index: 1000;
        }
        #successModal.show, #paymentModal.show { display: flex; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto p-6 pt-24">
        <h1 class="text-4xl font-bold text-gray-700 text-center mb-10">Checkout</h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message && $_POST['payment_method'] === 'cod'): ?>
            <div id="successModal" class="show">
                <div class="bg-white p-8 rounded-xl shadow-2xl max-w-lg w-full text-center">
                    <h2 class="text-3xl font-bold text-green-600 mb-4">Order Placed!</h2>
                    <p class="text-lg text-gray-700 mb-6"><?= $success_message ?></p>
                    <p class="text-sm text-gray-500 mb-6">You'll receive confirmation soon. Thank you for shopping with Plant-Hub 🌱</p>
                    <button id="closeSuccess" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-full">Continue Shopping</button>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold text-green-700 mb-6">Cart Summary</h2>
        <div class="bg-white p-6 rounded-xl shadow-lg border border-green-200 mb-10">
            <?php if (empty($cart)): ?>
                <p class="text-gray-600">Your cart is empty.</p>
            <?php else: ?>
                <div class="space-y-4 max-h-80 overflow-y-auto">
                    <?php foreach ($cart as $item): ?>
                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-16 h-16 object-cover rounded">
                                <div>
                                    <p class="font-medium"><?= htmlspecialchars($item['name']) ?></p>
                                    <p class="text-sm text-gray-600">₹ <?= number_format($item['price'], 2) ?></p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-800">₹ <?= number_format($item['price'], 2) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-6 pt-4 border-t">
                    <p class="text-xl font-bold text-green-700 text-right">
                        Total: ₹ <?= number_format($total_price, 2) ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($cart)): ?>
            <h2 class="text-2xl font-bold text-green-700 mb-6">Delivery & Payment Details</h2>
            <form method="POST" action="" class="bg-white p-8 rounded-xl shadow-lg border border-green-200">
                <input type="hidden" name="total_price" value="<?= $total_price ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Full Name</label>
                        <input type="text" name="user_name" value="<?= htmlspecialchars($_POST['user_name'] ?? ($_SESSION['name'] ?? '')) ?>" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Phone Number</label>
                        <input type="text" name="user_phone" value="<?= htmlspecialchars($_POST['user_phone'] ?? '') ?>" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-2">Delivery Address</label>
                    <input type="text" name="user_location" value="<?= htmlspecialchars($_POST['user_location'] ?? '') ?>" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">PIN Code</label>
                        <input type="text" name="user_zip" value="<?= htmlspecialchars($_POST['user_zip'] ?? '') ?>" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">State</label>
                        <input type="text" name="user_state" value="<?= htmlspecialchars($_POST['user_state'] ?? '') ?>" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 mb-4">Payment Method</h3>
                <div class="space-y-4 mb-8">
                    <label class="flex items-center space-x-3">
                        <input type="radio" name="payment_method" value="cod" checked class="h-5 w-5 text-green-600">
                        <span class="text-gray-700">Cash on Delivery (COD)</span>
                    </label>
                    <label class="flex items-center space-x-3">
                        <input type="radio" name="payment_method" value="online" class="h-5 w-5 text-green-600">
                        <span class="text-gray-700">Pay Online (Razorpay - Cards, UPI, Wallets)</span>
                    </label>
                </div>

                <div class="text-center">
                    <button type="submit" name="submit_checkout" class="bg-green-600 hover:bg-green-700 text-white px-10 py-4 rounded-full shadow-lg text-lg font-semibold transition">
                        <?= ($_POST['payment_method'] ?? 'cod') === 'online' ? 'Proceed to Payment' : 'Place Order' ?>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Razorpay Payment Modal -->
    <?php if ($razorpay_options): ?>
        <div id="paymentModal" class="show">
            <div class="bg-white p-8 rounded-xl shadow-2xl max-w-md w-full text-center">
                <h2 class="text-2xl font-bold text-green-600 mb-4">Proceed to Payment</h2>
                <p class="text-gray-600 mb-6">You will be redirected to secure Razorpay payment page.</p>
                <button id="rzp-button" class="bg-green-600 hover:bg-green-700 text-white px-10 py-4 rounded-full text-lg font-semibold">
                    Pay ₹ <?= number_format($total_price, 2) ?>
                </button>
            </div>
        </div>

        <script>
            var options = <?= json_encode($razorpay_options) ?>;

            options.handler = function (response) {
                window.location.href = `payment-callback.php?razorpay_payment_id=${response.razorpay_payment_id}&razorpay_order_id=${response.razorpay_order_id}&razorpay_signature=${response.razorpay_signature}&order_id=<?= $order_id ?>`;
            };

            var rzp = new Razorpay(options);

            document.getElementById('rzp-button').onclick = function(e) {
                rzp.open();
                e.preventDefault();
            };
        </script>
    <?php endif; ?>

    <!-- Success Modal Script (for COD) -->
    <script>
        document.getElementById('closeSuccess')?.addEventListener('click', function() {
            document.getElementById('successModal').classList.remove('show');
            setTimeout(() => { window.location.href = 'index.php'; }, 500);
        });
    </script>
</body>
</html>
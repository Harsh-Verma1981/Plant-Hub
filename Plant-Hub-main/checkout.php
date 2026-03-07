<?php
session_start();

// Database connection (update with your credentials)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=planthub', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Initialize variables
$errors = [];
$total_price = isset($_POST['total_price']) ? (float)$_POST['total_price'] : 0;
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$success_message = '';
$order_id = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_checkout'])) {
    $user_name = trim($_POST['user_name']);
    $user_phone = trim($_POST['user_phone']);
    $user_location = trim($_POST['user_location']);
    $user_zip = trim($_POST['user_zip']);
    $user_state = trim($_POST['user_state']);

    // Validate input
    if (empty($user_name)) $errors[] = "Name is required.";
    if (empty($user_phone) || !preg_match('/^[0-9]{10,15}$/', $user_phone)) $errors[] = "Valid phone number is required.";
    if (empty($user_location)) $errors[] = "Location is required.";
    if (empty($user_zip) || !preg_match('/^[0-9]{6}$/', $user_zip)) $errors[] = "Valid ZIP code is required (e.g., 110001).";
    if (empty($user_state)) $errors[] = "State is required.";
    if (empty($cart)) $errors[] = "Cart is empty.";

    if (empty($errors)) {
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, user_name, user_phone, user_location, user_zip, user_state, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $user_name, $user_phone, $user_location, $user_zip, $user_state, $total_price]);
            $order_id = $pdo->lastInsertId();

            // Insert order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, price, image) VALUES (?, ?, ?, ?)");
            foreach ($cart as $item) {
                $stmt->execute([$order_id, $item['name'], $item['price'], $item['image']]);
            }

            // Commit transaction
            $pdo->commit();

            // Clear cart
            $_SESSION['cart'] = [];

            // Set success message for pop-up
            $success_message = "Order placed successfully! Order ID: $order_id";

            // Clear POST data to reset form
            $_POST = [];
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Failed to place order: " . $e->getMessage();
        }
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
    <title>Checkout - Plant-Hub</title>
    <style>
        /*
        body {
            margin: 0;
            padding: 0;
            background-color: #a9d37c;
            --color: rgba(114, 114, 114, 0.3);
            background-image: linear-gradient(0deg, transparent 24%, var(--color) 25%, var(--color) 26%, transparent 27%, transparent 74%, var(--color) 75%, var(--color) 76%, transparent 77%, transparent),
                linear-gradient(90deg, transparent 24%, var(--color) 25%, var(--color) 26%, transparent 27%, transparent 74%, var(--color) 75%, var(--color) 76%, transparent 77%, transparent);
            background-size: 55px 55px;
        } 
        */
        .error { color: red; }
        #successModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        #successModal.show {
            display: flex;
        }
    </style>
</head>
<body>
    <div class="h-20"></div>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-4xl font-bold text-gray-600 text-center mb-8">Checkout</h1>

        <?php if (!empty($errors)): ?>
            <div class="error bg-red-100 p-4 rounded-lg mb-6">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold text-green-700 mb-4">Cart Summary</h2>
        <div class="bg-white p-6 rounded-xl shadow-2xl border border-green-300 mb-6">
            <?php if (empty($cart)): ?>
                <p class="text-gray-600">Your cart is empty.</p>
            <?php else: ?>
                <div class="space-y-4 max-h-72 overflow-y-auto pr-2">
                    <?php foreach ($cart as $item): ?>
                        <div class="flex items-center justify-between bg-gray-100 p-3 rounded-lg shadow-sm">
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-12 h-12 object-cover rounded">
                                <div>
                                    <p class="font-medium"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="text-sm text-gray-600">Rs. <?php echo number_format($item['price'], 2); ?></p>
                                </div>
                            </div>
                            <p class="font-medium">Rs. <?php echo number_format($item['price'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-6 border-t pt-4">
                    <p class="text-lg font-semibold text-green-700">Total: Rs. <?php echo number_format($total_price, 2); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="text-2xl font-bold text-green-700 mb-4">User Information</h2>
        <form method="POST" action="checkout.php" class="bg-white p-6 rounded-xl shadow-2xl border border-green-300">
            <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Name:</label>
                <input type="text" name="user_name" value="<?php echo isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : (isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''); ?>" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Phone Number:</label>
                <input type="text" name="user_phone" value="<?php echo isset($_POST['user_phone']) ? htmlspecialchars($_POST['user_phone']) : ''; ?>" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Location (Address):</label>
                <input type="text" name="user_location" value="<?php echo isset($_POST['user_location']) ? htmlspecialchars($_POST['user_location']) : ''; ?>" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">ZIP Code:</label>
                <input type="text" name="user_zip" value="<?php echo isset($_POST['user_zip']) ? htmlspecialchars($_POST['user_zip']) : ''; ?>" class="w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">State:</label>
                <input type="text" name="user_state" value="<?php echo isset($_POST['user_state']) ? htmlspecialchars($_POST['user_state']) : ''; ?>" class="w-full p-2 border rounded">
            </div>
            <button type="submit" name="submit_checkout" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-full shadow-md transition-all">Place Order</button>
        </form>
    </div>

    <!-- Success Modal -->
    <?php if (!empty($success_message)): ?>
        <div id="successModal" class="show">
            <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full">
                <h2 class="text-2xl font-bold text-green-600 mb-4">Success!</h2>
                <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($success_message); ?></p>
                <button id="closeModal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full">Close</button>
            </div>
        </div>
        <script>
            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('successModal').classList.remove('show');
                // Redirect to index.php after 3 seconds
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000);
            });
        </script>
    <?php endif; ?>
</body>
</html>
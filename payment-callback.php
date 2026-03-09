<?php
session_start();
require_once __DIR__ . '/razorpay-php-master/Razorpay.php';
require 'config.php';

use Razorpay\Api\Api;

$razorpay_key_id     = $_ENV['RAZORPAY_KEY_ID'];
$razorpay_key_secret = $_ENV['RAZORPAY_KEY_SECRET'];

$api = new Api($razorpay_key_id, $razorpay_key_secret);

if (!isset($_GET['razorpay_payment_id']) || !isset($_GET['razorpay_order_id']) || !isset($_GET['razorpay_signature']) || !isset($_GET['order_id'])) {
    header("Location: checkout.php?error=missing_parameters");
    exit;
}

try {
    $attributes = [
        'razorpay_order_id'   => $_GET['razorpay_order_id'],
        'razorpay_payment_id' => $_GET['razorpay_payment_id'],
        'razorpay_signature'  => $_GET['razorpay_signature']
    ];

    $api->utility->verifyPaymentSignature($attributes);

    // All good → update database
    $pdo = new PDO('mysql:host=localhost;dbname=planthub', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $order_id = (int)$_GET['order_id'];

    if ($order_id <= 0) {
        throw new Exception("Invalid order ID");
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid' WHERE id = ? AND payment_status = 'pending'");
    $stmt->execute([$order_id]);

    $stmt = $pdo->prepare("UPDATE order_items SET payment_status = 'paid' WHERE order_id = ?");
    $stmt->execute([$order_id]);

    $pdo->commit();

    // Clear cart
    $_SESSION['cart'] = [];
    setcookie('plant_hub_cart', '', time() - 3600, "/");

    // Get order reference
    $ref = $pdo->query("SELECT order_reference FROM orders WHERE id = $order_id")->fetchColumn();

    // Set success message
    $_SESSION['success_message'] = "Payment successful! Order #$ref confirmed. 🌱";

    // Redirect with success (you can also pass order_reference if you stored it)
    $redirect_msg = urlencode("Payment successful! Thank you for your order.");
    header("Location: index.php?success=1&msg=$redirect_msg");
    exit;

} catch (Exception $e) {
    error_log("Razorpay callback error: " . $e->getMessage());
    header("Location: checkout.php?error=payment_verification_failed");
    exit;
}
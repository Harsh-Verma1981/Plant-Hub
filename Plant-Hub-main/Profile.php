<?php
session_start();
    
require 'config.php';
// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Temporary check: only require name in session (remove user_id dependency)
if (!isset($_SESSION['name'])) {
    header("Location: login.php");
    exit();
}

include 'SignupDatabase.php'; // $connect

$current_name  = $_SESSION['name'];
$current_email = $_SESSION['email'] ?? ''; // optional - will be empty if not set

// ────────────────────────────────────────────────
// A) Update profile (name + email)
if (isset($_POST['update_profile'])) {
    $new_name  = trim($_POST['name']);
    $new_email = trim($_POST['email']);

    if (!empty($new_name) && (!empty($new_email) || filter_var($new_email, FILTER_VALIDATE_EMAIL))) {
        // If you already have email in DB, update it; otherwise skip email update
        $sql = "UPDATE signup SET name = ?" . ($new_email ? ", email = ?" : "") . " WHERE name = ?";
        $stmt = $connect->prepare($sql);

        if ($new_email) {
            $stmt->bind_param("sss", $new_name, $new_email, $current_name);
        } else {
            $stmt->bind_param("ss", $new_name, $current_name);
        }

        if ($stmt->execute()) {
            $_SESSION['name']  = $new_name;
            if ($new_email) $_SESSION['email'] = $new_email;
            $profile_success = "Profile updated successfully!";
        } else {
            $profile_error = "Failed to update profile.";
        }
        $stmt->close();
    } else {
        $profile_error = "Please enter a valid name" . ($new_email ? " and email" : "") . ".";
    }
}

// ────────────────────────────────────────────────
// B) Request OTP for password change
if (isset($_POST['request_otp'])) {
    if (empty($current_email)) {
        $otp_message = "No email found in session. Please update your email first.";
    } else {
        $otp = rand(100000, 999999);
        $_SESSION['otp']       = $otp;
        $_SESSION['otp_time']  = time();
        $_SESSION['otp_email'] = $current_email;

        require 'PHPMailer/Exception.php';
        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAILER_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAILER_FROM'];
            $mail->Password   = $_ENV['MAILER_PASS'];
            $mail->SMTPSecure = $_ENV['MAILER_SECURE'];
            $mail->Port       = $_ENV['MAILER_PORT'];

            $mail->setFrom($_ENV['MAILER_HOST'], 'Plant-Hub');
            $mail->addAddress($current_email);

            $mail->isHTML(true);
            $mail->Subject = 'Plant-Hub – Password Change OTP';
            $mail->Body    = "
                <h2>Password Reset OTP</h2>
                <p>Hello {$current_name},</p>
                <p>Use this OTP to change your password:</p>
                <h1 style='color:#2e7d32; letter-spacing:8px;'>{$otp}</h1>
                <p><strong>Valid for 10 minutes only.</strong></p>
                <p>If you didn't request this, ignore this email.</p>
                <p>Best regards,<br>Plant-Hub Team</p>
            ";

            $mail->send();
            $otp_sent = true;
            $otp_message = "OTP sent to $current_email (valid for 10 minutes).";
        } catch (Exception $e) {
            $otp_message = "Failed to send OTP: " . $mail->ErrorInfo;
        }
    }
}

// ────────────────────────────────────────────────
// C) Change password after OTP
if (isset($_POST['change_password'])) {
    $entered_otp   = trim($_POST['otp']);
    $new_pwd       = trim($_POST['new_password']);
    $confirm_pwd   = trim($_POST['confirm_password']);

    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_time'])) {
        $pwd_error = "No active OTP. Please request a new one.";
    } elseif (time() - $_SESSION['otp_time'] > 600) {
        $pwd_error = "OTP expired. Request a new one.";
        unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['otp_email']);
    } elseif ($entered_otp !== (string)$_SESSION['otp']) {
        $pwd_error = "Invalid OTP.";
    } elseif ($new_pwd !== $confirm_pwd) {
        $pwd_error = "Passwords do not match.";
    } elseif (strlen($new_pwd) < 6) {
        $pwd_error = "Password must be at least 6 characters.";
    } else {
        // Hash the new password
        $hashed = password_hash($new_pwd, PASSWORD_DEFAULT);

        $stmt = $connect->prepare("UPDATE signup SET password = ? WHERE name = ?");
        $stmt->bind_param("ss", $hashed, $current_name);

        if ($stmt->execute()) {
            $pwd_success = "Password changed successfully!";
            unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['otp_email']);
        } else {
            $pwd_error = "Failed to update password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Profile - Plant-Hub</title>
  <link rel="icon" href="plant.png" type="image/png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { 
        font-family: 'Raleway', sans-serif;
        background: #f0f7f0; 
    }
    .alert-success { 
        background: #d4f4dd; 
        color: #155724; 
        border: 1px solid #c3e6cb;
    }
    .alert-danger { 
        background: #f8d7da; 
        color: #721c24; 
        border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

<!-- Navbar (same as your other pages) -->
<nav class="fixed top-0 w-full bg-gray-300 shadow z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <a href="index.php">
        <img class="h-10 w-auto rounded-full" src="https://media.istockphoto.com/id/1045368942/vector/abstract-green-leaf-logo-icon-vector-design-ecology-icon-set-eco-icon.jpg?s=612x612&w=0&k=20&c=XIfHMI8r1G73blCpCBFmLIxCtOLx8qX0O3mZC9csRLs=" alt="Plant-Hub">
      </a>

      <button id="menu-toggle" class="md:hidden text-3xl">☰</button>

      <ul class="hidden md:flex space-x-8 items-center">
        <li><a href="index.php" class="text-gray-700 hover:text-green-600">Home</a></li>
        <li><a href="Shop.php" class="text-gray-700 hover:text-green-600">Shop</a></li>
        <li class="relative group">
          <span class="text-gray-700 hover:text-green-600 cursor-pointer">About Us</span>
          <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2">
            <a href="who_we_are.php" class="block px-4 py-2 hover:bg-gray-100">Who We Are</a>
            <a href="Developers.php" class="block px-4 py-2 hover:bg-gray-100">Developers</a>
            <a href="Contact.php" class="block px-4 py-2 hover:bg-gray-100">Contact</a>
          </div>
        </li>
        <li><a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a></li>
      </ul>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-gray-100 shadow-lg">
      <ul class="px-4 py-3 space-y-3">
        <li><a href="index.php" class="block">Home</a></li>
        <li><a href="Shop.php" class="block">Shop</a></li>
        <li><a href="who_we_are.php" class="block">Who We Are</a></li>
        <li><a href="Developers.php" class="block">Developers</a></li>
        <li><a href="Contact.php" class="block">Contact</a></li>
        <li><a href="logout.php" class="block text-red-600 font-bold">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="h-20"></div>

<!-- Main Content -->
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
  <h1 class="text-3xl sm:text-4xl font-bold text-center text-green-800 mb-10">My Profile</h1>

  <!-- Profile Update -->
  <div class="bg-white shadow-xl rounded-xl p-6 sm:p-8 mb-10">
    <h2 class="text-2xl font-semibold mb-6 text-green-700">Update Profile</h2>

    <?php if(isset($profile_success)): ?>
      <div class="alert-success p-4 rounded-lg mb-6">
        <?php echo $profile_success; 
            echo "<script>
            setTimeout(function(){
                window.location.href = 'index.php';
            }, 3000);
            </script>";
        ?>
    </div>
    <?php endif; ?>
    <?php if(isset($profile_error)): ?>
      <div class="alert-danger p-4 rounded-lg mb-6">
        <?php echo $profile_error; ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-6">
        <label class="block text-gray-700 font-medium mb-2">Full Name</label>
        <input type="text" name="name" value="<?= htmlspecialchars($current_name) ?>" required
               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>

      <div class="mb-8">
        <label class="block text-gray-700 font-medium mb-2">Email (optional)</label>
        <input type="email" name="email" value="<?= htmlspecialchars($current_email) ?>"
               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>

      <button type="submit" name="update_profile"
              class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">
        Save Changes
      </button>
    </form>
  </div>

  <!-- Change Password -->
  <div class="bg-white shadow-xl rounded-xl p-6 sm:p-8">
    <h2 class="text-2xl font-semibold mb-6 text-green-700">Change Password</h2>

    <?php if(isset($pwd_success)): ?>
      <div class="alert-success p-4 rounded-lg mb-6">
        <?php 
        echo $pwd_success;
        echo "<script>
            setTimeout(function(){
                window.location.href = 'index.php';
            }, 3000);
            </script>";
        ?></div>
    <?php endif; ?>
    <?php if(isset($pwd_error)): ?>
      <div class="alert-danger p-4 rounded-lg mb-6"><?php echo $pwd_error; ?></div>
    <?php endif; ?>

    <?php if (!isset($otp_sent)): ?>
      <form method="post">
        <button type="submit" name="request_otp"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition">
          Send OTP to Email
        </button>
      </form>
    <?php else: ?>
      <p class="text-center text-green-700 font-medium mb-6"><?= $otp_message ?></p>

      <form method="post">
        <div class="mb-6">
          <label class="block text-gray-700 font-medium mb-2">Enter OTP</label>
          <input type="text" name="otp" maxlength="6" pattern="\d{6}" required
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 text-center text-xl tracking-widest focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 font-medium mb-2">New Password</label>
          <input type="password" name="new_password" required minlength="6"
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div class="mb-8">
          <label class="block text-gray-700 font-medium mb-2">Confirm Password</label>
          <input type="password" name="confirm_password" required minlength="6"
                 class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <button type="submit" name="change_password"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg transition">
          Update Password
        </button>
      </form>

      <p class="text-center text-sm text-gray-600 mt-4">
        Didn't receive OTP? <a href="?request_otp=1" class="text-blue-600 hover:underline">Resend</a>
      </p>
    <?php endif; ?>
  </div>
</div>

<!-- Footer -->
<footer class="mt-auto bg-gray-300 py-6 text-center text-gray-700">
  <p>© <?= date("Y") ?> Plant-Hub. All rights reserved.</p>
</footer>

<script>
// Mobile menu toggle
document.getElementById('menu-toggle')?.addEventListener('click', () => {
  document.getElementById('mobile-menu')?.classList.toggle('hidden');
});
</script>

</body>
</html>
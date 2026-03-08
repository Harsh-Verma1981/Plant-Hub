<?php
session_start();
if(!isset($_SESSION['email']) && isset($_COOKIE['email'])) {
    $_SESSION['name'] = $_COOKIE['name'];
    $_SESSION['email'] = $_COOKIE['email'];
    
}
require 'config.php';
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$success = 0;
$user = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'SignupDatabase.php';

    $orgname = $_POST['orgname'];
    $location = $_POST['location'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // Sanitize inputs to prevent SQL injection
    $orgname = mysqli_real_escape_string($connect, $orgname);
    $location = mysqli_real_escape_string($connect, $location);
    $email = mysqli_real_escape_string($connect, $email);
    $phone = mysqli_real_escape_string($connect, $phone);
    $message = mysqli_real_escape_string($connect, $message);

    // Check if organization already exists
    $sql = "SELECT * FROM `organization` WHERE orgname = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $orgname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $num = $result->num_rows;

        if ($num > 0) {
            $user = 1;
        } else {
            // Insert new organization
            $sql = "INSERT INTO `organization` (orgname, location, email, phone, message) VALUES (?, ?, ?, ?, ?)";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param("sssss", $orgname, $location, $email, $phone, $message);
            $result = $stmt->execute();

            if ($result) {
                $success = 1;

                // Send confirmation email using PHPMailer
                require 'PHPMailer/Exception.php';
                require 'PHPMailer/PHPMailer.php';
                require 'PHPMailer/SMTP.php';

                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = $_ENV['MAILER_HOST'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['MAILER_FROM'];
                    $mail->Password = $_ENV['MAILER_PASS'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    // Recipients
                    $mail->setFrom($_ENV['MAILER_FROM'], 'Plant-Hub');
                    $mail->addAddress($email, $orgname);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to Plant-Hub Partnership';
                    $mail->Body = "
                        <h2>Dear $orgname,</h2>
                        <p>Thank you for your interest in partnering with Plant-Hub, your trusted companion in cultivating a green lifestyle.</p>
                        <p>We have successfully received your application to join our community. Below are the details you submitted:</p>
                        <ul>
                            <li><strong>Organization Name:</strong> $orgname</li>
                            <li><strong>Location:</strong> $location</li>
                            <li><strong>Email:</strong> $email</li>
                            <li><strong>Phone:</strong> $phone</li>
                            <li><strong>Message:</strong> $message</li>
                        </ul>
                        <p>Our team will review your application and reach out to you soon to discuss the next steps in our partnership journey.</p>
                        <p>Should you have any questions in the meantime, please feel free to contact us at <a href='mailto:support@planthub.com'>support@planthub.com</a>.</p>
                        <p>Thank you for choosing to grow with us!</p>
                        <p>Best regards,<br>The Plant-Hub Team</p>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    // Log error instead of displaying to user
                    error_log("Failed to send email: " . $mail->ErrorInfo);
                }
            } else {
                die(mysqli_error($connect));
            }
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="plant.png" type="image/png">
    <title>Contact - Plant-Hub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
        body {
            font-family: "Raleway", sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: white;
        }
        /* Custom Alert Box Styling */
        .custom-alert {
            transition: opacity 0.5s ease-in-out;
        }
        .custom-alert.hidden {
            opacity: 0;
            display: none;
        }
        /* Chatbot Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .chatbot-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 800px;
            min-height: 40vh;
            height: auto;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .chatbot-header {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 12px 16px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .chatbot-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }
        .chatbot-header h2 {
            font-size: 1.25rem;
            margin-bottom: 4px;
            position: relative;
            z-index: 1;
        }
        .chatbot-header p {
            opacity: 0.9;
            font-size: 0.75rem;
            position: relative;
            z-index: 1;
        }
        .chat-messages {
            flex: 1;
            padding: 12px 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f8f9fa;
            min-height: 200px;
        }
        .message {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            opacity: 0;
            transform: translateY(20px);
            animation: messageSlide 0.5s ease-out forwards;
        }
        @keyframes messageSlide {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .message.user {
            flex-direction: row-reverse;
        }
        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .bot-avatar {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
        }
        .user-avatar {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }
        .message-content {
            background: white;
            padding: 12px;
            border-radius: 18px;
            max-width: 85%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            line-height: 1.5;
            position: relative;
            font-size: 0.875rem;
        }
        .message.user .message-content {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }
        .message-content::before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            top: 16px;
        }
        .message:not(.user) .message-content::before {
            left: -6px;
            border-right: 6px solid white;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
        }
        .message.user .message-content::before {
            right: -6px;
            border-left: 6px solid #2196F3;
            border-top: 6px solid transparent;
            border-bottom: 6px solid transparent;
        }
        .input-container {
            padding: 12px 16px;
            background: white;
            border-top: 1px solid #e0e0e0;
            position: relative;
        }
        .button-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 8px;
            margin-bottom: 12px;
        }
        .query-button {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border: 2px solid #dee2e6;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 0.75rem;
            color: #495057;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
        }
        .query-button:hover {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            border-color: #4CAF50;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }
        .query-button:active {
            transform: translateY(0);
        }
        .query-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .query-button:hover::before {
            left: 100%;
        }
        .custom-input-section {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e9ecef;
        }
        .input-toggle {
            background: none;
            border: none;
            color: #4CAF50;
            cursor: pointer;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .input-toggle:hover {
            background: rgba(76, 175, 80, 0.1);
        }
        .input-wrapper {
            display: none;
            align-items: center;
            background: #f5f5f5;
            border-radius: 25px;
            padding: 6px 12px;
            gap: 8px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .input-wrapper.active {
            display: flex;
        }
        .input-wrapper:focus-within {
            background: white;
            border-color: #4CAF50;
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.1);
        }
        .message-input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.875rem;
            padding: 8px 0;
            color: #333;
        }
        .message-input::placeholder {
            color: #888;
        }
        .send-button {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        .send-button:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
        }
        .send-button:active {
            transform: scale(0.95);
        }
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }
        .typing-indicator.show {
            opacity: 1;
            transform: translateY(0);
        }
        .typing-dots {
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .typing-dot {
            width: 6px;
            height: 6px;
            background: #4CAF50;
            border-radius: 50%;
            animation: typingPulse 1.5s ease-in-out infinite;
        }
        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typingPulse {
            0%, 60%, 100% {
                transform: scale(1);
                opacity: 0.7;
            }
            30% {
                transform: scale(1.2);
                opacity: 1;
            }
        }
        .welcome-message {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 16px 0;
            font-size: 0.875rem;
        }
        @media (max-width: 640px) {
            .chatbot-container {
                margin: 8px;
                min-height: 40vh;
            }
            .chat-messages {
                min-height: 150px;
            }
            .message-content {
                max-width: 90%;
                font-size: 0.75rem;
                padding: 8px;
            }
            .button-grid {
                grid-template-columns: 1fr;
                gap: 6px;
            }
            .query-button {
                padding: 8px 10px;
                font-size: 0.75rem;
            }
            .chatbot-header h2 {
                font-size: 1rem;
            }
            .chatbot-header p {
                font-size: 0.625rem;
            }
            .message-avatar {
                width: 28px;
                height: 28px;
                font-size: 12px;
            }
            .input-toggle {
                font-size: 0.75rem;
            }
            .message-input {
                font-size: 0.75rem;
            }
            .send-button {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
        }
        @media (min-width: 1024px) {
            .chatbot-container {
                min-height: 60vh;
                max-width: 900px;
            }
            .chat-messages {
                min-height: 400px;
            }
            .chatbot-header h2 {
                font-size: 1.5rem;
            }
            .chatbot-header p {
                font-size: 0.875rem;
            }
            .message-content {
                font-size: 1rem;
                padding: 16px;
            }
            .query-button {
                font-size: 0.875rem;
                padding: 12px 16px;
            }
            .message-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            .input-toggle {
                font-size: 0.875rem;
            }
            .message-input {
                font-size: 1rem;
            }
            .send-button {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="fixed w-full bg-gray-300 shadow-md z-50 mb-36">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="index.php">
                        <img class="h-10 w-auto rounded-full" src="https://media.istockphoto.com/id/1045368942/vector/abstract-green-leaf-logo-icon-vector-design-ecology-icon-set-eco-icon.jpg?s=612x612&w=0&k=20&c=XIfHMI8r1G73blCpCBFmLIxCtOLx8qX0O3mZC9csRLs=" alt="Plant Logo" loading="lazy">
                    </a>
                </div>

                <!-- for Mobile -->
                <button id="menu-toggle" class="md:hidden text-2xl focus:outline-none">
                    ☰
                </button>

                <!-- NavBar -->
                <ul id="nav-menu" class="hidden md:flex items-center space-x-8">
                    <li><a href="index.php" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-2 py-2">Home</a></li>
                    <li><a href="index.php#services" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-2 py-2">Services</a></li>
                    <li><a href="index.php#garden" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-2 py-2">Gardens</a></li>
                    <!-- Dropdown Area-->
                    <li class="relative group">
                        <a href="#" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">About Us</a>
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md mt-2 min-w-[160px]">
                            <a href="who_we_are.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100">Who We Are</a>
                            <a href="Developers.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100">About Developers</a>
                            <a href="Contact.php" class="block px-3 py-2 text-gray-700 hover:bg-gray-100">Contact us</a>
                        </div>
                    </li>
                    <li class="relative group">
                        <a href="Shop.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                        </a>
                    </li>
                    <li class="relative group">
                        <?php if (isset($_COOKIE['name'])): ?>
                            <a href="Profile.php" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-300">
                                <?php echo htmlspecialchars($_COOKIE['name']); ?>
                            </a>
                            <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md mt-2 min-w-[160px]">
                                <!-- <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a> -->
                                <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-300">Login</a>
                        <?php endif; ?>
                    
                    </li>
                </ul>
            </div>

            <!-- Mobile Menu (hidden by default) -->
            <div id="mobile-menu" class="hidden md:hidden bg-gray-100 shadow-md">
                <ul class="px-2 pt-2 pb-3 space-y-1">
                    <li><a href="index.php#home" class="block text-gray-700 font-medium hover:text-green-500 px-3 py-2">Home</a></li>
                    <li><a href="index.php#services" class="block text-gray-700 font-medium hover:text-green-500 px-3 py-2">Services</a></li>
                    <li><a href="index.php#garden" class="block text-gray-700 font-medium hover:text-green-500 px-3 py-2">Gardens</a></li>
                    <li>
                        <a href="#" class="block text-gray-700 font-medium hover:text-green-500 px-3 py-2">About Us</a>
                        <div class="pl-4">
                            <a href="who_we_are.php" class="block text-gray-700 hover:bg-gray-100 px-3 py-2">Who We Are</a>
                            <a href="Developers.php" class="block text-gray-700 hover:bg-gray-100 px-3 py-2">About Developers</a>
                            <a href="Contact.php" class="block text-gray-700 hover:bg-gray-100 px-3 py-2">Contact Us</a>
                        </div>
                    </li>
                    <li class="relative group">
                        <a href="Shop.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <?php if (isset($_SESSION['name'])): ?>
                            <a href="Profile.php" class="block bg-green-500 text-white px-4 py-2 rounded-md text-center hover:bg-green-600 transition-colors duration-300">
                                <?php echo htmlspecialchars($_SESSION['name']); ?>
                            </a>
                            <div class="pl-4">
                                <!-- <a href="profile.php" class="block text-gray-700 hover:bg-gray-100 px-3 py-2">Profile</a> -->
                                <a href="logout.php" class="block text-gray-700 hover:bg-gray-100 px-3 py-2">Logout</a>
                            </div>
                        <?php else: ?>
                            <a href="login.php" class="block bg-green-500 text-white px-4 py-2 rounded-md text-center hover:bg-green-600 transition-colors duration-300">Login</a>
                        <?php endif; ?>
                        

                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Spacer -->
    <div class="h-16 sm:h-20"></div>

    <!-- Contact Section -->
    <section id="contact" class="px-4 py-8 sm:px-6 sm:py-12 lg:px-8 lg:py-16 flex-grow">
        <!-- User Exists Alert -->
        <?php if ($user): ?>
            <div id="user-alert" class="custom-alert w-[90%] sm:max-w-2xl lg:max-w-3xl mx-auto bg-red-100 border-l-4 border-red-500 text-red-700 p-3 sm:p-4 mb-6 rounded-lg shadow-md flex justify-between items-center">
                <div class="text-sm sm:text-base lg:text-lg">
                    <strong>Oops!</strong> Organization already exists.
                </div>
                <button onclick="closeAlert('user-alert')" class="text-red-700 hover:text-red-900 focus:outline-none">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <!-- Success Alert -->
        <?php if ($success): ?>
            <div id="success-alert" class="custom-alert w-[90%] sm:max-w-2xl lg:max-w-3xl mx-auto bg-green-100 border-l-4 border-green-500 text-green-700 p-3 sm:p-4 mb-6 rounded-lg shadow-md flex justify-between items-center">
                <div class="text-sm sm:text-base lg:text-lg">
                    <strong>Success!</strong> Details sent successfully. We will contact you ASAP.
                </div>
                <button onclick="closeAlert('success-alert')" class="text-green-700 hover:text-green-900 focus:outline-none">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        <?php endif; ?>

        <h1 class="text-center text-green-600 font-bold text-2xl sm:text-3xl lg:text-4xl capitalize mb-8 sm:mb-10 lg:mb-12">Ask Your Queries</h1>

        <!-- ChatBot -->
        <div class="flex justify-center mb-8 sm:mb-12 lg:mb-16">
            <div class="chatbot-container">
                <div class="chatbot-header">
                    <h2>Plant Hub Assistant</h2>
                    <p>Your friendly gardening companion</p>
                </div>
                <div class="chat-messages" id="chatMessages">
                    <div class="welcome-message">
                        Welcome to Plant Hub! Ask me anything about plants, seeds, or our services.
                    </div>
                </div>
                <div class="typing-indicator" id="typingIndicator">
                    <div class="message-avatar bot-avatar">P</div>
                    <div class="message-content">
                        <div class="typing-dots">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>
                </div>
                <div class="input-container">
                    <div class="button-grid">
                        <button class="query-button" data-query="how to add organization">Add Organization</button>
                        <button class="query-button" data-query="how to grow seeds indoor">Grow Seeds Indoor</button>
                        <button class="query-button" data-query="can I order without login">Order Without Login</button>
                        <button class="query-button" data-query="plant care tips">Plant Care Tips</button>
                        <button class="query-button" data-query="what seeds do you have">Available Seeds</button>
                    </div>
                    <div class="flex justify-end mt-2 mb-2">
                        <button id="clearChatButton" class="bg-red-100 hover:bg-red-200 text-red-600 font-semibold px-4 py-1 rounded-md text-xs border border-red-200 transition-all duration-200">Clear Chat</button>
                    </div>
                    <div class="custom-input-section">
                        <button class="input-toggle" id="inputToggle">
                            Ask custom question
                            <span id="toggleIcon">▼</span>
                        </button>
                        <div class="input-wrapper" id="customInput">
                            <input
                                type="text"
                                class="message-input"
                                id="messageInput"
                                placeholder="Type your question here..."
                                autocomplete="off"
                            />
                            <button class="send-button" id="sendButton">➤</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form and Image -->
        <div class="container mx-auto flex flex-col md:flex-row gap-8 sm:gap-12 lg:gap-16 items-center justify-center">
            <!-- Image -->
            <div class="w-full md:w-1/3 flex justify-center">
                <img src="png7.png" alt="Contact" class="w-full max-w-[200px] sm:max-w-xs lg:max-w-sm h-auto rounded-3xl">
            </div>
            <!-- Form -->
            <div class="w-full md:w-2/3 flex flex-col items-center">
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-emerald-400 mb-6 text-center">Join Our Community</h1>
                <form action="Contact.php" method="post" class="w-full max-w-md lg:max-w-lg bg-white p-4 sm:p-6 lg:p-8 rounded-xl space-y-4 sm:space-y-6">
                    <!-- Organization Name -->
                    <div>
                        <label class="block text-sm sm:text-base lg:text-lg font-medium mb-1 sm:mb-2">Organization Name</label>
                        <input type="text" name="orgname" placeholder="Enter Organization Name" required autocomplete="off"
                               class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 text-sm sm:text-base">
                    </div>
                    <!-- Location -->
                    <div>
                        <label class="block text-sm sm:text-base lg:text-lg font-medium mb-1 sm:mb-2">Location</label>
                        <input type="text" name="location" placeholder="Enter Location" required
                               class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 text-sm sm:text-base">
                    </div>
                    <!-- Email -->
                    <div>
                        <label class="block text-sm sm:text-base lg:text-lg font-medium mb-1 sm:mb-2">Email</label>
                        <input type="email" name="email" placeholder="Enter Email Address" required
                               class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 text-sm sm:text-base">
                    </div>
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm sm:text-base lg:text-lg font-medium mb-1 sm:mb-2">Phone Number</label>
                        <input type="tel" name="phone" placeholder="Enter Phone Number" required pattern="\d{10}"
                               class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 text-sm sm:text-base">
                    </div>
                    <!-- Message -->
                    <div>
                        <label class="block text-sm sm:text-base lg:text-lg font-medium mb-1 sm:mb-2">Message</label>
                        <textarea name="message" placeholder="Enter your message" rows="4"
                               class="w-full border border-gray-300 px-3 py-2 sm:px-4 sm:py-3 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400 text-sm sm:text-base"></textarea>
                    </div>
                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="w-full bg-green-500 text-white text-sm sm:text-base lg:text-lg font-semibold py-2 sm:py-3 rounded-md hover:bg-green-600 transition-all duration-300">
                            Join Community
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-300 mt-8 sm:mt-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-10">
            <!-- Logo & Description -->
            <div>
                <a href="index.php" class="flex items-center space-x-3 mb-4">
                    <img src="https://media.istockphoto.com/id/1045368942/vector/abstract-green-leaf-logo-icon-vector-design-ecology-icon-set-eco-icon.jpg?s=612x612&w=0&k=20&c=XIfHMI8r1G73blCpCBFmLIxCtOLx8qX0O3mZC9csRLs=" alt="Plant-Hub" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full">
                    <span class="text-xl sm:text-2xl font-bold text-emerald-700">Plant-Hub</span>
                </a>
                <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">
                    Your trusted companion in cultivating a green lifestyle 🌱. Join our plant-loving community and grow together.
                </p>
            </div>
            <!-- Navigation Links -->
            <div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3 sm:mb-4">Quick Links</h3>
                <ul class="space-y-2 text-xs sm:text-sm">
                    <li><a href="index.php#home" class="text-gray-600 hover:text-emerald-600 transition">Home</a></li>
                    <li><a href="index.php#services" class="text-gray-600 hover:text-emerald-600 transition">Services</a></li>
                    <li><a href="index.php#garden" class="text-gray-600 hover:text-emerald-600 transition">Gardens</a></li>
                    <li><a href="who_we_are.php" class="text-gray-600 hover:text-emerald-600 transition">Who We Are</a></li>
                    <li><a href="Developers.php" class="text-gray-600 hover:text-emerald-600 transition">About Developers</a></li>
                    <li><a href="Contact.php" class="text-gray-600 hover:text-emerald-600 transition">Contact Us</a></li>
                    <li><a href="Shop.php" class="text-gray-600 hover:text-emerald-600 transition">Shop</a></li>

                </ul>
            </div>
            <!-- Social Media -->
            <div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-3 sm:mb-4">Connect With Us</h3>
                <div class="flex space-x-3 sm:space-x-6">
                    <a href="https://www.instagram.com" target="_blank" class="text-gray-600 hover:text-pink-500 transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M7.75 2C4.022 2 2 4.021 2 7.75v8.5C2 19.979 4.021 22 7.75 22h8.5C19.979 22 22 19.979 22 16.25v-8.5C22 4.021 19.979 2 16.25 2h-8.5zm0 1.5h8.5C18.216 3.5 20.5 5.784 20.5 8.25v7.5c0 2.466-2.284 4.75-4.75 4.75h-8.5C5.784 20.5 3.5 18.216 3.5 15.75v-7.5C3.5 5.784 5.784 3.5 7.75 3.5zm8.25 2a1 1 0 100 2 1 1 0 000-2zM12 7.25a4.75 4.75 0 110 9.5 4.75 4.75 0 010-9.5zm0 1.5a3.25 3.25 0 100 6.5 3.25 3.25 0 000-6.5z"/>
                        </svg>
                    </a>
                    <a href="https://www.facebook.com" target="_blank" class="text-gray-600 hover:text-blue-600 transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 2.05v3.45h2.4l-.35 2.7H13V12h2.65l-.4 2.7H13v7.25h-3.1V14.7H8.5v-2.7h1.4v-2.2c0-2.1 1.05-3.5 3.6-3.5h2.5z"/>
                        </svg>
                    </a>
                    <a href="https://github.com/Harsh-Verma/Plant-Hub" target="_blank" class="text-gray-600 hover:text-gray-900 transition">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 .297a12 12 0 00-3.793 23.4c.6.113.82-.26.82-.577v-2.234c-3.338.726-4.042-1.615-4.042-1.615-.547-1.386-1.336-1.756-1.336-1.756-1.093-.748.083-.733.083-.733 1.21.085 1.847 1.243 1.847 1.243 1.07 1.834 2.807 1.304 3.492.996.108-.775.42-1.304.764-1.604-2.665-.304-5.467-1.333-5.467-5.93 0-1.31.467-2.38 1.235-3.22-.124-.303-.535-1.523.117-3.176 0 0 1.008-.322 3.3 1.23a11.52 11.52 0 016 0c2.29-1.552 3.296-1.23 3.296-1.23.653 1.653.242 2.873.12 3.176.77.84 1.233 1.91 1.233 3.22 0 4.61-2.807 5.624-5.48 5.92.43.37.823 1.102.823 2.222v3.293c0 .32.218.694.825.576A12.003 12.003 0 0012 .297z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <!-- Bottom Bar -->
        <div class="bg-gray-200 text-center py-4">
            <p class="text-xs sm:text-sm text-gray-600">© <?= date("Y") ?> Plant-Hub. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript for Mobile Menu Toggle and Alert Close -->
    <script>
        // Mobile Menu Toggle
        document.getElementById('menu-toggle').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Function to Close Alerts
        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            alert.classList.add('hidden');
        }

        // Chatbot JavaScript
        class PlantHubChatbot {
            constructor() {
                this.messages = [];
                this.chatMessages = document.getElementById('chatMessages');
                this.messageInput = document.getElementById('messageInput');
                this.sendButton = document.getElementById('sendButton');
                this.typingIndicator = document.getElementById('typingIndicator');
                this.clearChatButton = document.getElementById('clearChatButton');
                this.knowledgeBase = {
                    greetings: {
                        patterns: ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'],
                        responses: [
                            "Hello there! Welcome to Plant Hub! I'm here to help you with all your plant and gardening needs. What would you like to know?",
                            "Hi! Great to see you at Plant Hub! Whether you're looking for seeds, plant care tips, or want to know about our services, I'm here to help!",
                            "Hey! Welcome to your friendly Plant Hub assistant! I can help you with plant care, seed growing, orders, and more. What's on your mind?"
                        ]
                    },
                    organization: {
                        patterns: ['organization', 'add organization', 'become organization', 'partner', 'business'],
                        response: "To add your organization to Plant Hub, please go to the 'About Us' dropdown menu and click on the 'Contact' button. Fill out the form with your organization details and send it to us. We'll get back to you soon!"
                    },
                    indoorSeeds: {
                        patterns: ['grow seed indoor', 'indoor growing', 'seeds indoor', 'indoor gardening', 'grow inside'],
                        response: "Growing seeds indoors is a great way to start your garden! Here are the key steps:\n\n1. Choose the right seeds - Start with easy varieties like herbs, lettuce, or tomatoes\n2. Use quality potting soil - Well-draining, nutrient-rich soil is essential\n3. Provide adequate light - Place near a sunny window or use grow lights\n4. Maintain proper moisture - Keep soil consistently moist but not waterlogged\n5. Control temperature - Most seeds germinate best at 65-75°F\n6. Ensure good air circulation - This prevents mold and strengthens plants\n\nWould you like specific advice for any particular type of seed?"
                    },
                    orderWithoutLogin: {
                        patterns: ['order without login', 'no login order', 'guest order', 'order without account'],
                        response: "Yes, you can definitely place an order without logging in! You'll need to fill in the required details during checkout (name, address, payment info), but we do recommend creating an account to enjoy a better user experience with order tracking, faster checkout, and personalized recommendations"
                    },
                    plantCare: {
                        patterns: ['plant care', 'care tips', 'how to care', 'plant health', 'watering'],
                        response: "Here are essential plant care tips!\n\nWatering: Check soil moisture - most plants prefer soil that's slightly dry between waterings\nLight: Match your plant's light needs - some love bright sun, others prefer shade\nSoil: Use well-draining potting mix appropriate for your plant type\nFeeding: Feed during growing season with appropriate fertilizer\nPruning: Remove dead or yellowing leaves to promote healthy growth\n\nWhat specific plant are you caring for? I can give more targeted advice!"
                    },
                    seeds: {
                        patterns: ['seeds', 'seed types', 'what seeds', 'buy seeds'],
                        response: "We have a wonderful variety of seeds at Plant Hub! You can check our seeds section to meet with your requirements. Our collection includes:\n\n• Vegetable seeds - Tomatoes, peppers, lettuce, carrots, and more\n• Herb seeds - Basil, cilantro, parsley, mint, and other aromatics\n• Flower seeds - Sunflowers, marigolds, zinnias, and beautiful blooms\n• Fruit seeds - Strawberries, melons, and other delicious options\n\nAll our seeds are high-quality and tested for germination. What type of plants are you interested in growing?"
                    }
                };
                this.initEventListeners();
                this.showWelcomeMessage();
            }
            initEventListeners() {
                this.sendButton.addEventListener('click', () => this.handleSendMessage());
                this.messageInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.handleSendMessage();
                    }
                });
                const queryButtons = document.querySelectorAll('.query-button');
                queryButtons.forEach(button => {
                    button.addEventListener('click', () => {
                        const query = button.dataset.query;
                        this.handleButtonClick(query);
                    });
                });
                const inputToggle = document.getElementById('inputToggle');
                const customInput = document.getElementById('customInput');
                const toggleIcon = document.getElementById('toggleIcon');
                inputToggle.addEventListener('click', () => {
                    const isActive = customInput.classList.contains('active');
                    if (isActive) {
                        customInput.classList.remove('active');
                        toggleIcon.textContent = '▼';
                    } else {
                        customInput.classList.add('active');
                        toggleIcon.textContent = '▲';
                        this.messageInput.focus();
                    }
                });
                this.clearChatButton.addEventListener('click', () => this.clearChat());
            }
            clearChat() {
                // Remove all messages except the welcome message
                this.chatMessages.innerHTML = '<div class="welcome-message">Welcome to Plant Hub! Ask me anything about plants, seeds, or our services.</div>';
            }
            handleButtonClick(query) {
                this.addUserMessage(query);
                setTimeout(() => {
                    this.showTyping();
                    setTimeout(() => {
                        this.hideTyping();
                        this.processMessage(query);
                    }, 1500);
                }, 500);
            }
            showWelcomeMessage() {
                setTimeout(() => {
                    this.addBotMessage("Hello! I'm your Plant Hub assistant. I'm here to help you with plant care, seed growing, orders, and any questions about our services. Choose a quick option below or ask me a custom question!");
                }, 1000);
            }
            handleSendMessage() {
                const message = this.messageInput.value.trim();
                if (!message) return;
                this.addUserMessage(message);
                this.messageInput.value = '';
                setTimeout(() => {
                    this.showTyping();
                    setTimeout(() => {
                        this.hideTyping();
                        this.processMessage(message);
                    }, 1500);
                }, 500);
            }
            addUserMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message user';
                messageDiv.innerHTML = `
                    <div class="message-avatar user-avatar">U</div>
                    <div class="message-content">${message}</div>
                `;
                this.chatMessages.appendChild(messageDiv);
                this.scrollToBottom();
            }
            addBotMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message';
                messageDiv.innerHTML = `
                    <div class="message-avatar bot-avatar">P</div>
                    <div class="message-content">${message}</div>
                `;
                this.chatMessages.appendChild(messageDiv);
                this.scrollToBottom();
            }
            showTyping() {
                this.typingIndicator.classList.add('show');
                this.scrollToBottom();
            }
            hideTyping() {
                this.typingIndicator.classList.remove('show');
            }
            processMessage(userMessage) {
                const lowercaseMessage = userMessage.toLowerCase();
                let response = null;
                if (this.knowledgeBase.greetings.patterns.some(pattern => lowercaseMessage.includes(pattern))) {
                    const responses = this.knowledgeBase.greetings.responses;
                    response = responses[Math.floor(Math.random() * responses.length)];
                } else if (this.knowledgeBase.organization.patterns.some(pattern => lowercaseMessage.includes(pattern))) {
                    response = this.knowledgeBase.organization.response;
                } else if (this.knowledgeBase.indoorSeeds.patterns.some(pattern => lowercaseMessage.includes(pattern))) {
                    response = this.knowledgeBase.indoorSeeds.response;
                } else if (this.knowledgeBase.orderWithoutLogin.patterns.some(pattern => lowercaseMessage.includes(pattern))) {
                    response = this.knowledgeBase.orderWithoutLogin.response;
                } else if (this.knowledgeBase.plantCare.patterns.some(pattern => lowercaseMessage.includes(pattern))) {
                    response = this.knowledgeBase.plantCare.response;
                } else if (this.knowledgeBase.seeds.patterns.some(pattern => lowercaseMessage.includes(pattern))) {
                    response = this.knowledgeBase.seeds.response;
                } else {
                    const defaultResponses = [
                        "Thank you for your question! This seems to be outside our standard Plant Hub services. Please wait 24 hours and we will reach out to you soon with a detailed response.",
                        "I appreciate your inquiry! This appears to be a specialized question. Please wait 24 hours and our team will reach out to you soon with the information you need.",
                        "Thanks for reaching out! This question requires special attention from our team. Please wait 24 hours and we will contact you soon with a comprehensive answer."
                    ];
                    response = defaultResponses[Math.floor(Math.random() * defaultResponses.length)];
                }
                this.addBotMessage(response);
            }
            scrollToBottom() {
                this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            new PlantHubChatbot();
        });
    </script>
</body>
</html>
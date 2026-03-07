<?php 
session_start();
if(!isset($_SESSION['email']) && isset($_COOKIE['email'])) {
    $_SESSION['name'] = $_COOKIE['name'];
    $_SESSION['email'] = $_COOKIE['email'];
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="plant.png" type="image/png">
    <title>Plant-Hub</title>
    <style>
      html {
        scroll-behavior: smooth;
      }
        /* Body background in regular CSS */

        /*
        body {
            background: transparent;
            width: 100%;
            height: 100%;
            --color: rgba(114, 114, 114, 0.3);
            background-color: #a9d37c;
            background-image: linear-gradient(0deg, transparent 24%, var(--color) 25%, var(--color) 26%, transparent 27%, transparent 74%, var(--color) 75%, var(--color) 76%, transparent 77%, transparent),
                linear-gradient(90deg, transparent 24%, var(--color) 25%, var(--color) 26%, transparent 27%, transparent 74%, var(--color) 75%, var(--color) 76%, transparent 77%, transparent);
            background-size: 55px 55px;
            margin: 0;
            padding: 0;
        }*/
    </style>
</head>
<body>
    <nav class="fixed w-full bg-gray-300 shadow-md z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="index.php">
                        <img class="h-10 w-auto rounded-full" src="https://media.istockphoto.com/id/1045368942/vector/abstract-green-leaf-logo-icon-vector-design-ecology-icon-set-eco-icon.jpg?s=612x612&w=0&k=20&c=XIfHMI8r1G73blCpCBFmLIxCtOLx8qX0O3mZC9csRLs=" alt="Plant Logo" loading="lazy">
                    </a>
                </div>
    
                <!-- Menu Toggle for Mobile -->
                <button id="menu-toggle" class="md:hidden text-2xl focus:outline-none">
                    ☰
                </button>
    
                <!-- Navigation Menu -->
                <ul id="nav-menu" class="hidden md:flex items-center space-x-8">
                    <li><a href="index.php#home" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">Home</a></li>
                    <li><a href="index.php#services" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">Services</a></li>
                    <li><a href="index.php#garden" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">Gardens</a></li>
                    <!-- Dropdown -->
                    <li class="relative group">
                        <a href="#" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">About Us</a>
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md mt-2 min-w-[160px]">
                            <a href="who_we_are.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Who We Are</a>
                            <a href="Developers.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">About Developers</a>
                            <a href="Contact.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Contact us</a>
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
                    <li class="relative group">
                    <?php if (isset($_SESSION['name'])): ?>
                        <a href="Profile.php" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-300">
                            <?php echo htmlspecialchars($_SESSION['name']); ?>
                        </a>
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md mt-2 min-w-[160px]">
                            <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-300">Login</a>
                    <?php endif; ?>
                </li>
                </ul>
            </div>
    </div>
</nav>
    
    <!-- JavaScript for mobile menu toggle -->
<script>
        document.getElementById('menu-toggle').addEventListener('click', function () {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
</script>

<div class="h-20"></div><!-- To remove the space above the navBar -->

<section class="py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
      
      <!-- Left side: Developer boxes -->
      <div class="space-y-6">
        <!-- Title Box -->
        <div class="border-8 border-green-700 rounded-3xl p-4 text-center">
          <h1 class="text-3xl sm:text-4xl font-bold text-green-800">About Developers</h1>
        </div>
  
        <!-- Name + Roles Box -->
        <div class="border-2 border-green-700 rounded-3xl p-4 sm:p-6 flex flex-col sm:flex-row justify-between">
          <!-- Developer List -->
          <div>
              <h2 class="text-green-900 font-bold text-center text-lg sm:text-xl">DEVELOPERS</h2>
            <ol class="list-decimal pl-5 space-y-1 text-green-800">
              <li class="text-lg sm:text-xl font-medium"><a href="#" class="hover:underline hover:text-black">Harsh Verma</a></li>
              <li class="text-lg sm:text-xl font-medium"><a href="#" class="hover:underline hover:text-black">Rohit Kumar</a></li>
              <li class="text-lg sm:text-xl font-medium"><a href="#" class="hover:underline hover:text-black">Shobhit Jindakur</a></li>
              <li class="text-lg sm:text-xl font-medium"><a href="#" class="hover:underline hover:text-black">M.Shokin</a></li>
            </ol>
          </div>
  
          <!-- Roles -->
          <div class="mt-6 sm:mt-0 sm:ml-6 space-y-1 text-green-800 p-4">
            <p class="text-lg sm:text-xl font-bold">1. Shop and Checkout pages.</p>
            <p class="text-lg sm:text-xl font-bold">2. Homepage or Dashborad of the website.</p>
            <p class="text-lg sm:text-xl font-bold">3. Contact page to add orgainzations.</p>
            <p class="text-lg sm:text-xl font-bold">4. Who we are and developers page to tell about us more.</p>
          </div>
        </div>
      </div>
  
      <!-- Right side: Illustration -->
      <div class="flex justify-center">
        <img src="png6.png" alt="Developer Illustration" class="w-full max-w-md h-auto object-contain">
      </div>
  
    </div>
</section>
  
<!-- Footer of the WebPage -->
<footer class="bg-gray-300 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
      <div>
        <a href="index.php" class="flex items-center space-x-3 mb-4">
          <img src="https://media.istockphoto.com/id/1045368942/vector/abstract-green-leaf-logo-icon-vector-design-ecology-icon-set-eco-icon.jpg?s=612x612&w=0&k=20&c=XIfHMI8r1G73blCpCBFmLIxCtOLx8qX0O3mZC9csRLs=" alt="Plant-Hub" class="w-10 h-10 rounded-full">
          <span class="text-xl sm:text-2xl font-bold text-emerald-700">Plant-Hub</span>
        </a>
        <p class="text-gray-600 text-sm leading-relaxed">
          Your trusted companion in cultivating a green lifestyle 🌱. Join our plant-loving community and grow together.
        </p>
      </div>
  
      <div>
        <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Quick Links</h3>
        <ul class="space-y-2">
          <li><a href="index.php#home" class="text-gray-600 hover:text-emerald-600 transition">Home</a></li>
          <li><a href="index.php#services" class="text-gray-600 hover:text-emerald-600 transition">Services</a></li>
          <li><a href="index.php#garden" class="text-gray-600 hover:text-emerald-600 transition">Gardens</a></li>
          <li><a href="who_we_are.php" class="text-gray-600 hover:text-emerald-600 transition">Who We Are</a></li>
          <li><a href="Developers.php" class="text-gray-600 hover:text-emerald-600 transition">About Developers</a></li>
          <li><a href="Contact.php" class="text-gray-600 hover:text-emerald-600 transition">Contact Us</a></li>
          <li><a href="Shop.php" class="text-gray-600 hover:text-emerald-600 transition">Shop</a></li>
        </ul>
      </div>
  
      <div>
        <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Connect With Us</h3>
        <div class="flex space-x-4">
          <a href="https://www.instagram.com" target="_blank" class="text-gray-500 hover:text-pink-500 transition">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M7.75 2C4.022 2 2 4.021 2 7.75v8.5C2 19.979 4.021 22 7.75 22h8.5C19.979 22 22 19.979 22 16.25v-8.5C22 4.021 19.979 2 16.25 2h-8.5zm0 1.5h8.5C18.216 3.5 20.5 5.784 20.5 8.25v7.5c0 2.466-2.284 4.75-4.75 4.75h-8.5C5.784 20.5 3.5 18.216 3.5 15.75v-7.5C3.5 5.784 5.784 3.5 7.75 3.5zm8.25 2a1 1 0 100 2 1 1 0 000-2zM12 7.25a4.75 4.75 0 110 9.5 4.75 4.75 0 010-9.5zm0 1.5a3.25 3.25 0 100 6.5 3.25 3.25 0 000-6.5z"/>
            </svg>
          </a>
          <a href="https://www.facebook.com" target="_blank" class="text-gray-500 hover:text-blue-600 transition">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M13 2.05v3.45h2.4l-.35 2.7H13V12h2.65l-.4 2.7H13v7.25h-3.1V14.7H8.5v-2.7h1.4v-2.2c0-2.1 1.05-3.5 3.6-3.5h2.5z"/>
            </svg>
          </a>
          <a href="https://github.com/Harsh-Verma1981/Plant-Hub" target="_blank" class="text-gray-500 hover:text-gray-900 transition">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M12 .297a12 12 0 00-3.793 23.4c.6.113.82-.26.82-.577v-2.234c-3.338.726-4.042-1.615-4.042-1.615-.547-1.386-1.336-1.756-1.336-1.756-1.093-.748.083-.733.083-.733 1.21.085 1.847 1.243 1.847 1.243 1.07 1.834 2.807 1.304 3.492.996.108-.775.42-1.304.764-1.604-2.665-.304-5.467-1.333-5.467-5.93 0-1.31.467-2.38 1.235-3.22-.124-.303-.535-1.523.117-3.176 0 0 1.008-.322 3.3 1.23a11.52 11.52 0 016 0c2.29-1.552 3.296-1.23 3.296-1.23.653 1.653.242 2.873.12 3.176.77.84 1.233 1.91 1.233 3.22 0 4.61-2.807 5.624-5.48 5.92.43.37.823 1.102.823 2.222v3.293c0 .32.218.694.825.576A12.003 12.003 0 0012 .297z"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
    <!-- Bottom Bar -->
    <div class="bg-gray-200 text-center py-4">
      <p class="text-sm text-gray-600">© <?= date("Y") ?> Plant-Hub. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
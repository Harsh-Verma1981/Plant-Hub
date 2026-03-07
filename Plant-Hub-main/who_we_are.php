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
    <link rel="icon" href="plant.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="output.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Plant-Hub</title>
    <style>
        html {
            scroll-behavior: smooth;
        }
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
        @keyframes fadeInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes fadeInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .animate-fade-in-left {
            animation: fadeInLeft 1s ease-out forwards;
        }

        .animate-fade-in-right {
            animation: fadeInRight 1s ease-out forwards;
        }

        /* This is an example, feel free to delete this code */
        .tooltip-container {
            /* --background: #22d3ee; */
            position: relative;
            background: var(--background);
            cursor: pointer;
            transition: all 0.2s;
            font-size: 17px;
            padding: 0.7em 1.8em;
            margin-bottom: -100px;
        }
        
        .bg {
            position: absolute;
            left: -30px;
            top: 0px;
            z-index: -2;
        }
        
        .tooltip {
            position: absolute;
            top: 0;
            left: 50%;
            width: 180px;
            height: 100px;
            transform: translateX(-50%);
            padding: 0.5em 1em;
            opacity: 0;
            pointer-events: none;
            transition: all 0.3s;
            background: url("data:image/jpeg;base64,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");
            border-radius: 30px;
            background-size: cover;
            box-shadow: inset 0px 8px 9px #182218, 5px 8px 0px #314328;
            filter: drop-shadow(60px 60px 6px #18221888);
            border: 10px solid #3f5641ee;
            z-index: -1;
            animation: float 2s infinite;
        }
        
        .tooltip::before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            bottom: -22px;
            left: 50%;
            transform: translate(-50%) rotate(45deg);
            background: #e9f6f4;
            border: 10px solid #3f5641ee;
            border-left: none;
            border-top: none;
            border-radius: 5px;
            box-shadow: 10px 4px 0px #314328;
        }
        
        .tooltip-container:hover .tooltip {
            top: -120px;
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        
        .leaf1 {
            top: -30px;
            left: -60px;
        }
        .leaf2 {
            top: -30px;
            left: -60px;
        }
        .leaf3 {
            top: -30px;
            left: -60px;
        }
        .leaf4 {
            top: -30px;
            left: -60px;
        }
        .tooltip-container:hover .leaf1 {
            rotate: z 20deg;
        }
        /* .tooltip-container:hover .leaf2{
        rotate: z 20deg;
        } */
        .tooltip-container:hover .leaf3 {
            rotate: z -20deg;
        }
        .tooltip-container:hover .leaf4 {
            rotate: z 40deg;
        }
        
        .leaf {
            width: 60px;
            height: 40px;
            position: absolute;
            transform-origin: 100% 100%;
            transition: rotate 0.5s 0.2s linear;
            /* border: px solid #314328; */
            border-radius: 0 100%;
            background-image: linear-gradient(to right, #b4c7b3, #314328);
        }
        .icon {
            /* box-shadow: 60px 60px 6px #18221888,120px 60px 6px #18221888; */
            transform: translateX(-50%);
            filter: drop-shadow(60px 60px 6px #18221888);
        }
        .text {
            z-index: 2;
            font-size: 22px;
            color: #3f5641;
            font-weight: bolder;
            margin: auto;
            width: fit-content;
            padding-top: 25px;
            filter: drop-shadow(0px 0px 3px #fff);
        }
        @keyframes float {
            0% {
            top: -120px;
            }
            50% {
            top: -125px;
            }
            100% {
            top: -120px;
            }
        }

</style>
</head>
<body>

<!-- Navbar -->
<nav class="fixed top-0 left-0 w-full bg-gray-300 shadow-md z-50">
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
                    <a href="" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">About Us</a>
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
                <!-- Login Button -->
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

        <!-- Mobile Menu -->
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
                            <!-- <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a> -->
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

<div class="h-20"></div><!-- Space bar div container -->

<section id="who-we-are" class="py-20 px-4 md:px-20">
    <!-- <div class="flex justify-end items-end">
        <div class="tooltip-container">
            <div class="tooltip">
              <div class="text">Save Trees</div>
          
              <div class="leaf leaf1"></div>
              <div class="leaf leaf2"></div>
              <div class="leaf leaf3"></div>
              <div class="leaf leaf4"></div>
            </div>
            <div class="leaf icon"></div>
        </div>
    </div>     -->
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-10 items-center">
        <!-- Image -->
        <div class="flex justify-center">
            <img src="png5.png" alt="Gardener" class="w-full max-w-sm animate-fade-in-left">
        </div>

        <!-- Text Content -->
        <div class="animate-fade-in-right">
            <h2 class="text-5xl font-extrabold text-emerald-800 mb-6">Who We Are</h2>
            <p class="text-gray-700 text-lg leading-relaxed">
                We at <strong class="text-green-600 text-2xl">Plant-Hub</strong> welcome you to a green and growing community.
                Our platform connects nature lovers and plant enthusiasts, enabling you to
                <span class="text-emerald-600 font-semibold">share knowledge</span>, build communities,
                and get advice on the <span class="text-emerald-600 font-semibold">growth and care of your plants</span>.
                Whether you're a beginner or a plant pro, there's always room to grow here.
            </p>
        </div>
    </div>
    
    <div class="flex justify-center items-center mt-10">
        <button class="bg-green-600 w-64 h-auto p-4 m-4 text-white hover:text-black transition ease-out rounded-3xl mt-6 mb-6 text-2xl font-medium"><a href="index.php#services">Know More</a></button>
    </div>
</section>

<!-- JavaScript -->
<script>
    // Mobile menu toggle
    document.getElementById('menu-toggle').addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Scroll animation
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('opacity-100', 'translate-y-0');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-fade-in-left, .animate-fade-in-right').forEach(el => {
        el.classList.add('opacity-0', 'translate-y-8', 'transition-all', 'duration-700');
        observer.observe(el);
    });
</script>

<!-- Footer of the webpage ..  -->
<footer class="bg-gray-300 mt-12 w-full">
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
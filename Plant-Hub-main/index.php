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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="plant.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Plant-Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-behavior: smooth;
        }
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

        /* Image sliding properties */
        .slider-container {
            width: 100%;
            max-width: 100%;
            margin: auto;
            border: none;
            /*border-radius: 10px;*/
            overflow: hidden;
            position: relative;
        }
        .slider-container img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            display: none;
        }
        .slider-container img.active {
            display: block;
        }
        /* Code for the designing of the developers. */

        header {
            text-align: center;
            padding: 50px 20px 20px 20px;
        }

        header h1 {
            font-size: 3em;
            margin: 0;
            color: #bcb777;
        }

        header p {
            font-size: 1.5em;
            color: #555;
        }

        .Production{
            width: auto;
            height: auto;
            background-color: aliceblue;
        }

        .Production summary strong{
            font-size: 3em;
            color: #555;
            font: bold;
        }

        .Production ul li{
            font-size: 1.6em;
            color: #88ba6f;
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
                <li><a href="#home" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">Home</a></li>
                <li><a href="#services" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">Services</a></li>
                <li><a href="#garden" class="text-gray-700 font-medium hover:text-green-500 transition-colors duration-300 px-3 py-2">Gardens</a></li>
                <!-- Dropdown Area-->
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

            <?php if (isset($_SESSION['name']) || isset($_COOKIE['name'])): ?>

            <a href="Profile.php" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-300">
            <?php echo htmlspecialchars($_SESSION['name'] ?? $_COOKIE['name']); ?>
            </a>

            <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md mt-2 min-w-[160px]">
            <a href="logout.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Logout</a>
            </div>

            <?php else: ?>

            <a href="login.php" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors duration-300">
            Login
            </a>

            <?php endif; ?>

            </li>
            </ul>
        </div>

        <!-- Mobile Menu (hidden by default) -->
        <div id="mobile-menu" class="hidden md:hidden bg-gray-100 shadow-md">
            <ul class="px-2 pt-2 pb-3 space-y-1">
                <li><a href="#home" class="block text-gray-700 font-medium hover:text-green-500 px-3 py-2">Home</a></li>
                <li><a href="#services" class="block text-gray-700 font-medium hover:text-green-500 px-3 py-2">Services</a></li>
                <li><a href="#garden" class="block text-gray-700 font-medium hover:text-green-500 px-3 py-2">Gardens</a></li>
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

<!-- JavaScript for mobile menu toggle -->
<script>
    document.getElementById('menu-toggle').addEventListener('click', function () {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>

<section id="home" class="flex flex-col items-center">
    <div class="flex flex-col md:flex-row items-center justify-center gap-4">
        <div class="w-full md:w-72 h-auto">
            <img src="png1.png" alt="Garden portrait 1" class="w-full h-auto mt-20" loading="lazy">
        </div>
        
        <div class="flex flex-col text-center mt-20 items-center">
            <h1 class="font-extrabold text-4xl md:text-6xl text-emerald-900 animate-bounce">The Benefits of Gardening.</h1>
            <div class="text-center border-4 border-solid rounded-2xl w-72 h-42 text-3xl font-[Times] text-black font-extrabold mt-12 border-black p-4">
                Improving Life with the Magic of Nature
            </div>
        </div>

        <div class="w-full md:w-72 h-auto mt-20">
            <img src="png2.png" alt="Garden portrait 2" class="w-full h-auto" loading="lazy">
        </div>
    </div>
    <!-- images slider -->
    <div class="relative w-full overflow-hidden mt-10">
        <div class="slider-container relative flex mb-2 mt-2">
            <img src="https://plus.unsplash.com/premium_photo-1680286739871-01142bc609df?fm=jpg&q=60&w=3000&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8Z2FyZGVuaW5nfGVufDB8fDB8fHww" class="active w-full h-auto object-cover" alt="Garden 1" loading="lazy">
            <img src="https://static.vecteezy.com/system/resources/previews/024/634/104/non_2x/professional-gardener-with-garden-pruning-and-cutting-tools-photo.jpg" class="w-full h-auto object-cover hidden" alt="Garden 2" loading="lazy">
            <img src="https://cdn.prod.website-files.com/63c4d70094eb39c47c09d55a/67b695c4921270477f1d4574_67b67f35acf635f489c4a255-1740017309182.jpg" class="w-full h-auto object-cover hidden" alt="Garden 3" loading="lazy">
        </div>
    
        <!-- Navigation Buttons -->
        <button id="prev" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-gray-700 text-white px-4 py-2 rounded-full">❮</button>
        <button id="next" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-gray-700 text-white px-4 py-2 rounded-full">❯</button>
    </div>  
    
    <script>
        //image slider logic
        let index = 0;
        const images = document.querySelectorAll('.slider-container img');
        const totalImages = images.length;

        function showImage(i) {
            images.forEach(img => img.classList.remove('active'));
            images[i].classList.add('active');
        }

        function nextImage() {
            index = (index + 1) % totalImages;
            showImage(index);
        }

        function prevImage() {
            index = (index - 1 + totalImages) % totalImages;
            showImage(index);
        }

        document.getElementById('next').addEventListener('click', nextImage);
        document.getElementById('prev').addEventListener('click', prevImage);
        
        setInterval(nextImage, 5000); // Auto-slide every 5 seconds
    </script>
</section>

<!-- Services Section -->
<section id="services" class="mt-0 mb-10 rounded-2xl">
    <div class="flex justify-center text-center items-center">
        <h1 class="w-64 h-24 ml-0 rounded-3xl text-center font-extrabold text-6xl text-emerald-900 p-20 mr-12 mb-4">
            Services
        </h1>
    </div>

    <div class="flex flex-col md:flex-row items-center justify-between">
        <!-- Image on the left -->
        <div class="w-full md:w-1/2 h-auto">
            <img src="png4.png" alt="Grass1" class="rounded-xl object-cover w-full" loading="lazy">
        </div>

        <!-- Text on the right -->
        <div class="w-full md:w-1/2 h-auto rounded-2xl text-2xl p-6 text-left">
            <h1 class="font-bold italic">
                Welcome to <span class="text-green-600">PlantHub</span>, your go-to Community Garden Platform!<br><br>
                At PlantHub, we believe in the power of gardening to bring people together. Whether you're a seasoned gardener or just starting, our platform connects garden enthusiasts, urban farmers, and eco-conscious individuals to share knowledge, resources, and experiences.<br><br>
                <strong>Our Mission:</strong> To foster a sustainable and green community by making gardening accessible to everyone.
            </h1>
        </div>
    </div>

    <div class="flex flex-row items-center">
        <div class="flex-1 p-8">
            <h1 class="font-medium font-[Times] text-3xl text-gray-800 ml-2">Property Managers</h1>
            <p class="text-2xl font-bold text-gray-800">
                Turn-key property managers for private landowners. Year-round property management and maintenance including:
                boulevard maintenance, grass cutting, leaf removal, and more.
            </p>
        </div>

        <div class="w-80 h-72 mr-8">
            <img src="https://www.ugaoo.com/cdn/shop/articles/shutterstock_139759228_eb50c5c7-3293-4358-aac1-03e0ef67a383.jpg?v=1661768042" alt="services1" class="w-full h-full object-cover rounded-lg" loading="lazy">
        </div>
    </div>

    <div class="flex flex-row items-center mt-10">
        <div class="flex-1 p-8">
            <h1 class="font-medium font-[Times] text-3xl text-gray-800 ml-2">Interim Land-use</h1>
            <p class="text-2xl font-bold text-gray-800">
                Our gardens are built in 1-5 days and the average timeline for our projects range from 1-5 years. 
                We have clear rules and agreements with all our gardeners, renewing annually with a 30-day removal clause. 
                We take our projects growing season by growing season. 
            </p>
        </div>

        <div class="w-80 h-72 mr-8">
            <img src="https://images.immediate.co.uk/production/volatile/sites/10/2018/10/2048x1365-No-dig-gardening-Charles_Dowding_JI_160817_CharlesDowding_125-7958889.jpg?resize=768,574" alt="services1" class="w-full h-full object-cover rounded-lg" loading="lazy">
        </div>
    </div>

    <div class="flex flex-row items-center mt-10">
        <div class="flex-1 p-8">
            <h1 class="font-medium font-[Times] text-3xl text-gray-800 ml-2">Dog Garden🐾 </h1>
            <p class="text-2xl font-bold text-gray-800">
                A dog park where dogs run free. 
                We create purpose built parks for dogs to exercise and play off-leash in a controlled environment under the supervision of their owners. 
                Dog owners can let their dogs roam free with peace of mind, while sharing the experience with other people & their pets. 
            </p>
        </div>

        <div class="w-80 h-72 mr-8">
            <img src="https://images.theconversation.com/files/625049/original/file-20241010-15-95v3ha.jpg?ixlib=rb-4.1.0&rect=4%2C12%2C2679%2C1521&q=20&auto=format&w=320&fit=clip&dpr=2&usm=12&cs=strip" alt="services1" class="w-full h-full object-cover rounded-lg" loading="lazy">
        </div>
    </div>
</section>

<section id="garden" class="mt-24">
    <h1 class="text-center font-extrabold text-4xl font-sans text-emerald-800 mt-5">
        <strong>Active Community Garden Projects</strong>
    </h1>

    <div class="flex flex-row flex-wrap gap-6 mt-8 mx-4 justify-between items-stretch">
        <!-- garden card -->
        <div class="flex flex-col w-full md:w-[32%] rounded-2xl shadow-2xl overflow-hidden">
            <h1 class="text-3xl text-gray-800 mt-6 mb-4 font-bold text-center">
                <a href="#" class="hover:text-green-500">Plants Paradise</a>
            </h1>
            <a href="#">
                <img src="https://thumbs.dreamstime.com/b/professional-gardener-finishing-newly-developed-backyard-garden-planting-last-decorative-trees-168604803.jpg" 
                    alt="Plants Paradise" 
                    class="w-full aspect-[4/3] object-cover mb-4" loading="lazy">
            </a>
            <p class="text-black font-medium text-2xl p-4 flex-grow">
                The <strong class="text-3xl text-emerald-600 hover:text-emerald-950 hover:underline">Plants Paradise</strong> is a growing and developing community Nursery for plants located at Old Phagwara Rd, opp. Volkswagon workshop, Jalandhar, Punjab 144024.
            </p>
        </div>

        <div class="flex flex-col w-full md:w-[32%] rounded-2xl shadow-2xl overflow-hidden">
            <h1 class="text-3xl text-gray-800 mt-6 mb-4 font-bold text-center">
                <a href="#" class="hover:text-green-500">Back Garden Nursery</a>
            </h1>
            <a href="#">
                <img src="https://media.istockphoto.com/id/1385759203/photo/soil-with-a-young-plant-planting-seedlings-in-the-ground-there-is-a-spatula-nearby-the.jpg?s=612x612&w=0&k=20&c=gmEFj5-5ZP8PnX74ostHb0G8hzUKSIKR_qxnjpBHHvg=" 
                    alt="Back Garden Nursery" 
                    class="w-full aspect-[4/3] object-cover mb-4" loading="lazy">
            </a>
            <p class="text-black font-medium text-2xl p-4 flex-grow">
                The <strong class="text-3xl text-emerald-600 hover:text-emerald-950 hover:underline">Back Garden Nursery</strong> is one of the best community Garden in the Punjab for plants located at 9 A, opp. Income Tax Colony, Jyoti Nagar, Jalandhar, Punjab 144001.
            </p>
        </div>

        <div class="flex flex-col w-full md:w-[32%] rounded-2xl shadow-2xl overflow-hidden">
            <h1 class="text-3xl text-gray-800 mt-6 mb-4 font-bold text-center">
                <a href="#" class="hover:text-green-500">RK Nursery</a>
            </h1>
            <a href="#">
                <img src="https://img.freepik.com/free-photo/overhead-view-hand-holding-small-fresh-potted-plant_23-2147844319.jpg?semt=ais_hybrid&w=740" 
                    alt="RK Nursery" 
                    class="w-full aspect-[4/3] object-cover mb-4" loading="lazy">
            </a>
            <p class="text-black font-medium text-2xl p-4 flex-grow">
                The <strong class="text-3xl text-emerald-600 hover:text-emerald-950 hover:underline">RK Nursery</strong> is a famous and one of the old Nursery in the city for plants located at Grand Trunk Rd, Goraya, Punjab 144409.
            </p>
        </div>
    </div>

    <script>
        window.addEventListener('load', equalizeCardHeights);
        window.addEventListener('resize', equalizeCardHeights);
    
        function equalizeCardHeights() {
            const cards = document.querySelectorAll('#garden .flex-col');
            let maxHeight = 0;
    
            cards.forEach(card => {
                card.style.height = 'auto'; // Reset height
                if (card.offsetHeight > maxHeight) {
                    maxHeight = card.offsetHeight;
                }
            });
    
            cards.forEach(card => {
                card.style.height = maxHeight + 'px';
            });
        }
    </script>
</section>

<!-- Footer -->
<footer class="bg-gray-300 mt-12">
    <div class="max-w-7xl mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-3 gap-10">
        <!-- Logo & Description -->
        <div>
            <a href="index.php" class="flex items-center space-x-3 mb-4">
                <img src="https://media.istockphoto.com/id/1045368942/vector/abstract-green-leaf-logo-icon-vector-design-ecology-icon-set-eco-icon.jpg?s=612x612&w=0&k=20&c=XIfHMI8r1G73blCpCBFmLIxCtOLx8qX0O3mZC9csRLs=" alt="Plant-Hub" class="w-10 h-10 rounded-full">
                <span class="text-2xl font-bold text-emerald-700">Plant-Hub</span>
            </a>
            <p class="text-gray-600 text-sm leading-relaxed">
                Your trusted companion in cultivating a green lifestyle 🌱. Join our plant-loving community and grow together.
            </p>
        </div>
  
        <!-- Navigation Links -->
        <div>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Quick Links</h3>
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
  
        <!-- Social Media -->
        <div>
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Connect With Us</h3>
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
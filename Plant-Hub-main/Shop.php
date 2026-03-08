<?php
session_start();
if(!isset($_SESSION['email']) && isset($_COOKIE['email'])) {
    $_SESSION['name'] = $_COOKIE['name'];
    $_SESSION['email'] = $_COOKIE['email'];
}

// Function to get cart (prioritize session, fallback to cookie)
function getCart() {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        return $_SESSION['cart'];
    }
    if (isset($_COOKIE['plant_hub_cart'])) {
        return json_decode($_COOKIE['plant_hub_cart'], true) ?: [];
    }
    return [];
}

// Function to save cart to session and cookie
function saveCart($cart) {
    $_SESSION['cart'] = $cart;
    setcookie('plant_hub_cart', json_encode($cart), time() + (86400 * 30), "/"); // 30 days
}

// Initialize/load cart
$cart = getCart();

// Handle adding items to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_name = $_POST['name'];
    $price = (float)$_POST['price'];
    $image = $_POST['image'];

    // Add item to cart (each addition is a new entry)
    $cart[] = [
        'name' => $product_name,
        'price' => $price,
        'image' => $image
    ];
    saveCart($cart);
}

// Handle removing items from cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_from_cart'])) {
    $index = (int)$_POST['index'];
    if (isset($cart[$index])) {
        unset($cart[$index]);
        // Reindex array to avoid gaps
        $cart = array_values($cart);
        saveCart($cart);
    }
}

// Calculate total price
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="plant.png" type="image/png">
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
        }*/
    </style>
</head>
<body>
    <!-- nav bar -->
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

    <!-- To remove the space above the navBar -->
    <div class="h-20"></div>

    <div class="m-2">
        <h1 class="text-center font-bold text-4xl text-gray-600 p-4">View All Items</h1>
    </div>
    
    <!-- Main Content Layout -->
    <div class="flex">
    <!-- Sidebar -->
    <aside class="w-1/3 h-[550px] sticky top-20 bg-gray-100 rounded-2xl p-4 shadow-lg overflow-visible mt-20 ml-4">
        <h2 class="font-medium mb-4 text-green-600 text-xl text-center">Categories</h2>
        <ul class="space-y-3">
            <li><a href="#seeds" class="hover:text-green-500 text-2xl">Seeds</a></li>
            <li><a href="#pots" class="hover:text-green-500 text-2xl">Pots</a></li>
            <li><a href="#indoor" class="hover:text-green-500 text-2xl">Indoor Flowers</a></li>
            <li><a href="#outdoor" class="hover:text-green-500 text-2xl">Outdoor Flowers</a></li>
            <li><a href="#tools" class="hover:text-green-500 text-2xl">Gardening Tools</a></li>
        </ul>
    </aside>

    <!-- Shop Products -->
    <main class="w-3/4 p-6 space-y-12">

        <!-- Seeds Section -->
        <section id="seeds">
        <h2 class="text-4xl font-bold text-green-700 mb-4">Seeds</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://media.istockphoto.com/id/153572197/photo/closeup-of-sunflower-seeds.jpg?s=612x612&w=0&k=20&c=GP4ILfhx-Xlee6oGYhJK4i_lzrjuWdEXnMzItr96IdA=" loading="lazy">
                    <img src="https://media.istockphoto.com/id/153572197/photo/closeup-of-sunflower-seeds.jpg?s=612x612&w=0&k=20&c=GP4ILfhx-Xlee6oGYhJK4i_lzrjuWdEXnMzItr96IdA=" alt="Sunflower Seeds" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Sunflower Seeds</h3>
                <p class="text-sm text-gray-600">Bright flowers for sunlight areas. Rs. 50</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Sunflower Seeds">
                    <input type="hidden" name="price" value="50">
                    <input type="hidden" name="image" value="https://media.istockphoto.com/id/153572197/photo/closeup-of-sunflower-seeds.jpg?s=612x612&w=0&k=20&c=GP4ILfhx-Xlee6oGYhJK4i_lzrjuWdEXnMzItr96IdA=">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://m.media-amazon.com/images/I/51o2Z6HjnSL._UF350,350_QL80_.jpg" loading="lazy">
                    <img src="https://m.media-amazon.com/images/I/51o2Z6HjnSL._UF350,350_QL80_.jpg" alt="Rose Seeds" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Rose Seeds</h3>
                <p class="text-sm text-gray-600">Fragrant roses in multiple colors. Rs. 70</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Rose Seeds">
                    <input type="hidden" name="price" value="70">
                    <input type="hidden" name="image" value="https://m.media-amazon.com/images/I/51o2Z6HjnSL._UF350,350_QL80_.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://4.imimg.com/data4/BK/KR/MY-3705724/jasmine-flower-seeds.jpg" loading="lazy">
                    <img src="https://4.imimg.com/data4/BK/KR/MY-3705724/jasmine-flower-seeds.jpg" alt="Jasmine Seeds" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Jasmine Seeds</h3>
                <p class="text-sm text-gray-600">Aromatic and elegant blooms. Rs. 60</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Jasmine Seeds">
                    <input type="hidden" name="price" value="60">
                    <input type="hidden" name="image" value="https://4.imimg.com/data4/BK/KR/MY-3705724/jasmine-flower-seeds.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
        </div>
        </section>

        <!-- Pots Section -->
        <section id="pots">
        <h2 class="text-4xl font-bold text-green-700 mb-4">Pots</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://5.imimg.com/data5/QO/OH/MY-68749358/colour-roofing-tiles-500x500.jpg" loading="lazy">
                    <img src="https://5.imimg.com/data5/QO/OH/MY-68749358/colour-roofing-tiles-500x500.jpg" alt="Clay Pot" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Clay Pot</h3>
                <p class="text-sm text-gray-600">Natural breathable pot. Rs. 100</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Clay Pot">
                    <input type="hidden" name="price" value="100">
                    <input type="hidden" name="image" value="https://5.imimg.com/data5/QO/OH/MY-68749358/colour-roofing-tiles-500x500.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://images-eu.ssl-images-amazon.com/images/I/81D7GRoQeHL._AC_UL600_SR600,600_.jpg" loading="lazy">
                    <img src="https://images-eu.ssl-images-amazon.com/images/I/81D7GRoQeHL._AC_UL600_SR600,600_.jpg" alt="Plastic Pot" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Plastic Pot</h3>
                <p class="text-sm text-gray-600">Lightweight and durable. Rs. 60</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Plastic Pot">
                    <input type="hidden" name="price" value="60">
                    <input type="hidden" name="image" value="https://images-eu.ssl-images-amazon.com/images/I/81D7GRoQeHL._AC_UL600_SR600,600_.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://5.imimg.com/data5/SELLER/Default/2024/8/446809329/BD/BZ/VC/140709196/download-43-500x500.png" loading="lazy">
                    <img src="https://5.imimg.com/data5/SELLER/Default/2024/8/446809329/BD/BZ/VC/140709196/download-43-500x500.png" alt="Hanging Pot" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Hanging Pot</h3>
                <p class="text-sm text-gray-600">Perfect for balconies. Rs. 80</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Hanging Pot">
                    <input type="hidden" name="price" value="80">
                    <input type="hidden" name="image" value="https://5.imimg.com/data5/SELLER/Default/2024/8/446809329/BD/BZ/VC/140709196/download-43-500x500.png">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
        </div>
        </section>

        <!-- outdoor section -->
        <section id="outdoor">
        <h2 class="text-4xl font-bold text-green-700 mb-4">Outdoor Flowers</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://m.media-amazon.com/images/I/71MkH0TnRyL._UF1000,1000_QL80_.jpg" loading="lazy">
                    <img src="https://m.media-amazon.com/images/I/71MkH0TnRyL._UF1000,1000_QL80_.jpg" alt="Hibiscus" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Hibiscus</h3>
                <p class="text-sm text-gray-600">Large, colorful outdoor blooms. Rs. 90</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Hibiscus">
                    <input type="hidden" name="price" value="90">
                    <input type="hidden" name="image" value="https://m.media-amazon.com/images/I/71MkH0TnRyL._UF1000,1000_QL80_.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://media.istockphoto.com/id/1127829274/photo/bucket-of-fresh-white-daisy-selling-in-flower-shop.jpg?s=170667a&w=0&k=20&c=IEbeACM2wse9WF9-IIkVKJSwiTEoRWhih2UwmPOBkYo=" loading="lazy">
                    <img src="https://media.istockphoto.com/id/1127829274/photo/bucket-of-fresh-white-daisy-selling-in-flower-shop.jpg?s=170667a&w=0&k=20&c=IEbeACM2wse9WF9-IIkVKJSwiTEoRWhih2UwmPOBkYo=" alt="Daisy" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Daisy</h3>
                <p class="text-sm text-gray-600">Cheerful, low-maintenance flower. Rs. 75</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Daisy">
                    <input type="hidden" name="price" value="75">
                    <input type="hidden" name="image" value="https://media.istockphoto.com/id/1127829274/photo/bucket-of-fresh-white-daisy-selling-in-flower-shop.jpg?s=170667a&w=0&k=20&c=IEbeACM2wse9WF9-IIkVKJSwiTEoRWhih2UwmPOBkYo=">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://m.media-amazon.com/images/I/61tsujok8iL._UF350,350_QL80_.jpg" loading="lazy">
                    <img src="https://m.media-amazon.com/images/I/61tsujok8iL._UF350,350_QL80_.jpg" alt="Zinnia" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Zinnia</h3>
                <p class="text-sm text-gray-600">Heat-tolerant, colorful petals. Rs. 65</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Zinnia">
                    <input type="hidden" name="price" value="65">
                    <input type="hidden" name="image" value="https://m.media-amazon.com/images/I/61tsujok8iL._UF350,350_QL80_.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
        </div>
        </section>

        <!-- indoor section -->
        <section id="indoor">
        <h2 class="text-4xl font-bold text-green-700 mb-4">Indoor Flowers</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://5.imimg.com/data5/LH/FS/NJ/SELLER-18704783/peace-lily-plant.jpg" loading="lazy">
                    <img src="https://5.imimg.com/data5/LH/FS/NJ/SELLER-18704783/peace-lily-plant.jpg" alt="Peace Lily" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Peace Lily</h3>
                <p class="text-sm text-gray-600">Air purifying, low maintenance. Rs. 120</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Peace Lily">
                    <input type="hidden" name="price" value="120">
                    <input type="hidden" name="image" value="https://5.imimg.com/data5/LH/FS/NJ/SELLER-18704783/peace-lily-plant.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://potsandpetals.in/wp-content/uploads/2020/12/perceval-product-image.jpg" loading="lazy">
                    <img src="https://potsandpetals.in/wp-content/uploads/2020/12/perceval-product-image.jpg" alt="Orchid" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Orchid</h3>
                <p class="text-sm text-gray-600">Elegant blooming houseplant. Rs. 200</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Orchid">
                    <input type="hidden" name="price" value="200">
                    <input type="hidden" name="image" value="https://potsandpetals.in/wp-content/uploads/2020/12/perceval-product-image.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://nurserylive.com/cdn/shop/products/nurserylive-african-violets-purple-plant.jpg?v=1634212033" loading="lazy">
                    <img src="https://nurserylive.com/cdn/shop/products/nurserylive-african-violets-purple-plant.jpg?v=1634212033" alt="African Violet" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">African Violet</h3>
                <p class="text-sm text-gray-600">Compact plant with vibrant flowers. Rs. 150</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="African Violet">
                    <input type="hidden" name="price" value="150">
                    <input type="hidden" name="image" value="https://nurserylive.com/cdn/shop/products/nurserylive-african-violets-purple-plant.jpg?v=1634212033">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
        </div>
        </section>

        <!-- gardening tools section -->
        <section id="tools">
        <h2 class="text-4xl font-bold text-green-700 mb-4">Gardening Tools</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://m.media-amazon.com/images/I/51jA+fU9d0L._UF1000,1000_QL80_.jpg" loading="lazy">
                    <img src="https://m.media-amazon.com/images/I/51jA+fU9d0L._UF1000,1000_QL80_.jpg" alt="Trowel" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Trowel</h3>
                <p class="text-sm text-gray-600">Perfect for planting & digging. Rs. 55</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Trowel">
                    <input type="hidden" name="price" value="55">
                    <input type="hidden" name="image" value="https://m.media-amazon.com/images/I/51jA+fU9d0L._UF1000,1000_QL80_.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://organicbazar.net/cdn/shop/products/Watering-can-feature-new-1.jpg?v=1694167712" loading="lazy">
                    <img src="https://organicbazar.net/cdn/shop/products/Watering-can-feature-new-1.jpg?v=1694167712" alt="Watering Can" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Watering Can</h3>
                <p class="text-sm text-gray-600">Lightweight & easy to carry. Rs. 80</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Watering Can">
                    <input type="hidden" name="price" value="80">
                    <input type="hidden" name="image" value="https://organicbazar.net/cdn/shop/products/Watering-can-feature-new-1.jpg?v=1694167712">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
            <div class="bg-white p-4 rounded-2xl shadow-2xl">
                <a href="https://images.thdstatic.com/productImages/51d35080-40fc-438b-82f5-eb1013d77e82/svn/nevlers-pruning-shears-mgshearbp26-e1_600.jpg" loading="lazy">
                    <img src="https://images.thdstatic.com/productImages/51d35080-40fc-438b-82f5-eb1013d77e82/svn/nevlers-pruning-shears-mgshearbp26-e1_600.jpg" alt="Pruning Shears" class="w-full h-32 object-cover rounded mb-2">
                </a>
                <h3 class="text-lg font-semibold">Pruning Shears</h3>
                <p class="text-sm text-gray-600">Sharp blades for neat trimming. Rs. 95</p>
                <form method="POST" action="Shop.php">
                    <input type="hidden" name="name" value="Pruning Shears">
                    <input type="hidden" name="price" value="95">
                    <input type="hidden" name="image" value="https://images.thdstatic.com/productImages/51d35080-40fc-438b-82f5-eb1013d77e82/svn/nevlers-pruning-shears-mgshearbp26-e1_600.jpg">
                    <button type="submit" name="add_to_cart" class="mt-2 px-4 py-2 bg-green-500 text-white rounded">Add to Cart</button>
                </form>
            </div>
        </div>
        </section>

        <!-- Cart Section -->
        <section id="cart" class="bg-white p-6 rounded-2xl shadow-2xl border border-green-300 mt-12">
            <h2 class="text-3xl font-bold text-green-700 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 0 0 4.25 22.5h15.5a1.875 1.875 0 0 0 1.865-2.071l-1.263-12a1.875 1.875 0 0 0-1.865-1.679H16.5V6a4.5 4.5 0 1 0-9 0ZM12 3a3 3 0 0 0-3 3v.75h6V6a3 3 0 0 0-3-3Zm-3 8.25a3 3 0 1 0 6 0v-.75a.75.75 0 0 1 1.5 0v.75a4.5 4.5 0 1 1-9 0v-.75a.75.75 0 0 1 1.5 0v-.75Z" clip-rule="evenodd" />
                </svg>
                Cart
            </h2>
            
            <div id="cart-items" class="space-y-4 max-h-72 overflow-y-auto pr-2">
                <?php if (empty($cart)): ?>
                    <p class="text-gray-600">Your cart is empty.</p>
                <?php else: ?>
                    <?php foreach ($cart as $index => $item): ?>
                        <div class="flex items-center justify-between bg-gray-100 p-3 rounded-lg shadow-sm">
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-12 h-12 object-cover rounded" loading="lazy">
                                <div>
                                    <p class="font-medium"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <p class="text-sm text-gray-600">Rs. <?php echo number_format($item['price'], 2); ?></p>
                                </div>
                            </div>
                            <form method="POST" action="Shop.php">
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                <button type="submit" name="remove_from_cart" class="text-red-600 hover:text-red-800 font-medium">Remove</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-6 flex justify-between items-center border-t pt-4">
                <p class="text-lg font-semibold text-green-700">Total: Rs. <span id="total-price"><?php echo number_format($total_price, 2); ?></span></p>
                <!-- <form action="checkout.php" method="POST">
                    <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-full shadow-md transition-all">
                        Proceed to Checkout
                    </button>
                </form> -->
                <button onclick="window.location.href='checkout.php'" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-full shadow-md transition-all">
                    Proceed to Checkout
                </button>
            </div>
        </section>
    </main>
    </div>

    <!-- JavaScript code for cart section -->
    <script>
        // Update total price dynamically (optional, since PHP handles it)
        document.addEventListener('DOMContentLoaded', function () {
            const totalPriceEl = document.getElementById('total-price');
            totalPriceEl.textContent = '<?php echo number_format($total_price, 2); ?>';
        });
    </script>

        
    <!-- Footer of the WebPage -->
    <footer class="bg-gray-300 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
        <div>
            <a href="index.php" class="flex items-center space-x-3 mb-4">
            <img src="https://media.istockphoto.com/id/1045368942/vector/abstract-green-leaf-logo-icon-vector-design-ecology-icon-set-eco-icon.jpg?s=612x612&w=0&k=20&c=XIfHMI8r1G73blCpCBFmLIxCtOLx8qX0O3mZC9csRLs=" alt="Plant-Hub" class="w-10 h-10 rounded-full" loading="lazy">
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
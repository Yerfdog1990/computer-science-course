<?php
// ------------------ Question 1 --------------------
echo "Question 1: Create reusable functions for modular code by performing the following 3 tasks: <br>";
echo "<hr>";
// ------------------ Question 1(a) --------------------
# (a) Write a PHP function called calculateTotal that
# calculates the total price of a customer's order, including a 10% sales tax.
# It takes the price of an item, and the quantity purchased as input parameters, and
# calculate the total cost using arithmetic operations (price * quantity),
# applies a 10% tax, and returns the total amount.

function calculateTotal(float $price, int $quantity): float
{
    $totalCost = $price * $quantity;
    $totalCost += $totalCost * 0.10;
    return number_format($totalCost, 2);
}

# calling the function
echo "<span style='color: green'>Total cost: </span>$" . calculateTotal(10.99, 2);
echo "<hr>";

// ------------------ Question 1(b) --------------------
# Write a function formatProductName($name) that,
# trims any extra spaces,
# capitalizes the first letter of each word and
# ensures the name does not exceed 50 characters.

function formatProductName($name): string
{
    $name = trim($name);
    $name = ucwords($name);
    return substr($name, 0, 50);
}

# calling the function
$originalString = "  capitalizes the first letter of each word  ";
echo "<span style='color: green'>Original string: </span>" . $originalString;
echo "<br>";
echo "<span style='color: green'>Title case string: </span>" .formatProductName($originalString);
echo "<hr>";

// ------------------ Question 1(c) --------------------
# Create a function calculateDiscount($price, $discountPercent) that, accepts a product price and a discount percentage.
# Returns the final price after applying the discount.

function calculateDiscount($price, $discountPercent): float
{
    $discount = $price * ($discountPercent / 100);
    return number_format($price - $discount, 2);
}

# calling the function
echo "<span style='color: green'>Discounted price:  </span>$" . calculateDiscount(10.99, 10);
echo "<hr>";

// ------------------ Question 2 --------------------
echo "Question 2: Manipulate and manage arrays using built-in PHP functions by performing the following 3 tasks: <br>";
echo "<hr>";
// ------------------ Question 2(a) --------------------
# Create an array of products, each containing a name and price.
# Display the list of available products in a structured format.
# Use array functions to remove duplicate products, sort products by price in ascending order.

$products = [
    ["Apple", "$0.30"],
    ["Banana", "$0.20"],
    ["Orange", "$0.10"],
    ["Pineapple", "$0.50"],
    ["Grape", "$0.40"],
    ["Strawberry", "$0.60"],
    ["Watermelon", "$2.00"],
    ["Mango", "$1.50"],
    ["Kiwi", "$0.80"],
    ["Apple", "$0.30"], // duplicate
    ["Banana", "$0.20"], // duplicate
    ["Orange", "$0.10"], // duplicate
    ["Pineapple", "$0.50"]  // duplicate
];

echo "<p style='color: green'>Before removing duplicates:</p>";

foreach ($products as $product) {
    echo $product[0] . ": " . $product[1] . " <br>";
}

echo "<hr>";
echo "<p style='color: green'>After removing duplicates and sorting by price:</p>";

// Remove duplicates by converting to associative array with product names as keys
$uniqueProducts = [];
foreach ($products as $product) {
    if (!isset($uniqueProducts[$product[0]])) {
        $uniqueProducts[$product[0]] = $product[1];
    }
}

// Convert back to array for sorting
$sortedProducts = [];
foreach ($uniqueProducts as $name => $price) {
    $sortedProducts[] = [$name, $price];
}

// Sort by price (convert price string to float for comparison)
usort($sortedProducts, function($a, $b) {
    $priceA = floatval(str_replace('$', '', $a[1]));
    $priceB = floatval(str_replace('$', '', $b[1]));
    return $priceA <=> $priceB;
});

// Display sorted unique products
foreach ($sortedProducts as $product) {
    echo $product[0] . ": " . $product[1] . " <br>";
}

echo "<hr>";
// ------------------ Question 2(b) --------------------
# There is a seasonal sale, and you need to apply a 10% discount to all products in the "Electronics" category.
# Given an associative array of products, where each product has a name, category, and price,
# write a PHP script to identify all products in the "Electronics" category, reduce their prices by 10% and
# print the updated product list.

$products = [
    ["Laptop", "Electronics", "$999.99"],
    ["Smartphone", "Electronics", "$699.99"],
    ["Headphones", "Electronics", "$149.99"],
    ["Tablet", "Electronics", "$399.99"],
    ["Smartwatch", "Electronics", "$299.99"],
    ["Camera", "Electronics", "$549.99"],
    ["Apple", "Fruit", "$0.30"],
    ["Banana", "Fruit", "$0.20"],
    ["Orange", "Fruit", "$0.10"],
    ["Grape", "Fruit", "$0.40"],
    ["Book", "Education", "$15.99"],
    ["Notebook", "Education", "$4.99"],
    ["Pen", "Education", "$1.99"],
    ["T-Shirt", "Clothing", "$19.99"],
    ["Jeans", "Clothing", "$49.99"],
    ["Sneakers", "Clothing", "$79.99"]
];

echo "<p style='color: green'>Original product list:</p>";
foreach ($products as $product) {
    echo $product[0] . " (" . $product[1] . "): " . $product[2] . " <br>";
}

echo "<hr>";
echo "<p style='color: green'>After applying 10% discount to Electronics products:</p>";

// Apply 10% discount to Electronics products
$updatedProducts = [];
foreach ($products as $product) {
    $name = $product[0];
    $category = $product[1];
    $price = $product[2];

    if ($category === "Electronics") {
        // Remove $ sign and convert to float
        $numericPrice = floatval(str_replace('$', '', $price));
        // Apply 10% discount
        $discountedPrice = $numericPrice * 0.90;
        // Format back to price string
        $price = "$" . number_format($discountedPrice, 2);
    }

    $updatedProducts[] = [$name, $category, $price];
}

// Display updated product list
foreach ($updatedProducts as $product) {
    echo $product[0] . " (" . $product[1] . "): " . $product[2] . " <br>";
}

echo "<hr>";
// ------------------ Question 2(c) --------------------
# Your e-commerce platform is sourcing products from two different suppliers,
# and each supplier provides an array of available products.
# Write a PHP script to merge both supplier inventories into a single array,
# ensure there are no duplicate products in the final list, and print the combined inventory.

$firstSupplier = array(
    ["Laptop", "Electronics", "$999.99"],
    ["Smartphone", "Electronics", "$699.99"],
    ["Headphones", "Electronics", "$149.99"],
    ["Tablet", "Electronics", "$399.99"],
    ["Apple", "Fruit", "$0.30"],
    ["Banana", "Fruit", "$0.20"],
    ["Book", "Education", "$15.99"],
    ["T-Shirt", "Clothing", "$19.99"],
    ["Laptop", "Electronics", "$999.99"], // duplicate
    ["Smartphone", "Electronics", "$699.99"]  // duplicate
);

$secondSupplier = array(
    ["Camera", "Electronics", "$549.99"],
    ["Smartwatch", "Electronics", "$299.99"],
    ["Orange", "Fruit", "$0.10"],
    ["Grape", "Fruit", "$0.40"],
    ["Notebook", "Education", "$4.99"],
    ["Pen", "Education", "$1.99"],
    ["Jeans", "Clothing", "$49.99"],
    ["Sneakers", "Clothing", "$79.99"],
    ["Headphones", "Electronics", "$149.99"], // duplicate with first supplier
    ["Apple", "Fruit", "$0.30"]  // duplicate with first supplier
);

// Merge the suppliers' products
$allProducts = array_merge($firstSupplier, $secondSupplier);

// Remove duplicates by using product name as key
$uniqueProducts = [];
foreach ($allProducts as $product) {
    $key = $product[0]; // Use product name as unique identifier
    if (!isset($uniqueProducts[$key])) {
        $uniqueProducts[$key] = $product;
    }
}

// Convert back to indexed array
$mergedProducts = array_values($uniqueProducts);

// Print the merged products
echo "<p style='color: green'>Final list of products: </p>";
foreach ($mergedProducts as $product) {
    echo $product[0] . " (" . $product[1] . "): " . $product[2] . " <br>";
}
echo "<hr>";
// ------------------ Question 3 --------------------
echo "Question 3: Apply PHP string manipulation functions to process text by performing the following 3 tasks: <br>";
echo "<hr>";

// ------------------ Question 3(a) --------------------
# Your e-commerce website stores product names with product descriptions, but you need to format them.
# Write a PHP script to convert product descriptions to lowercase and replace any underscores (_) with spaces,
# ensure all product names are sanitized by removing unnecessary special characters.

// Array of products with distorted descriptions
$productsWithDistortedDescriptions = [
    ["Wireless Headphones", "PREMIUM_SOUND_QUALITY_WITH_NOISE_CANCELLATION!!!"],
    ["Smart Watch", "fitness_tracker__heart_rate_monitor__water_resistant"],
    ["Laptop Stand", "ADJUSTABLE_ERGONOMIC_DESIGN_FOR_BETTER_POSTURE@@@###"],
    ["USB-C Hub", "multi_port_adapter__4K_HDMI__USB_3.0__SD_CARD_READER!!!"],
    ["Bluetooth Speaker", "PORTABLE_WATERPROOF_SPEAKER_WITH_360_DEGREE_SOUND$$$"],
    ["Mechanical Keyboard", "RGB_BACKLIT__GAMING_KEYBOARD__MECHANICAL_SWITCHES@@@"],
    ["Webcam", "HD_1080P_WEBCAM_WITH_BUILT_IN_MICROPHONE_AND_AUTO_FOCUS###"],
    ["Mouse Pad", "LARGE_EXTENDED_MOUSE_PAD__ANTI_SLIP_BASE__WATER_RESISTANT!!!"],
    ["Phone Case", "SHOCKPROOF_PROTECTIVE_CASE_WITH_SCREEN_PROTECTOR___"],
    ["Cable Organizer", "CABLE_MANAGEMENT_SOLUTION__CLIPS_AND_TIES_NEAT"]
];

echo "<p style='color: green'>Original distorted product descriptions:</p>";
foreach ($productsWithDistortedDescriptions as $product) {
    echo "<strong>" . $product[0] . ":</strong> " . $product[1] . "<br>";
}

echo "<hr>";

// Sanitization function
function sanitizeProductDescription($description): string
{
    // Convert to lowercase
    $description = strtolower($description);

    // Replace underscores with spaces
    $description = str_replace('_', ' ', $description);

    // Remove unnecessary special characters (keep letters, numbers, spaces, and basic punctuation)
    $description = preg_replace('/[^a-z0-9\s.,!?-]/', '', $description);

    // Remove extra spaces
    $description = preg_replace('/\s+/', ' ', $description);

    // Trim spaces from beginning and end
    return trim($description);
}

// Apply sanitization to all product descriptions
$sanitizedProducts = [];
foreach ($productsWithDistortedDescriptions as $product) {
    $sanitizedDescription = sanitizeProductDescription($product[1]);
    $sanitizedProducts[] = [$product[0], $sanitizedDescription];
}

echo "<p style='color: green'>Sanitized product descriptions:</p>";
foreach ($sanitizedProducts as $product) {
    echo "<strong>" . $product[0] . ":</strong> " . $product[1] . "<br>";
}

echo "<hr>";
// ------------------ Question 3(b) --------------------
# The e-commerce website stores product descriptions,
# but you need to analyze them for better readability and keyword optimization.
# Write a PHP script that accepts a product description as input
# (e.g., "This is a high-quality leather wallet with RFID protection."),
# calculates and prints the total number of characters in the description,
# Counts and prints the total number of words in the description and
# checks if the word "leather" appears in the description and
# prints "Keyword found" if it exists, otherwise "Keyword not found".

function analyzeDescription(string $description, string $keyword): void
{
    // Calculate total number of characters
    $charCount = strlen($description);
    echo "<span style='color: green'>Total number of characters: </span>" . $charCount . "<br>";

    // Count total number of words
    $wordCount = str_word_count($description);
    echo "<span style='color: green'>Total number of words: </span>" . $wordCount . "<br>";

    // Check if keyword exists (case-insensitive)
    echo "<span style='color: green'>Search result: </span>";
    if (stripos($description, $keyword) !== false) {
        echo "<span style='color: blue'>Keyword found</span>";
    } else {
        echo "<span style='color: red'>Keyword not found</span>";
    }
}

$description = "This is a high-quality leather wallet with RFID protection.";
analyzeDescription($description, "leather");

echo "<hr>";
// ------------------ Question 3(c) --------------------
# Your e-commerce site collects customer reviews, and you need to ensure they are properly displayed and formatted.
# Write a PHP script that accepts a customer review as
# input (e.g., "Great product! Fast delivery and excellent service."),
# extracts and prints the first 20 characters of the review, followed by "...to indicate a preview,
# searches for the word "excellent" in the review and prints its starting position (if found).
# Concatenates the review with a message: "Thank you for your feedback!" and prints the full updated review.

function analyzeReview(string $review, string $keyword): void
{
    // Extract first 20 characters
    $extractedText = substr($review, 0, 20) . "...";
    echo "<span style='color: green'>Extracted text: </span>" . $extractedText . "<br>";

    // Find keyword position
    if(str_contains($review, $keyword)) {
        $keywordPosition = strpos($review, $keyword);
        echo "<span style='color: green'>Keyword position: </span>" . $keywordPosition . "<br>";
        // Concatenate review with message
        $updatedReview = $review . " Thank you for your feedback!";
        echo "<span style='color: green'>Updated review: </span>" . $updatedReview . "<br>";
    } else {
        // Print message if keyword not found
        echo "<span style='color: green'>Search result: </span>";
        echo "<span style='color: red'>Keyword not found in review</span><br>";
    }
}

// Call the function
$review = "Great product! Fast delivery and excellent service.";
analyzeReview($review, "excellent");
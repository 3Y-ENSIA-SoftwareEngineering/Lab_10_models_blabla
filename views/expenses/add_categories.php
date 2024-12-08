<?php
// Include the Category model
require_once __DIR__ . '/../../models/Category.php'; 

// Initialize an error message variable
$error_message = '';

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the category name from the POST data
    $name = trim($_POST['name'] ?? '');

    // Validate input
    if ($name !== '') {
        // Create an instance of the Category model
        $category = new Category();
        
        // Add the category to the database
        $category->add($name); // Assuming `add($name)` exists in the model
        
        // Redirect to the categories page
        header("Location: categories.php");
        exit();
    } else {
        $error_message = "Category name is required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Add New Category</h1>
    
    <!-- Display error message, if any -->
    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <!-- Form for adding a category -->
    <form method="POST" action="add_category.php">
        <label for="name">Category Name:</label>
        <input type="text" id="name" name="name" required>
        
        <button type="submit">Add Category</button>
    </form>
    
    <a href="categories.php">Back to Categories List</a>
</body>
</html>
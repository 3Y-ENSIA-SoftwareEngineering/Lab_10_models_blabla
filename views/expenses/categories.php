<?php
// Define the base directory for models
$modelsDirectory = __DIR__ . '/../../models/';

// Initialize data array
$data = ['categories' => []];

// Include the Category model
if (file_exists($modelsDirectory . 'Category.php')) {
    require_once $modelsDirectory . 'Category.php';
    
    // Fetch all categories from the database
    $category = new Category(); // Assuming Category model exists
    $data['categories'] = $category->getAll(); // Fetch all categories
} else {
    echo "Category module not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Manage Categories</h1>
    
    <a href="http://localhost/Lab_10_models_blabla/views/expenses/add_categories.php">Add New Category</a>

    <h2>Categories List</h2>
    <ul>
        <?php if (!empty($data['categories'])): ?>
            <?php foreach ($data['categories'] as $category): ?>
                <li>
                    <?= htmlspecialchars($category['name']) ?>
                    <!-- Optional: Add edit and delete actions -->
                    <a href="?module=category&action=edit&id=<?= $category['id'] ?>">Edit</a>
                    <a href="?module=category&action=delete&id=<?= $category['id'] ?>" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No categories available.</p>
        <?php endif; ?>
    </ul>
</body>
</html>
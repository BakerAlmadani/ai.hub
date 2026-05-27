<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Tools Directory</title>
    <link rel = "stylesheet" href = "Ai.css">
</head>
<body>

    <div class="container">
        <div class="header">
            <img src="Website.jpeg" alt="Ai Hub logo" title="Ai Hub logo" class = "site-logo">
            <h1>AI Tools Directory</h1>
            <p>Explore the best artificial intelligence tools</p>
        </div>

        <!-- Filter Checkboxes Section -->
        <div class="filter-container">
            <h3>Filter by Features:</h3>
            <div class="filter-options">
                <label class="filter-label">
                    <input type="checkbox" class="feature-checkbox" value="Generating images from text"> Generate Images
                </label>
                <label class="filter-label">
                    <input type="checkbox" class="feature-checkbox" value="Coding Tasks"> Coding Tasks
                </label>
                <label class="filter-label">
                    <input type="checkbox" class="feature-checkbox" value="Writing essays"> Text Generation
                </label>
                <label class="filter-label">
                    <input type="checkbox" class="feature-checkbox" value="Research and learning"> Research
                </label>
            </div>
        </div>

        <div class="grid">
            <?php
            $query = "SELECT * FROM ai_tools";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $features_string = isset($row['features']) ? $row['features'] : ''; 
                    
                   $features_array = array_filter(array_map('trim', explode(',', $features_string)));
                    ?>
                    
                    <div class="card" data-features="<?php echo htmlspecialchars($features_string); ?>">
                        <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?> Logo">
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <?php if (!empty($features_array)): ?>
                            <div class="features-list">
                                <?php foreach ($features_array as $feature): ?>
                                    <span class="feature-badge"><?php echo htmlspecialchars($feature); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <a href="tool.php?id=<?php echo $row['id']; ?>" class="btn">View Details</a>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='text-align:center; grid-column: 1 / -1;'>No tools found in the database.</p>";
            }
            ?>
            <p id="no-results">No tools match all selected filters.</p>
        </div>

        <hr style="border: 0; height: 1px; background: #E5E7EB; margin: 40px 0;">
        
        <a href="Review.html" class="footer-link">⭐ Rate Our Website</a>
    </div>

    <script src = "AiScript.js"></script>
</body>
</html>

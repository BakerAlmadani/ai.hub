<?php
include 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>Error: No AI tool selected. <a href='ai.php'>Go back</a></div>");
}

$tool_id = (int)$_GET['id'];
$tool_query = "SELECT * FROM ai_tools WHERE id = $tool_id";
$tool_result = mysqli_query($conn, $tool_query);

if (mysqli_num_rows($tool_result) == 0) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>Error: AI tool not found. <a href='ai.php'>Go back</a></div>");
}

$tool = mysqli_fetch_assoc($tool_result);
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json_data = json_decode(file_get_contents("php://input"), true);

    $action = $json_data['action'] ?? $_POST['action'] ?? '';
    $username = mysqli_real_escape_string($conn, $json_data['username'] ?? $_POST['username'] ?? '');
    $email = mysqli_real_escape_string($conn, $json_data['email'] ?? $_POST['email'] ?? '');
    $rating = (int)($json_data['rating'] ?? $_POST['rating'] ?? 0);
    $comments = mysqli_real_escape_string($conn, $json_data['comments'] ?? $_POST['comments'] ?? '');
    $date = date('Y-m-d H:i:s');

    $check_query = "SELECT ID FROM Comments WHERE email = '$email' AND tool_id = $tool_id";
    $check_result = mysqli_query($conn, $check_query);
    $user_exists = mysqli_num_rows($check_result) > 0;

    if ($action == 'submit') {
        if ($user_exists) {
            $message = "<div class='alert error'><b>Oops!</b> You have already reviewed this tool. Use Update or Delete below.</div>";
        } else {
            $sql = "INSERT INTO Comments (tool_id, Username, date, email, rating, comments) 
                    VALUES ($tool_id, '$username', '$date', '$email', $rating, '$comments')";
            if(mysqli_query($conn, $sql)) $message = "<div class='alert success'>Review added successfully!</div>";
        }
    } elseif ($action == 'update') {
        if ($user_exists) {
            $sql = "UPDATE Comments SET Username = '$username', date = '$date', rating = $rating, comments = '$comments' 
                    WHERE email = '$email' AND tool_id = $tool_id";
            if(mysqli_query($conn, $sql)) $message = "<div class='alert success'>Review updated successfully!</div>";
        } else {
            $message = "<div class='alert error'><b>Error:</b> No review found to update.</div>";
        }
    } elseif ($action == 'delete') {
        if ($user_exists) {
            $sql = "DELETE FROM Comments WHERE email = '$email' AND tool_id = $tool_id";
            if(mysqli_query($conn, $sql)) $message = "<div class='alert success'>Review deleted successfully.</div>";
        } else {
            $message = "<div class='alert error'><b>Error:</b> No review found to delete.</div>";
        }
    }

    if (!empty($json_data)) {
        echo $message;
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tool['name']); ?> - AI Details</title>
    <link rel = "stylesheet" href = "Tools.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?php echo htmlspecialchars($tool['image_url']); ?>" alt="<?php echo htmlspecialchars($tool['name']); ?> Logo">
            <h1><?php echo htmlspecialchars($tool['name']); ?></h1>
        </div>

        <h2 class="section-title">📌 Description</h2>
        <p><?php echo nl2br(htmlspecialchars($tool['description'])); ?></p>
        
        <h2 class="section-title">✨ Features</h2>
        <ul>
            <?php 
            $features_array = explode("\n", $tool['features']);
            foreach($features_array as $feature) {
                if(trim($feature) != "") echo "<li>" . htmlspecialchars(trim($feature)) . "</li>";
            }
            ?>
        </ul>
        
        <h2 class="section-title">🔗 Visit Website</h2>
        <br>
        <a href="<?php echo htmlspecialchars($tool['website_url']); ?>" target="_blank" class="btn-link">Open <?php echo htmlspecialchars($tool['name']); ?> Official Site</a>
        
        <hr style="border: 0; height: 1px; background: var(--border); margin: 40px 0;">

        <h2 class="section-title">💬 User Comments</h2>
        <br><br>
        <div id="responseMessage"><?php echo $message; ?></div>

        <?php
        $comments_query = "SELECT Username, date, rating, comments FROM Comments WHERE tool_id = $tool_id ORDER BY date DESC";
        $comments_result = mysqli_query($conn, $comments_query);

        if (mysqli_num_rows($comments_result) > 0) {
            while ($row = mysqli_fetch_assoc($comments_result)) {
                $stars = str_repeat("⭐", $row['rating']);
                ?>
                <div class="comment-card">
                    <div class="comment-header">
                        <span class="comment-name"><?php echo htmlspecialchars($row['Username']); ?></span>
                        <span class="comment-date"><?php echo $stars; ?> • <?php echo date("M j, Y", strtotime($row['date'])); ?></span>
                    </div>
                    <p class="comment-text"><?php echo nl2br(htmlspecialchars($row['comments'])); ?></p>
                </div>
                <?php
            }
        } else {
            echo "<p style='color: var(--text-muted);'>No comments yet. Be the first to review!</p>";
        }
        ?>

        <hr style="border: 0; height: 1px; background: var(--border); margin: 40px 0;">

        <h2 class="section-title">📝 Manage Your Review</h2>
        <form id = "Review" method="POST" action="" style="margin-top: 20px;" >
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="username" class="form-control" placeholder="John Doe" id = "UserName" required>
            </div>

            <div class="form-group">
                <label>Email (Required to verify identity):</label>
                <input type="email" name="email" class="form-control" placeholder="john@example.com" id = "Email" required>
            </div>

            <div class="form-group">
                <label>Comment:</label>
                <textarea name="comments" class="form-control" rows="4" placeholder="Share your experience..." id = "Comment" ></textarea>
            </div>

            <div class="form-group">
                <label>Rating:</label>
                <select name="rating" class="form-control" id = "Rating" required>
                    <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                    <option value="4">⭐⭐⭐⭐ Good</option>
                    <option value="3">⭐⭐⭐ Average</option>
                    <option value="2">⭐⭐ Poor</option>
                    <option value="1">⭐ Very Bad</option>
                </select>
            </div>

            <div class="button-group">
                <button type="submit" name="action" value="submit" class="btn btn-submit">Submit Review</button>
                <button type="submit" name="action" value="update" class="btn btn-update">Update Review</button>
                <button type="submit" name="action" value="delete" class="btn btn-delete" formnovalidate>Delete</button>
            </div>
        </form>

        <a href="ai.php" class="back-link">⬅ Back to Tools List</a>
        <script src = "ToolReview.js"></script>
    </div>

</body>
</html>
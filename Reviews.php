<?php
// 1. Turn on Error Reporting so we can see the exact problem instead of a 500 error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

// 2. Safely check if the database connected properly
if (!$conn) {
    echo "<div class='alert error'><b>Database Connection Failed:</b> " . mysqli_connect_error() . "</div>";
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $json_data = json_decode(file_get_contents("php://input"), true);

    $action = $json_data['action'] ?? $_POST['action'] ?? '';
    $email = mysqli_real_escape_string($conn, $json_data['email'] ?? $_POST['email'] ?? '');
    $rating = (int)($json_data['rating'] ?? $_POST['rating'] ?? 0);
    $comments = mysqli_real_escape_string($conn, $json_data['comments'] ?? $_POST['comments'] ?? '');
    $date = date('Y-m-d H:i:s');
    $issues = mysqli_real_escape_string($conn, $json_data['issues'] ?? $_POST['issues'] ?? '');

    // 3. Run the query and safely check for SQL errors
    $check_query = "SELECT ID FROM website WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    // If the table or column doesn't exist, this catches the crash and prints the reason
    if (!$check_result) {
        echo "<div class='alert error'><b>SQL Check Error:</b> " . mysqli_error($conn) . "</div>";
        exit;
    }

    $user_exists = mysqli_num_rows($check_result) > 0;

    if ($action == 'submit') {
        if ($user_exists) {
            $message = "<div class='alert error'><b>Oops!</b> You have already reviewed this tool. Use Update or Delete below.</div>";
        } else {
            $sql = "INSERT INTO website (date, email, rating, comments, issues) 
                    VALUES ('$date', '$email', $rating, '$comments', '$issues')";
            if (mysqli_query($conn, $sql)) {
                $message = "<div class='alert success'>Review added successfully!</div>";
            } else {
                $message = "<div class='alert error'><b>SQL Insert Error:</b> " . mysqli_error($conn) . "</div>";
            }
        }
    } elseif ($action == 'update') {
        if ($user_exists) {
            $sql = "UPDATE website SET date = '$date', rating = $rating, comments = '$comments', issues = '$issues' 
                    WHERE email = '$email'";
            if (mysqli_query($conn, $sql)) {
                $message = "<div class='alert success'>Review updated successfully!</div>";
            } else {
                $message = "<div class='alert error'><b>SQL Update Error:</b> " . mysqli_error($conn) . "</div>";
            }
        } else {
            $message = "<div class='alert error'><b>Error:</b> No review found to update.</div>";
        }
    } elseif ($action == 'delete') {
        if ($user_exists) {
            $sql = "DELETE FROM website WHERE email = '$email'";
            if (mysqli_query($conn, $sql)) {
                $message = "<div class='alert success'>Review deleted successfully.</div>";
            } else {
                $message = "<div class='alert error'><b>SQL Delete Error:</b> " . mysqli_error($conn) . "</div>";
            }
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
<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "guestbook";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed.']));
}

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    if (empty($_POST['user_name']) || !preg_match("/^[a-zA-Z0-9]+$/", $_POST['user_name'])) {
        $errors[] = "Invalid user name";
    }

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email";
    }

    if (empty($_POST['message'])) {
        $errors[] = "Message is required";
    }

    if (empty($_POST['captcha']) || $_POST['captcha'] != $_SESSION['captcha']) {
        $errors[] = "Invalid CAPTCHA";
    }

    if (empty($errors)) {
        $user_name = htmlspecialchars($_POST['user_name']);
        $email = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);
        $user_ip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $stmt = $conn->prepare("INSERT INTO messages (user_name, email, message, user_ip, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $user_name, $email, $message, $user_ip, $user_agent);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['user_name'] = $user_name;
            $response['email'] = $email;
            $response['message'] = $message;
            $response['created_at'] = date("Y-m-d H:i:s");
        } else {
            $response['success'] = false;
            $response['error'] = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['success'] = false;
        $response['error'] = implode(", ", $errors);
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 5;
    $offset = ($page - 1) * $limit;
    $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at';
    $sort_order = isset($_GET['sort_order']) && in_array(strtolower($_GET['sort_order']), ['asc', 'desc']) ? strtoupper($_GET['sort_order']) : 'DESC';

    $valid_sort_columns = ['user_name', 'email', 'created_at'];
    if (!in_array($sort_by, $valid_sort_columns)) {
        $sort_by = 'created_at';
    }

    $query = "SELECT * FROM messages ORDER BY $sort_by $sort_order LIMIT $limit OFFSET $offset";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        $response['success'] = true;
        $response['messages'] = $messages;

        $total_result = $conn->query("SELECT COUNT(*) AS total FROM messages");
        $total_row = $total_result->fetch_assoc();
        $total_messages = $total_row['total'];
        $response['total_pages'] = ceil($total_messages / $limit);
    } else {
        $response['success'] = false;
        $response['error'] = "No messages found.";
    }
}

$conn->close();

echo json_encode($response);
?>

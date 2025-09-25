<?php
header("Content-Type: application/json; charset=UTF-8");
require_once '../../../src/db.php';
$link = get_db_connection();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode(["message" => "Email and password are required."]);
    exit;
}

$email = mysqli_real_escape_string($link, $data->email);
$password = $data->password;

$sql = "SELECT id, name, email, password, role FROM users WHERE email = '$email'";
$result = mysqli_query($link, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password'])) {
        // In a real application, you would generate a proper JWT token.
        $token = "fake-jwt-token-for-" . $user['id'];

        http_response_code(200);
        echo json_encode([
            "id" => $user['id'],
            "name" => $user['name'],
            "token" => $token
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["message" => "Login failed."]);
    }
} else {
    http_response_code(401);
    echo json_encode(["message" => "Login failed."]);
}
?>
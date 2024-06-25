<?php
$servername = "sql109.infinityfree.com";
$username = "if0_35833320";
$password = "KRMplyqq8PY8";
$dbname = "if0_35833320_pciu_students";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$table = isset($_GET['table']) ? $_GET['table'] : 'CSE'; // Default to 'CSE' if not specified

$sql = "SELECT id, name FROM `$table` WHERE id LIKE ? OR name LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = '%' . $query . '%';
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($suggestions);
?>

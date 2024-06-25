<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "localhost";
$username = "...";
$password = "...";
$dbname = "...";

// Function to fetch student data from the API
function fetch_student_data($studentIdNo) {
    $url = "http://119.18.149.45/StudentAPI/api/studentinfo/get?studentIdNo=" . urlencode($studentIdNo);
    $response = file_get_contents($url);
    if ($response === FALSE) {
        die('Error occurred while fetching student data');
    }
    return json_decode($response, true);
}

// Function to update student data in the database
function update_student_data($conn, $studentIdNo, $studentBatch, $studentSession) {
    $sql = "UPDATE CSE SET start = ?, batch = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === FALSE) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("sss", $studentBatch, $studentSession, $studentIdNo);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo "Student data updated successfully for ID: $studentIdNo\n";
    } else {
        echo "No data was updated for ID: $studentIdNo\n";
    }
    $stmt->close();
}

// Function to get all student IDs from the CSE table
function get_all_student_ids($conn) {
    $studentIds = [];
    $sql = "SELECT id FROM CSE";
    $result = $conn->query($sql);
    if ($result === FALSE) {
        die("Error executing query: " . $conn->error);
    }
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $studentIds[] = $row['id'];
        }
    } else {
        echo "No student IDs found\n";
    }
    return $studentIds;
}

// Main script execution
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$studentIds = get_all_student_ids($conn);

foreach ($studentIds as $studentIdNo) {
    $studentData = fetch_student_data($studentIdNo);
    if ($studentData && isset($studentData[0])) {
        $studentBatch = $studentData[0]['studentBatch'];
        $studentSession = $studentData[0]['studentSession'];

        update_student_data($conn, $studentIdNo, $studentBatch, $studentSession);
    } else {
        echo "No data found for student ID: $studentIdNo\n";
    }
}

$conn->close();
?>

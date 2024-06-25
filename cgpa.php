<?php

//////////////////// working ..................... not finished yet


// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$servername = "...";
$username = "...";
$password = "...";
$dbname = "...";

// Main script execution
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch student data from the API using cURL
function fetch_student_data($studentIdNo) {
    $url = "http://119.18.149.45/StudentAPI/api/studentinfo/get?studentIdNo=" . urlencode($studentIdNo);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: */*',
            'Accept-Language: en-US,en',
            'Connection: keep-alive',
            'Referer: http://119.18.149.45/PCIUStudentPortal/Student/TrimesterResult',
            'Sec-GPC: 1',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
            'X-Requested-With: XMLHttpRequest'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
}

// Function to fetch student results from the API using cURL
function fetch_student_results($studentIdNo, $trimester) {
    $url = "http://119.18.149.45/StudentAPI/api/StudentResult/get?studentIdNo=" . urlencode($studentIdNo) . "&Trimester=" . urlencode($trimester);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Accept: */*',
            'Accept-Language: en-US,en',
            'Connection: keep-alive',
            'Referer: http://119.18.149.45/PCIUStudentPortal/Student/TrimesterResult',
            'Sec-GPC: 1',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36',
            'X-Requested-With: XMLHttpRequest'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    return json_decode($response, true);
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
$studentIds = get_all_student_ids($conn);

foreach ($studentIds as $studentIdNo) {
    $info = fetch_student_data($studentIdNo);
    
    if (!empty($info)) {
        $studentName = $info[0]['StudentName'];
        $studentSession = $info[0]['studentSession'];

        $startTrimester = $studentSession;
        $endTrimester = "Fall 2023";
        $currentTrimester = $startTrimester;
        $trimesters = [];

        while ($currentTrimester != $endTrimester) {
            $trimesters[] = $currentTrimester;

            // Increment the trimester
            if (strpos($currentTrimester, "Spring") !== false) {
                $currentTrimester = "Summer " . substr($currentTrimester, -4);
            } elseif (strpos($currentTrimester, "Summer") !== false) {
                $currentTrimester = "Fall " . substr($currentTrimester, -4);
            } elseif (strpos($currentTrimester, "Fall") !== false) {
                $year = intval(substr($currentTrimester, -4)) + 1;
                $currentTrimester = "Spring " . $year;
            }
        }

        $trimesters[] = $endTrimester;

        $serial = 0;
        $totalGPA = 0; 
        foreach ($trimesters as $trimester) {
            $data = fetch_student_results($studentIdNo, $trimester);
            
            if (!empty($data)) {
                $GPA = $data[0]['GPA'];
                $totalGPA += $GPA;
                $serial++;
            }
        }

        $averageGPA = $serial > 0 ? $totalGPA / $serial : 0;
        $cgpa = number_format($averageGPA, 3);

        $sql = "UPDATE CSE SET cgpa = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === FALSE) {
            die("Error preparing statement: " . $conn->error);
        }
        $stmt->bind_param("ss", $cgpa, $studentIdNo);
        $stmt->execute();
        $stmt->close();

        echo '<tr><th scope="row">'. $studentIdNo . '</th><th>' .  $studentName.  '</th><td>' . $cgpa . '</td></tr>';
    } else {
        echo "No data found for student ID: $studentIdNo\n";
    }
}

echo '</tbody>';
echo '</table>';
echo '</div>';
echo '<hr>';

$conn->close();
?>

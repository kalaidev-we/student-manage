<?php
session_start();
include 'db_config.php'; // Include your database configuration

$response = ['status' => 'error', 'message' => 'Unknown error occurred'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, "r");

        if ($handle) {
            $header = fgetcsv($handle); // Get the header row
            if (!$header || count($header) < 8) { // Assuming at least 8 columns
                $response['message'] = 'Invalid CSV format.';
                echo json_encode($response);
                exit;
            }

            $successCount = 0;
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Map CSV columns to database fields
                $roll_no = mysqli_real_escape_string($conn, $data[0]);
                $name = mysqli_real_escape_string($conn, $data[1]);
                $email = mysqli_real_escape_string($conn, $data[2]);
                $photo_url = mysqli_real_escape_string($conn, $data[3]);
                $mark_10th = mysqli_real_escape_string($conn, $data[4]);
                $mark_12th = mysqli_real_escape_string($conn, $data[5]);
                $cgpa = mysqli_real_escape_string($conn, $data[6]);
                $department = mysqli_real_escape_string($conn, $data[7]);
                $pdf = isset($data[8]) ? mysqli_real_escape_string($conn, $data[8]) : '';

                // Insert into the database
                $query = "INSERT INTO students (roll_no, name, email, photo_url, `10th_Mark`, `12th_Mark`, cgpa, department, pdf)
                          VALUES ('$roll_no', '$name', '$email', '$photo_url', '$mark_10th', '$mark_12th', '$cgpa', '$department', '$pdf')";
                if (mysqli_query($conn, $query)) {
                    $successCount++;
                } else {
                    $response['message'] = 'Database error: ' . mysqli_error($conn);
                    echo json_encode($response);
                    exit;
                }
            }

            fclose($handle);
            $response['status'] = 'success';
            $response['message'] = "$successCount records imported successfully.";
        } else {
            $response['message'] = 'Failed to open the CSV file.';
        }
    } else {
        $response['message'] = 'No file uploaded or upload error.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
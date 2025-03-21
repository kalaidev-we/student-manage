<?php
include 'db_config.php';

if (isset($_GET['roll_no'])) {
    $roll_no = $_GET['roll_no'];

    // Fetch the PDF file path before deleting
    $query = "SELECT pdf FROM students WHERE roll_no = '$roll_no'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $pdf_path = $row['pdf'];

    // Delete PDF file
    if (!empty($pdf_path) && file_exists($pdf_path)) {
        unlink($pdf_path);
    }

    // Delete student from database
    $sql = "DELETE FROM students WHERE roll_no = '$roll_no'";
    if (mysqli_query($conn, $sql)) {
        echo "Student deleted successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

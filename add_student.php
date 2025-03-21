<?php
include 'db_config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $cgpa = $_POST['cgpa'];
    $tenth_mark = $_POST['10th_Mark'];
    $twelfth_mark = $_POST['12th_Mark'];
    $photo_path = "";
    $pdf_path = "";

    // Handle Profile Photo Upload
    if (!empty($_FILES['photo']['name'])) {
        $photo_name = time() . "_" . basename($_FILES['photo']['name']);
        $target_dir = "uploads/photos/";
        $photo_path = $target_dir . $photo_name;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
    }

    // Handle PDF Upload
    if (!empty($_FILES['pdf']['name'])) {
        $pdf_name = time() . "_" . basename($_FILES['pdf']['name']);
        $target_dir = "uploads/";
        $pdf_path = $target_dir . $pdf_name;
        $file_type = strtolower(pathinfo($pdf_path, PATHINFO_EXTENSION));

        // Validate file type (Only allow PDFs)
        if ($file_type == "pdf") {
            move_uploaded_file($_FILES['pdf']['tmp_name'], $pdf_path);
        } else {
            die("<script>alert('❌ Only PDF files are allowed!');</script>");
        }
    }

    // Insert into database
    $sql = "INSERT INTO students (roll_no, name, email, `10th_Mark`, `12th_Mark`, cgpa, department, photo, pdf) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $roll_no, $name, $email, $tenth_Mark, $twelfth_Mark, $cgpa, $department, $photo_path, $pdf_path);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Student added successfully!'); window.location.href='manage_students.php';</script>";
    } else {
        echo "<script>alert('❌ Error: " . $stmt->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(-45deg, #0077B6, #00B4D8, #CAF0F8, #F4E8C1);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
            color: white;
            font-family: 'Arial', sans-serif;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container {
            margin-top: 50px;
            background: rgba(0, 0, 0, 0.85);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>➕ Add Student</h2>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Roll No:</label>
                <input type="text" name="roll_no" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Department:</label>
                <input type="text" name="department" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>10th Mark:</label>
                <input type="number" name="10th_Mark" class="form-control" required> <!-- Keeping original name -->
            </div>

            <div class="mb-3">
                <label>12th Mark:</label>
                <input type="number" name="12th_Mark" class="form-control" required> <!-- Keeping original name -->
            </div>

            <div class="mb-3">
                <label>CGPA:</label>
                <input type="text" name="cgpa" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Profile Photo:</label>
                <input type="file" name="photo" class="form-control">
            </div>

            <div class="mb-3">
                <label>Upload PDF (Certificates/Mark Sheet):</label>
                <input type="file" name="pdf" class="form-control" accept=".pdf">
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit</button>
            <a href="manage_students.php" class="btn btn-secondary w-100 mt-2">Back</a>
        </form>
    </div>
</body>
</html>

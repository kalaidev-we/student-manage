<?php
require 'db_config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['roll_no']) || empty($_GET['roll_no'])) {
    die("⚠️ Error: Invalid Roll Number.");
}

$roll_no = $_GET['roll_no'];

// Fetch student details
$stmt = $conn->prepare("SELECT * FROM students WHERE roll_no = ?");
$stmt->bind_param("s", $roll_no);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("⚠️ Error: Student not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $cgpa = $_POST['cgpa'];
    $tenth_mark = $_POST['10th_Mark'];
    $twelfth_mark = $_POST['12th_Mark'];
    $photo = $student['photo_url'];
    $pdf = $student['pdf'];

    // Handle photo upload
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        $new_file_name = uniqid() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $new_file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                if (!empty($student['photo_url']) && file_exists($student['photo_url'])) {
                    unlink($student['photo_url']);
                }
                $photo = $target_file;
            } else {
                echo "<script>alert('❌ Error uploading photo.');</script>";
            }
        } else {
            echo "<script>alert('❌ Invalid file type.');</script>";
        }
    }

    // Handle PDF upload
    if (!empty($_FILES['pdf']['name'])) {
        $pdf_dir = "uploads/pdfs/";
        $new_pdf_name = uniqid() . "_" . basename($_FILES["pdf"]["name"]);
        $pdf_file = $pdf_dir . $new_pdf_name;
        $pdfFileType = strtolower(pathinfo($pdf_file, PATHINFO_EXTENSION));

        if ($pdfFileType == "pdf") {
            if (move_uploaded_file($_FILES["pdf"]["tmp_name"], $pdf_file)) {
                if (!empty($student['pdf']) && file_exists($student['pdf'])) {
                    unlink($student['pdf']);
                }
                $pdf = $pdf_file;
            } else {
                echo "<script>alert('❌ Error uploading PDF.');</script>";
            }
        } else {
            echo "<script>alert('❌ Invalid file type. Only PDF allowed.');</script>";
        }
    }

    $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, department = ?, cgpa = ?, 10th_Mark = ?, 12th_Mark = ?, photo_url = ?, pdf = ? WHERE roll_no = ?");
    $stmt->bind_param("sssssssss", $name, $email, $department, $cgpa, $tenth_mark, $twelfth_mark, $photo, $pdf, $roll_no);

    if (!$stmt->execute()) {
        die("❌ MySQL Error: " . $stmt->error);
    } else {
        echo "<script>alert('✅ Student details updated!'); window.location.href='manage_students.php';</script>";
    }
}
?>    

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
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
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="text-center">✏️ Edit Student</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="roll_no" value="<?= htmlspecialchars($student['roll_no']); ?>">

                <div class="mb-3">
                    <label class="form-label">Name:</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($student['name']); ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($student['email']); ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Department:</label>
                    <input type="text" name="department" value="<?= htmlspecialchars($student['department']); ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">10th Mark:</label>
                    <input type="number" name="10th_Mark" value="<?= htmlspecialchars($student['10th_Mark']); ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">12th Mark:</label>
                    <input type="number" name="12th_Mark" value="<?= htmlspecialchars($student['12th_Mark']); ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">CGPA:</label>
                    <input type="number" step="0.01" name="cgpa" value="<?= htmlspecialchars($student['cgpa']); ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Profile Photo:</label>
                    <input type="file" name="photo" class="form-control">
                    <?php if (!empty($student['photo_url'])): ?>
                        <img src="<?= $student['photo_url']; ?>" alt="Profile Photo" width="100" class="mt-2 rounded">
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Upload PDF:</label>
                    <input type="file" name="pdf" class="form-control" accept=".pdf">
                    <?php if (!empty($student['pdf'])): ?>
                        <br>
                        <a href="<?= $student['pdf']; ?>" target="_blank" class="btn btn-sm btn-info">View PDF</a>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-success w-100">Update</button>
                <a href="manage_students.php" class="btn btn-secondary w-100 mt-2">Back</a>
            </form>
        </div>
    </div>
</body>
</html>

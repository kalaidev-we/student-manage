<?php include 'header.php'; ?>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <div class="container-fluid" style="margin-left: 260px; padding: 20px;">
        <!-- Page Content Starts Here -->
<?php
require 'db_config.php';

// Fetch students from the database
$result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
?>
    </div> <!-- Closing container-fluid -->
</div> <!-- Closing d-flex -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Dynamic Gradient Background */
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

        h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            background: white;
            color: black;
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background: #0077B6;
            color: white;
        }

        tbody tr:hover {
            background: #00B4D8;
            color: white;
            transition: 0.3s;
        }

        .btn-custom {
            background: #0077B6;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background: #00B4D8;
            color: white;
        }

        .search-box {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>

    <script>
        function searchStudent() {
            let input = document.getElementById("search").value.toUpperCase();
            let table = document.getElementById("studentTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let td = tr[i].getElementsByTagName("td")[1]; // Search by Name
                if (td) {
                    let textValue = td.textContent || td.innerText;
                    tr[i].style.display = textValue.toUpperCase().includes(input) ? "" : "none";
                }
            }
        }
    </script>
</head>

<body>

    <div class="container">
        <h2>📋 Student Management System</h2>
        <input type="text" id="search" class="search-box" placeholder="🔍 Search by Roll_no..." onkeyup="searchStudent()">

        <a href="add_student.php" class="btn btn-custom mb-3">➕ Add Student</a>
        <a href="index.php" class="btn btn-custom mb-3">➕ Back to home</a>
        <table class="table table-bordered" id="studentTable">
    <thead>
    <tr><th>Profile</th>
                        <th>Roll No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>10th Mark</th>
                        <th>12th Mark</th>
                        <th>CGPA</th>
                        <th>Department</th>
                        <th>PDF</th>
                    </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
        <td>
                            <?php if (!empty($row['photo_url'])): ?>
                                <img src="<?php echo $row['photo_url']; ?>" alt="Profile Photo" width="50" height="50" style="border-radius: 50%;">
                            <?php else: ?>
                                <span>No Photo</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['roll_no']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['10th_Mark']); ?></td>
                        <td><?php echo htmlspecialchars($row['12th_Mark']); ?></td>
                        <td><?php echo htmlspecialchars($row['cgpa']); ?></td>
                        <td><?php echo htmlspecialchars($row['department']); ?></td>
                        <td class="text-center">
                            <?php if (!empty($row['pdf'])): ?>
                                <a href="<?= $row['pdf'] ?>" target="_blank" class="btn btn-sm btn-info">View PDF</a>
                            <?php else: ?>
                                <span class="text-muted">No PDF</span>
                            <?php endif; ?>
                        </td>
            <td>
                <a href="edit_student.php?roll_no=<?php echo $row['roll_no']; ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                <a href="delete_student.php?roll_no=<?php echo $row['roll_no']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">🗑️ Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

    </div>

</body>
</html>

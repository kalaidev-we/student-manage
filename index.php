<?php
session_start();
include 'db_config.php';

// Get department from URL
$department = isset($_GET['department']) ? $_GET['department'] : '';

// Modify query based on department filter
$query = "SELECT * FROM students";
if (!empty($department)) {
    $query .= " WHERE department = '" . mysqli_real_escape_string($conn, $department) . "'";
}

$result = mysqli_query($conn, $query);
$students = [];
while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body {
            background: linear-gradient(-45deg, #00c6ff, #0072ff, #00c6ff, #0072ff);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
            min-height: 100vh;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .container-fluid {
            padding: 20px;
        }
        .sidebar {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }
        .table-container {
            width: 100%;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        @media print {
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: none !important;
    }
    .no-print, .sidebar, .pagination {
        display: none !important;
    }
    .table-container {
        box-shadow: none !important;
        border: none !important;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th, .table td {
        border: 1px solid black !important;
        padding: 8px;
        text-align: left;
    }
    .table th {
        background: #0072ff !important;
        color: white !important;
        font-size: 16px;
    }
    .table td {
        font-size: 14px;
    }
}

  
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar">
    <h5>Departments</h5>
    <a class="btn <?php echo empty($department) ? 'btn-primary' : 'btn-secondary'; ?> w-100 mb-2" href="index.php">All</a>
    <a class="btn <?php echo ($department == 'CSE') ? 'btn-primary' : 'btn-secondary'; ?> w-100 mb-2" href="index.php?department=CSE">CSE</a>
    <a class="btn <?php echo ($department == 'ECE') ? 'btn-primary' : 'btn-secondary'; ?> w-100 mb-2" href="index.php?department=ECE">ECE</a>
    <a class="btn <?php echo ($department == 'IT') ? 'btn-primary' : 'btn-secondary'; ?> w-100 mb-2" href="index.php?department=IT">IT</a>
    <a class="btn <?php echo ($department == 'MECH') ? 'btn-primary' : 'btn-secondary'; ?> w-100 mb-2" href="index.php?department=MECH">MECH</a>
    <a href="welcome.php" class="btn btn-secondary w-100">Home</a>
</nav>


            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 px-md-4">
                <h2 class="text-white text-center mb-3">Student Records</h2>

                <!-- Action Buttons (No Print) -->
                <div class="d-flex justify-content-between no-print mb-3">
                    <input type="text" id="searchBox" class="form-control w-25" placeholder="Search students..." onkeyup="filterStudents()">
                    <div>
                        <button class="btn btn-success" onclick="exportData('csv')">Export CSV</button>
                        <button class="btn btn-warning" onclick="window.location.href='manage_students.php'">Edit</button>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Photo</th>
                                <th>10th Mark</th>
                                <th>12th Mark</th>
                                <th>CGPA</th>
                                <th>Department</th>
                                <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody id="studentTable"></tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center no-print" id="pagination"></ul>
                </nav>
            </main>
        </div>
    </div>

    <script>
        let students = <?php echo json_encode($students); ?>;
let filteredStudents = [...students]; // Maintain original data
let currentPage = 1;
const studentsPerPage = 50;

function displayStudents(data = filteredStudents) {
    let tableBody = document.getElementById("studentTable");
    tableBody.innerHTML = "";
    let start = (currentPage - 1) * studentsPerPage;
    let end = start + studentsPerPage;
    let paginatedStudents = data.slice(start, end);

    paginatedStudents.forEach(student => {
        let pdfButton = student.pdf
            ? `<a href="${student.pdf}" target="_blank" class="btn btn-sm btn-info">View PDF</a>`
            : `<span class="text-muted">No PDF</span>`;

        let row = `
            <tr>
                <td>${student.roll_no}</td>
                <td>${student.name}</td>
                <td>${student.email}</td>
                <td><img src="${student.photo_url}" alt="Photo" width="50"></td>
                <td>${student["10th_Mark"]}</td>
                <td>${student["12th_Mark"]}</td>
                <td>${student.cgpa}</td>
                <td>${student.department}</td>
                <td class="text-center">${pdfButton}</td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });

    setupPagination(data);
}

function setupPagination(data = filteredStudents) {
    let pagination = document.getElementById("pagination");
    pagination.innerHTML = "";
    let totalPages = Math.ceil(data.length / studentsPerPage);

    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `;
    }
}

function changePage(page) {
    currentPage = page;
    displayStudents();
}

function filterStudents() {
    let searchText = document.getElementById("searchBox").value.toLowerCase();
    filteredStudents = students.filter(student => 
        student.roll_no.toLowerCase().includes(searchText)||
        student.name.toLowerCase().includes(searchText) || 
        student.email.toLowerCase().includes(searchText) ||
        student.department.toLowerCase().includes(searchText)
    );
    currentPage = 1;
    displayStudents();
}
function printTable() {
    let printContent = document.querySelector(".table-container").innerHTML;
    let originalContent = document.body.innerHTML;

    // Create a print-friendly layout
    document.body.innerHTML = `
        <div style="text-align:center; margin-bottom:20px;">
            <h2>Student Records - PSG Polytechnic College</h2>
        </div>
        ${printContent}
    `;

    window.print(); // Trigger print

    // Restore the original page after printing
    document.body.innerHTML = originalContent;
    window.location.reload(); // Ensure scripts reload properly
}
function exportData(format) {
    if (format === "csv") {
        exportToCSV();
    } 
}

function exportToCSV() {
    let csvContent = "data:text/csv;charset=utf-8,"; 
    let table = document.querySelector(".table"); 
    let rows = table.querySelectorAll("tr"); 

    rows.forEach(row => {
        let rowData = [];
        let cols = row.querySelectorAll("th, td");
        cols.forEach(col => rowData.push(col.innerText.trim()));
        csvContent += rowData.join(",") + "\n";
    });

    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "student_records.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


window.onload = () => {
    displayStudents();
};

    </script>
</body>
</html>

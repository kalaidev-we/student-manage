<?php
include 'db.php';include 'db_config.php';
$sql = "SELECT * FROM students";
$result = mysqli_query($conn, $sql);
$students = [];

while ($row = mysqli_fetch_assoc($result)) {
    $students[] = $row;
}

echo json_encode($students);


$department = $_GET['department'] ?? '';
$searchQuery = $_GET['searchQuery'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Build Query
$query = "SELECT * FROM students WHERE 1";

if (!empty($department)) {
    $query .= " AND department = ?";
}

if (!empty($searchQuery)) {
    $query .= " AND (name LIKE ? OR roll_no LIKE ?)";
}

$query .= " LIMIT ?, ?";

$stmt = mysqli_prepare($conn, $query);

if (!empty($department) && !empty($searchQuery)) {
    $search = "%$searchQuery%";
    mysqli_stmt_bind_param($stmt, "sssii", $department, $search, $search, $offset, $limit);
} elseif (!empty($department)) {
    mysqli_stmt_bind_param($stmt, "sii", $department, $offset, $limit);
} elseif (!empty($searchQuery)) {
    $search = "%$searchQuery%";
    mysqli_stmt_bind_param($stmt, "sii", $search, $search, $offset, $limit);
} else {
    mysqli_stmt_bind_param($stmt, "ii", $offset, $limit);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Generate HTML for student table
$students = '';
while ($row = mysqli_fetch_assoc($result)) {
    $students .= "<tr>
        <td>{$row['roll_no']}</td>
        <td>{$row['name']}</td>
        <td>{$row['email']}</td>
        <td><img src='{$row['photo_url']}' width='50'></td>
        <td>{$row['10th_Mark']}</td>
        <td>{$row['12th_Mark']}</td>
        <td>{$row['cgpa']}</td>
        <td>{$row['department']}</td>
    </tr>";
}

// Fetch total records for pagination
$totalQuery = "SELECT COUNT(*) FROM students";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRows = mysqli_fetch_array($totalResult)[0];
$totalPages = ceil($totalRows / $limit);

// Generate Pagination
$pagination = '';
for ($i = 1; $i <= $totalPages; $i++) {
    $pagination .= "<li class='page-item'><a class='page-link' href='#' onclick='loadStudents(\"$department\", $i)'>$i</a></li>";
}

echo json_encode(["students" => $students, "pagination" => $pagination]);
?>

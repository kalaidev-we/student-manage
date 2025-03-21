<?php
require 'db_config.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="students.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, array('ID', 'Name', 'Email'));

$result = $conn->query("SELECT id, name, email FROM students");
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>

<?php
$students = [
    "Alice Johnson" => 95,
    "Bob Smith" => 82,
    "Charlie Brown" => 77,
    "Diana Garcia" => 63,
    "Ethan Williams" => 88,
    "Fiona Davis" => 91,
    "George Miller" => 74
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grades Table</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
            font-family: Arial;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
    </style>
</head>
<body>

<h2>Student Grades</h2>

<!--
    The table must have:

        | Student Name | Grade | Remark |

        Rules:

        Grade ≥ 90 → Excellent

        80–89 → Good

        75–79 → Passing

        Below 75 → Failing

        Use a foreach loop to create <tr> rows dynamically.
    -->


<table>
    <tr>
        <th>Student Name</th>
        <th>Grade</th>
        <th>Remark</th>
    </tr>

    <?php
    foreach ($students as $name => $grade) {
        if ($grade >= 90) {
            $remark = "Excellent";
        } elseif ($grade >= 80) {
            $remark = "Good";
        } elseif ($grade >= 75) {
            $remark = "Passing";
        } else {
            $remark = "Failing";
        }

        // 4. Generate the row dynamically
        echo "<tr>";
        echo "<td>{$name}</td>";
        echo "<td>{$grade}</td>";
        echo "<td>{$remark}</td>";
        echo "</tr>";
    }
    ?>


</table>

<?php
    $average = array_sum($students) / count($students);
?>
<h3>Class Average: <?php echo number_format($average, 2); ?></h3>

</body>
</html>

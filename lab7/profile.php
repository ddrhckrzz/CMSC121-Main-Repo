<?php
// TODO: Define and initialize the following variables
// $firstName
// $lastName
// $age
// $course
// $gwa
$firstName = "John";
$lastName = "Doe";
$age = 25;
$course = "Computer Science";
$gwa = 1.65;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <style>
        .card {
            width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            font-family: Arial, sans-serif;
        }
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            margin-top: 10px;
        }

        .bg-blue {
            background-color: blue;
        }

        .bg-green {
            background-color: #27ae60;
        }
        
        .bg-gray {
            background-color: gray;
        }

        .distinction {
            background-color: #f1c40f;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="card">
    <h1><?php echo $firstName. " " . $lastName; ?></h1>
    <p>Course: <?= $course ?></p>
    <p>Age: <?= $age ?></p>
    <p>GWA: <?= $gwa ?></p>

    <?php
        if ($age < 18) {
            echo '<span class="badge bg-blue" >Minor</span>';
        } elseif ($age >= 18 && $age <= 59) {
            echo '<span class="badge bg-green" >Adult</span>';
        } else {
            echo '<span class="badge bg-gray" >Senior</span>';
        }
    ?>

    <?php
        if ($gwa <= 1.75) {
            echo '<div class="badge distinction">With Academic Distinction</div>';
        }
    ?>

</div>

</body>
</html>

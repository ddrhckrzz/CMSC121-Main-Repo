<?php
$products = [
    ["Laptop", 999.99, 10],
    ["Mouse", 25.50, 3],["Keyboard", 45.00, 5],
    ["Monitor", 199.99, 0],["Headphones", 79.99, 2],["USB Cables", 12.99, 15]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Catalog</title>
    <style>
        .container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .card {
            border: 1px solid #ccc;
            padding: 15px;
            width: 200px;
            border-radius: 8px;
        }
        h3 {
            margin-top: 0;
        }
    </style>
</head>
<body>

<h2>Product Catalog</h2>

<div class="container">
    <!--If stock = 0 → display:

    Out of Stock (in red)

    If stock ≤ 5 → display:

    Low Stock (orange)

    Otherwise:

    In Stock (green)
     -->

    <?php
    // 2. Loop through products
    foreach ($products as $item) {
        $name = $item[0];
        $price = $item[1];
        $stock = $item[2];

        // 3. Determine stock status and color
        if ($stock == 0) {
            $statusText = "Out of Stock";
            $statusColor = "red";
        } elseif ($stock <= 5) {
            $statusText = "Low Stock";
            $statusColor = "orange";
        } else {
            $statusText = "In Stock";
            $statusColor = "green";
        }
        ?>
        <div class="card">
            <h3><?= $name ?></h3>
            <p><strong>₱<?= number_format($price, 2) ?></strong></p>
            
            <!-- Apply dynamic text and style for stock -->
            <p style="margin-bottom: 0;">Stock: <?= $stock ?></p>
            <strong style="color: <?= $statusColor ?>;"><?= $statusText ?></strong>
        </div>
        <?php
    }
    ?>
</div>

</body>
</html>

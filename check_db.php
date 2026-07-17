<?php
    include 'c:\xampp\htdocs\trading\config.php';
    $r = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
    while($row = $r->fetch_assoc()) {
        print_r($row);
    }
    echo "ITEMS:\n";
    $r = $conn->query("SELECT * FROM order_items ORDER BY order_id DESC LIMIT 5");
    while($row = $r->fetch_assoc()) {
        print_r($row);
    }

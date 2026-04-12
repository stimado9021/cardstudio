<?php
// api_publica.php
require_once 'admin/includes/db.php';
header('Content-Type: application/json');




    $query = "SELECT * FROM diseños ORDER BY id_diseño DESC";
     var_dump($query);
    $res = mysqli_query($conn, $query);
     var_dump($res);
    $diseños = [];
    while($row = mysqli_fetch_assoc($res)) {
        $diseños[] = $row;
    }
    
    echo json_encode($diseños);




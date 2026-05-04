<?php
// api_publica.php
require_once 'admin/includes/db.php';
header('Content-Type: application/json');




    $query = "SELECT * FROM disenos ORDER BY id_diseno DESC";
     
    $res = mysqli_query($conn, $query);
    
    $disenos = [];
    while($row = mysqli_fetch_assoc($res)) {
        $disenos[] = $row;
    }
    
    echo json_encode($disenos);




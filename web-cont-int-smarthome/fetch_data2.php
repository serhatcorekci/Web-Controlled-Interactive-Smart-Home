<?php
$servername = "localhost";
$dbname = "u1703896_smarthome";
$username = "u1703896_espdata";
$password = "Smarthome123.";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id, sensor, value1, value2, value3, value4 FROM SensorData ORDER BY id DESC LIMIT 1";



if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {   
        $row_sensor = $row["sensor"];
        $row_value1 = $row["value1"];
        $row_value2 = $row["value2"]; 
        $row_value3 = $row["value3"];
        $row_value4 = $row["value4"];
        
       
        
       
        
        $values = array(
            "value1" => $row_value1,
            "value2" => $row_value2,
            "value3" => $row_value3,
            "value4" => $row_value4
        );
        
        echo json_encode($values);
       
    }
    $result->free();
}



$conn->close();
?>

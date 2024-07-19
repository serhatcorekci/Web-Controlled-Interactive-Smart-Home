<?php
$servername = "localhost";
$dbname = "u1703896_smarthome";
$username = "u1703896_espdata";
$password = "Smarthome123.";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT id, sensor, value1, value2, value3, value4, reading_time FROM SensorData ORDER BY id DESC LIMIT 20";

$output = '<table>';
$output .= '<tr>';
$output .= '<th>LDR SENSOR</th>';
$output .= '<th>RAIN SENSOR</th>';
$output .= '<th>GAS SENSOR</th>';
$output .= '<th>TEMP SENSOR</th>';
$output .= '<th>Reading Time</th>';
$output .= '</tr>';

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {   
        $row_sensor = $row["sensor"];
        $row_value1 = $row["value1"];
        $row_value2 = $row["value2"]; 
        $row_value3 = $row["value3"];
        $row_value4 = $row["value4"];
        $row_reading_time = $row["reading_time"];
       
        $output .= '<tr>';
        $output .= '<td>' . $row_value1 . '</td>';
        $output .= '<td>' . $row_value2 . '</td>';
        $output .= '<td>' . $row_value3 . '</td>';
        $output .= '<td>' . $row_value4 . '</td>';
        $output .= '<td>' . $row_reading_time . '</td>';
        $output .= '</tr>';
    }
    $result->free();
}

$output .= '</table>';
echo $output;

$conn->close();
?>

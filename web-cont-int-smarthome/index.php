<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
    
include_once('esp_database.php');

// Bu kısımda sensör verileri yer alacak, fetch_data2.php'nin içeriği buraya gelecek

$result = getAllOutputs();
$html_buttons = null;
$i=0;
if($result) {
    while($row = $result->fetch_assoc()) {
        // Determine the initial class based on the initial state fetched from the database
        $button_class = ($row["state"] == "1") ? "button-checked" : "";

        if ($row["id"] == 13) {
            $roow_id[] = '
            <button class="output-button button-checked" onclick="updateOutput(this)" id="' . $row["id"] . '">' . ($row["state"] == "1" ? 'ON' : 'OFF') . '</button>';
        } else {
            $roow_id[] = '
            <button class="output-button ' . $button_class . '" onclick="updateOutput(this)" id="' . $row["id"] . '">' . ($row["state"] == "1" ? 'ON' : 'OFF') . '</button>';
        }
    }
}

$result2 = getAllBoards();
$html_boards = null;
if ($result2) {
    $html_boards .= '<h3 >Boards</h3>';
    while ($row = $result2->fetch_assoc()) {
        //$row_reading_time = $row["last_request"];
        
        //$html_boards .= '<p><strong>Board ' . $row["board"] . '</strong> - Last Request Time: '. $row_reading_time . '</p>';
    }
}

   

?>


<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home Control Panel</title>
    <style>
        body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background-image: url(https://iot.eetimes.com/wp-content/uploads/2015/08/smart-home-2769210_1920.jpg);
    background-size: cover;
    background-repeat: no-repeat;
    color: #333;
}
        .container {
            display: grid;
            grid-template-columns: repeat(2, minmax(200px, 1fr)); 
            grid-gap: 50px;
            padding: 20px;
        }
        
        .box {
            position: relative; /* Relative position for absolute positioning of the images */
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: rgba(240, 240, 240, 0.9);
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 80px; 
            width: 250px; 
            margin-left: auto; 
            margin-right: auto; 
        }
        .box h2 {
            font-size: 20px;
            text-align: center;
            color: #555;
            margin-bottom: 10px;
        }
        .dropdown {
            position: relative;
            display: inline-block;
            margin-bottom: 10px;
            width: 100%;
        }
        .dropbtn {
            background-color: #4CAF50;
            color: white;
            padding: 8px;
            font-size: 14px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
            width: 100%;
        }
        .dropbtn:hover {
            background-color: #45a049;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            width: 100%;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            left: 0;
        }
        .dropdown-content a {
            color: black;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s;
            font-size: 14px;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .dropdown:hover .dropdown-content {
            display: block;
        }
        .output-button {
            width: 70px;
            height: 25px;
            border: none;
            cursor: pointer;
            outline: none;
            background-color: #ddd;
            color: #333;
            border-radius: 4px;
            transition: background-color 0.3s, color 0.3s;
        }
        .output-button:hover {
            background-color: #ccc;
        }
        .button-checked {
            /* No color change */
        }
        .sensor-data {
            margin-bottom: 10px;
        }
        .brand-image {
            position: absolute;
            top: 10px; /* Adjust as needed */
            right: 10px; /* Adjust as needed */
            width: 72px; /* Updated size */
            height: auto;
        }
        .lighting-image {
            position: absolute;
            top: 10px; /* Adjust as needed */
            left: 10px; /* Adjust as needed */
            width: 48px; /* Updated size */
            height: auto;
        }
        .gas-image {
            position: absolute;
            top: 10px; /* Adjust as needed */
            left: 10px; /* Adjust as needed */
            width: 45px; /* Updated size */
            height: auto;
        }
        .ac-image {
            position: absolute;
            top: 10px; /* Adjust as needed */
            right: 10px; /* Adjust as needed */
            width: 72px; /* Updated size */
            height: auto;
        } 
        
        
        /* Ekran boyutlarına göre düzenleme */
        @media only screen and (max-width: 600px) {
            .container {
        grid-template-columns: 1fr; /* Tek sütunlu düzen */
    }
    .box {
        width: 100%; /* kutuları tam genişlik yap */
        margin: 20px auto; /* Yan boşluklarını ayarla */
    }
    .brand-image {
        width: 60px; /* mrka resminin boyutunu ayarla */
    }
     body {
        background-position: center; /* arkaplan görselini ortala */
    }
}

        /* Küçük ekranlar için diğer düzenlemeler */
        @media only screen and (max-width: 400px) {
            .box {
                padding: 20px; /* daha küçük kutular için iç boşlukları azalt */
            }
            .dropbtn {
                font-size: 12px; /* seçenek düğmelerinin metin boyutunu küçült */
            }
            .output-button {
                width: 60px; /* cıkış düğmelerinin boyutunu ayarla */
                height: 20px;
                font-size: 12px; /* metin boyutunu küçült */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="box" id="1">
            <h2>LIGHTING</h2>
            <div class="dropdown"> <h3 style="text-align:center"> <div id="value1"></div> </h3>
                <button class="dropbtn">Bulb Controls</button>
                <div class="dropdown-content"><h3 style="text-align:center">
                   Close <?php echo $roow_id[0] ?><br><br>
                   Low <?php echo $roow_id[11] ?><br><br>
                   Medium <?php echo $roow_id[12] ?><br><br>
                   High <?php echo $roow_id[13] ?></h3>
                </div>
                
            </div>
            <img src="https://w7.pngwing.com/pngs/648/872/png-transparent-lightbulb-incandescent-light-bulb-computer-icons-lighting-lampada-angle-hand-lamp-thumbnail.png" alt="Lighting Control" class="lighting-image">
        </div>
        
        <div class="box" id="2">
            <h2>BRANDA CONTROL</h2>
            <div class="dropdown"> <h3 style="text-align:center"> <div id="value2"></div> </h3>
                <button class="dropbtn">Servo Controls</button>
                <div class="dropdown-content"><h3 style="text-align:center">
                Close    <?php echo $roow_id[14] ?><br><br>
                Slow    <?php echo $roow_id[15] ?><br><br>
                Medium    <?php echo $roow_id[16] ?><br><br>
                Fast    <?php echo $roow_id[17] ?></h3>
                </div>
              
            </div>
            <img src="https://w7.pngwing.com/pngs/629/164/png-transparent-rain-computer-icons-symbol-weather-rainy-day-love-text-cloud.png" alt="Branda Control" class="brand-image">
        </div>
        
        <div class="box" id="3">
            <h2>GAS ALARM</h2>
            <div class="dropdown"> <h3 style="text-align:center">  <div id="value3"></div> </h3>
                <button class="dropbtn">Fan Controls</button>
                <div class="dropdown-content"><h3 style="text-align:center">
                Close    <?php echo $roow_id[18] ?><br><br>
                Slow    <?php echo $roow_id[10] ?><br><br>
                Medium    <?php echo $roow_id[9] ?><br><br>
                Fast    <?php echo $roow_id[1] ?></h3>
                </div>
                
            </div>
            <div class="dropdown"> <h3 style="text-align:center">  <div id="value3"></div> </h3>
                <button class="dropbtn">Buzzer Controls</button>
                <div class="dropdown-content"><h3 style="text-align:center">
                 On   <?php echo $roow_id[8] ?><br><br>
                 Off  <?php echo $roow_id[3] ?></h3>
                </div>
                
            </div>
            <img src="https://w7.pngwing.com/pngs/783/874/png-transparent-natural-gas-computer-icons-petroleum-industry-industrial-gas-others-miscellaneous-logo-monochrome-thumbnail.png" alt="Gas Alarm" class="gas-image">
        </div>
        
        <div class="box" id="4">
            <h2>AIR CONDITIONING</h2>
            <div class="dropdown"><h3 style="text-align:center"><div id="value4"></div></h3>
            
                <button class="dropbtn">Fan Controls</button>
                <div class="dropdown-content"><h3 style="text-align:center">
                Close    <?php echo $roow_id[19] ?><br><br>
                Slow    <?php echo $roow_id[7] ?><br><br>
                Medium    <?php echo $roow_id[6] ?><br><br>
                Fast    <?php echo $roow_id[5] ?></h3>
                </div>
                 
            </div>
            <div class="dropdown"><h3 style="text-align:center"><div id="value4"></div></h3>
            
                <button class="dropbtn">Heater Controls</button>
                <div class="dropdown-content"> <h3 style="text-align:center">
                On    <?php echo $roow_id[4] ?> <br><br>
                Off    <?php echo $roow_id[2] ?> </h3>
                </div>
                 
            </div>
            <img src="https://w7.pngwing.com/pngs/751/930/png-transparent-computer-icons-air-conditioning-symbol-air-conditioner-miscellaneous-angle-text.png" alt="Air Conditioning" class="ac-image">
        </div>
        <div id="value1">
        <!-- Sensor data will be loaded here -->
    </div>
    </div>
    
<!--//////////////////////////////////////////////////////////////////////////////////////////////////-->        
<!--Sensor Data:-->


<script>
        // Function to fetch data using AJAX
        function fetchData() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                var responseData = JSON.parse(this.responseText);
                // Gelen verilerden istediğiniz değerleri alarak işleyebilirsiniz
                var value1 = responseData.value1;
                var value2 = responseData.value2;
                var value3 = responseData.value3;
                var value4 = responseData.value4;
                // Örneğin, bu değerleri ekrana yazdıralım
                document.getElementById("value1").innerText = "LDR SENSOR: " + value1;
                document.getElementById("value2").innerText = "RAIN SENSOR: " + value2;
                document.getElementById("value3").innerText = "GAS SENSOR: " + value3;
                document.getElementById("value4").innerText = "TEMP SENSOR: " + value4;
                    
                }
            };
            xhttp.open("GET", "fetch_data2.php", true);
            xhttp.send();
        }

        // Call fetchData function initially and set interval to refresh every 3 seconds
        fetchData();
        setInterval(fetchData, 3000); // Refresh every 3 seconds
    </script>
    
<!--//////////////////////////////////////////////////////////////////////////////////////////////////-->    
    
<script>

// Buton durumlarını saklamak için bir nesne oluşturuyoruz
var buttonStates = {};

window.onload = function() {
    updateAllOnButtons();
};

function updateAllOnButtons() {
    var buttons = document.querySelectorAll('.output-button');
    buttons.forEach(function(button) {
        if (button.innerText.trim() === 'ON') {
            updateOutput(button);
        }
    });
}

function updateOutput(element) {
    // Get the container of the clicked button
    var container = element.closest('.dropdown-content');
    
    // Get all buttons in the same container
    var buttons = container.querySelectorAll('.output-button');
    
    // Loop through each button in the container
    buttons.forEach(function(button) {
        // If the button is the clicked one, set its state to ON, otherwise set to OFF
        if (button === element) {
            button.innerText = 'ON';
        } else {
            button.innerText = 'OFF';
        }
    });

    // Send a request to update the state on the server
    var xhrOutput = new XMLHttpRequest();
    xhrOutput.onreadystatechange = function() {
        if (xhrOutput.readyState === XMLHttpRequest.DONE) {
            if (xhrOutput.status === 200) {
                console.log("State updated successfully!");
            } else {
                console.log("Failed to update state.");
                // Revert the button text if there was an error
                element.innerText = 'OFF';
            }
        }
    };

    xhrOutput.open("GET", "esp_outputs_action.php?action=output_update&id=" + element.id + "&state=1", true);
    xhrOutput.send();
}

// Function to handle button click event
function handleButtonClick(element) {
    // Get the container of the clicked button
    var container = element.closest('.dropdown-content');
    
    // Get all buttons in the same container
    var buttons = container.querySelectorAll('.output-button');
    
    // Loop through each button in the container
    buttons.forEach(function(button) {
        // If the button is the clicked one, set its state to ON, otherwise set to OFF
        if (button === element) {
            button.innerText = 'ON';
        } else {
            button.innerText = 'OFF';
        }
    });

    // Send a request to update the state on the server
    var xhrOutput = new XMLHttpRequest();
    xhrOutput.onreadystatechange = function() {
        if (xhrOutput.readyState === XMLHttpRequest.DONE) {
            if (xhrOutput.status === 200) {
                console.log("State updated successfully!");
            } else {
                console.log("Failed to update state.");
                // Revert the button text if there was an error
                element.innerText = 'OFF';
            }
        }
    };

    xhrOutput.open("GET", "esp_outputs_action.php?action=output_update&id=" + element.id + "&state=" + (element.innerText === 'ON' ? '1' : '0'), true);
    xhrOutput.send();
}

</script>


</body>
</html>
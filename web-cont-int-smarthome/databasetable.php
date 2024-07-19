<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        // Function to fetch data using AJAX
        function fetchData() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("sensorData").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "fetch_data.php", true);
            xhttp.send();
        }

        // Call fetchData function initially and set interval to refresh every 3 seconds
        fetchData();
        setInterval(fetchData, 3000); // Refresh every 3 seconds
    </script>
</head>
<body>
    <div id="sensorData">
        <!-- Sensor data will be loaded here -->
    </div>
</body>
</html>

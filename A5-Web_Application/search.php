<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <link rel="icon" href="img/plane.png" />
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0, maximum-scale=1, minimum-scale=1" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Tangerine">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
        <!-- Custom stylesheet -->
        <link rel="stylesheet" href="styles/airline.css">
        <link rel="stylesheet" href="styles/search_flights.css">
        <!-- Import jQuery -->
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
        <!-- Load components -->
        <script type="text/javascript">
            // header
            $(document).ready(function(){
                $('#header').load('components/header.html');
            });
        </script>
        <title>Airline Database</title>
    </head>
<body>
    <?php include 'connect_to_db.php'; ?>
    <div class="main_container">
        <div id="header"></div>
        <div class="page_name"> Search Results </div>
        
        <?php 
            if (empty($_GET["airline_code"]) || strlen($_GET["airline_code"]) != 2) {
                echo "Please enter a valid airline code";
            } else {
                $code=$_GET['airline_code'];
                $day=$_GET["days"];
                $result = $connection->query("SELECT Flight.AirlineCode, Flight.ThreeDigitNumber FROM Flight NATURAL JOIN DayOffered WHERE Flight.AirlineCode='$code' AND DayValue='$day' ");
                $result = $result->fetch();
                if (count($result) == 0) {
                    echo "No results found. Are you sure '$code' and '$day' are the correct airline code and day you are looking for?";
                } else {
                    echo "<table id='data'>
                            <tr>
                            <th>Airline</th>
                            <th>Flight Code</th>
                            <th>Departing Airport Location</th>
                            <th>Arriving Airport Location</th>
                        </tr>";
                    while ($row = $result) {
                        echo "<tr>";
                        echo "<td>".$row["AirlineCode"]."</td>";
                        // echo "<td>".$row["ThreeDigitNumber"]."</td>";
                        echo "</tr>";
                    }
                    echo "</table>";    
                }
                
            }
        ?>
    </div>
</body>
</html>
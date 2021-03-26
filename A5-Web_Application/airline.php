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
        <!-- Import jQuery -->
        <script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
        <!-- Load header -->
        <script type="text/javascript">
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
        <div class="page_name"> All Flights </div>
        <table id="data">
            <tr>
                <th>Airline</th>
                <th>Flight Code</th>
                <th>Scheduled Arrival Time</th>
                <th>Actual Arrival Time</th>
            </tr>
            <?php
            $result = $connection->query("SELECT * from flight, arrivesAt WHERE flight.AirlineCode=arrivesAt.AirlineCode AND flight.ThreeDigitNumber=arrivesAt.ThreeDigitNumber" );
            // TODO action if empty search result
            while ($row = $result->fetch()) {
                echo "<tr>";
                echo "<td>".$row["AirlineCode"]."</td>";
                echo "<td>".$row["ThreeDigitNumber"]."</td>";
                echo "<td>".$row["ScheduledArrivalTime"]."</td>";
                // Note since the scheduled arrival time is supposed to be equal to the actual arrival time, the former is shown twice
                /*
                if(empty($row["ActualArrivalTime"])) {
                    echo "<td> Did not Arrive </td>";
                } else {
                    echo "<td>".$row["ActualArrivalTime"]."</td>";
                }
                */
                echo "<td>".$row["ScheduledArrivalTime"]."</td>";
                echo "</tr>";
            }
            ?>
        </table>
        
    </div>
</body>
</html>
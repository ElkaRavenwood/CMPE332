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
        <link rel="stylesheet" href="styles/update_flight.css">
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
        <div class="page_name"> Update Flight </div>
        <form action="update_flight.php" class="update_flight">
            <h4>
                <?php
                    if (empty($_GET["flightInfo"]) || empty($_GET["departureTime"])) {
                        echo "Please fill out the form. You need to select a flight and enter a valid departure time";
                    } else {
                        $time = $_GET["departureTime"];
                        $pieces = explode(" ", $_GET["flightInfo"]);
                        $code = $pieces[0];
                        $number = $pieces[1];
                        try {
                            $result = $connection->query(<<<EOD
                                UPDATE
                                    DepartsFrom
                                SET
                                    ActualDepartureTime = "$time"
                                WHERE
                                    AirlineCode = "$code" AND ThreeDigitNumber = "$number"
                            EOD);
                            echo "Flight updated!";
                        } catch (PDOException $e) {
                            echo "Could not update flight. Please try again.";
                        }
                    }
                ?>
            </h4>
            <h2>Select a Flight </h2>
            <table id="data">
                <tr>
                    <th>Select</th>
                    <th>Airline</th>
                    <th>Flight Code</th>
                    <th>Current Departure Time</th>
                    <th>Actual Departure Time</th>
                </tr>
                <?php
                    $flightCount = 0;
                    $result = $connection->query(<<<EOD
                        SELECT
                            *
                        FROM
                            Flight
                        JOIN DepartsFrom ON Flight.AirlineCode = DepartsFrom.AirlineCode AND Flight.ThreeDigitNumber = DepartsFrom.ThreeDigitNumber
                    EOD);
                    $savedFlightInfo = $_GET["flightInfo"];
                    while ($row = $result->fetch()) {
                        $airlineCode = $row["AirlineCode"];
                        $flightNumber = $row["ThreeDigitNumber"];
                        $combined = "$airlineCode $flightNumber";
                        // echo $combined;
                        $time = empty($row["ActualDepartureTime"]) ? "Did not depart" : $row["ActualDepartureTime"];
                        echo "<tr>";
                        if (($flightCount == 0 && empty($savedFlightInfo)) || $savedFlightInfo == $combined) {
                            echo <<<EOD
                                <td><input value="$combined" id="$combined" name="flightInfo" type="radio" CHECKED/></td>
                            EOD;
                        } else {
                            echo <<<EOD
                                <td><input value="$combined" id="$combined" name="flightInfo" type="radio" /></td>
                            EOD;
                        }
                        echo "<td>".$row["AirlineCode"]."</td>";
                        echo "<td>".$row["ThreeDigitNumber"]."</td>";
                        echo "<td>".$row["ScheduledDepartureTime"]."</td>";
                        echo "<td>".$time."</td>";
                        echo "</tr>";
                        $flightCount ++;
                    }
                    echo "</table>";
                    if ($flightCount == 0) {
                        echo "<h4>No flights available. You can't update it!</h4>";
                    }
                ?>
            <div class="display_flex jc_space_between">
                <h2>Enter a time</h2>
                <input id="search" class="update_flight_time" type="text" name="departureTime" placeholder="Type here">
                <button type="submit" class="search-submit add_flight_submit" class="btn btn-success" method="get">
                    Update Flight
                </button>
            </div>
        </form>
    </div>
</body>
</html>
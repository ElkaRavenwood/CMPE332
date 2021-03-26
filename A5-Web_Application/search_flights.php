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
        $(document).ready(function() {
            $('#header').load('components/header.html');
        });
    </script>
    <title>Airline Database</title>
</head>

<body>
    <?php include 'connect_to_db.php'; ?>
    <div class="main_container">
        <div id="header"></div>
        <div class="page_name"> Search Flights </div>
        <form action="/search_flights.php" class="search_airline" id="search_airline">
            <label for="search" class="search-descriptors">Enter an airline code:</label>
            <input id="search" class="search-text" type="text" name="airline_code" placeholder="Type here">
            <label for="search" class="search-descriptors">Select a day: </label>
            <select id="days" name="days">
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <button type="submit" class="search-submit" class="btn btn-success" method="get">
                <i class="material-icons">search</i>
            </button>
        </form>
        <?php
        if (empty($_GET["airline_code"]) || strlen($_GET["airline_code"]) != 2) {
            echo "Please enter a valid airline code";
        } else {
            $code = $_GET['airline_code'];
            $day = $_GET["days"];
            // $result = $connection->query("SELECT Flight.AirlineCode as AirlineCode, Flight.ThreeDigitNumber as ThreeDigitNumber FROM Flight JOIN DayOffered ON Flight.AirlineCode=DayOffered.AirlineCode WHERE Flight.AirlineCode='$code' AND DayValue='$day' ");
            $result = $connection->query(<<<EOD
            SELECT
                Flight.AirlineCode AS AirlineCode,
                Flight.ThreeDigitNumber AS ThreeDigitNumber,
                ArrivalAirport.City AS ArrivalCity,
                DepartureAirport.City AS DepartureCity,
                DayValue
            FROM
                Flight
            JOIN DepartsFrom ON Flight.AirlineCode = DepartsFrom.AirlineCode AND Flight.ThreeDigitNumber = DepartsFrom.ThreeDigitNumber
            JOIN ArrivesAt ON Flight.AirlineCode = ArrivesAt.AirlineCode AND Flight.ThreeDigitNumber = ArrivesAt.ThreeDigitNumber
            JOIN Airport AS DepartureAirport
            ON
                DepartsFrom.AirportCode = DepartureAirport.AirportCode
            JOIN Airport AS ArrivalAirport
            ON
                ArrivesAt.AirportCode = ArrivalAirport.AirportCode
            JOIN DayOffered 
            ON 
                Flight.AirlineCode = DayOffered.AirlineCode AND Flight.ThreeDigitNumber = DayOffered.ThreeDigitNumber
            WHERE DayOffered.DayValue="$day" AND Flight.AirlineCode="$code"
        EOD);
            $result_fetched = $result->fetch();
            if (empty($result_fetched)) {
                echo "No results found. Are you sure '$code' and '$day' are the correct airline code and day you are looking for?";
            } else {
                echo "<table id='data'>
                            <tr>
                            <th>Airline</th>
                            <th>Flight Code</th>
                            <th>Departing Airport Location</th>
                            <th>Arriving Airport Location</th>
                        </tr>";
                echo "<tr>";
                echo "<td>" . $result_fetched["AirlineCode"] . "</td>";
                echo "<td>". $result_fetched["ThreeDigitNumber"]."</td>";
                echo "<td>". $result_fetched["DepartureCity"]."</td>";
                echo "<td>". $result_fetched["ArrivalCity"]."</td>";
                echo "</tr>";
                while ($row = $result->fetch()) {
                    echo "<tr>";
                    echo "<td>" . $row["AirlineCode"] . "</td>";
                    echo "<td>". $row["ThreeDigitNumber"]."</td>";
                    echo "<td>". $row["DepartureCity"]."</td>";
                    echo "<td>". $row["ArrivalCity"]."</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
        ?>
    </div>
</body>

</html>
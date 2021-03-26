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
    <link rel="stylesheet" href="styles/average_seats.css">
    <!-- Import jQuery -->
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.5.1.min.js"></script>
    <!-- Load components -->
    <script type="text/javascript">
        // header
        $(document).ready(function() {
            $('#header').load('components/header.html');
        });
        // $("#search_airline").submit(function(e) {
        //     e.preventDefault();
        // });
    </script>
    <title>Airline Database</title>
</head>

<body>
    <?php include 'connect_to_db.php'; ?>
    <div class="main_container">
        <div id="header"></div>
        <div class="page_name"> Average Seats </div>
        <form action="/average_seats.php" style="text-align: center;" class="average_seats" >
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
            <button type="submit" class="search-submit " method="get" style="text-align: center;" >
                <i class="material-icons">calculate</i>
                Calculate
                <i class="material-icons">calculate</i>
            </button>
        </form>
        <?php
        if (empty($_GET["days"])) {
            echo "Please select a day";
        } else {
            $day = $_GET["days"];
            $result = $connection->query(<<<EOD
                SELECT
                    Flight.AirlineCode,
                    Flight.ThreeDigitNumber,
                    AirplaneType.MaxSeats,
                    DayOffered.DayValue
                FROM
                    Flight
                JOIN DayOffered ON Flight.AirlineCode = DayOffered.AirlineCode AND Flight.ThreeDigitNumber = DayOffered.ThreeDigitNumber
                JOIN Airplane ON Flight.AirplaneId = Airplane.AirplaneId
                JOIN AirplaneType ON AirplaneType.AirplaneTypeName = Airplane.AirplaneTypeName
                WHERE DayOffered.DayValue="$day"
            EOD);
            $sum = 0;
            $count = 0;
            while ($row = $result->fetch()) {
                echo "<br />";
                $sum = $sum + $row["MaxSeats"];
                $count = $count + 1;
            }
            if ($count == 0) {
                $average = 0;
            } else {
                $average = $sum / $count;
            }
            echo "<div style='text-align: center;'>The average number of seats per flight on $day is $average </div>";
        }
        ?>
    </div>
</body>

</html>
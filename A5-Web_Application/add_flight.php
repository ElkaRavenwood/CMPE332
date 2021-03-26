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
    <link rel="stylesheet" href="styles/add_flight.css">
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
    <div class="main_container" style="height: unset;">
        <div id="header"></div>
        <div class="page_name"> Add Flight </div>
        <form action="/add_flight.php" class="add_flight" id="add_flight">
            <div class="jc_space_between">
                <?php 
                    if (empty($_GET["flight_number"]) || empty($_GET["airline"]) || empty($_GET["DaysOffered"]) || empty($_GET["arrivalAirport"]) || empty($_GET["departureAirport"]) || empty($_GET["airplane"]) || empty($_GET["arrivalTime"]) || empty($_GET["departureTime"])) {
                        echo "Please fill out the form. Remember to click the arrow after selecting an airline once. The following fields are missing: <br /> <ul class='two_column'>";
                        if (empty($_GET["airline"])) {
                            echo "<li>An Airline</li>";
                        }
                        if (empty($_GET["DaysOffered"])) {
                            echo "<li>At least one day offered </li>";
                        }
                        if (empty($_GET["arrivalAirport"])) {
                            echo "<li>The Arrival Airport</li>";
                        }
                        if (empty($_GET["departureAirport"])) {
                            echo "<li>The Departure Airport</li>";
                        }
                        if (empty($_GET["airplane"])) {
                            echo "<li>An Airplane</li>";
                        }
                        if (empty($_GET["flight_number"])) {
                            echo "<li>A Flight Number</li>";
                        }
                        if (empty($_GET["arrivalTime"])) {
                            echo "<li>An Arrival Time</li>";
                        }
                        if (empty($_GET["departureTime"])) {
                            echo "<li>A Departure Time</li>";
                        }
                        echo "</ul>";
                    } else if ((strlen($_GET["flight_number"]) > 0 && strlen($_GET["flight_number"]) != 3) || intval($_GET["flight_number"]) == 0) {
                        echo "Please enter a valid flight number. It must be a 3 digit number";
                    } else {
                        echo "All form fields are correctly filled out";
                        try {
                            $flight_number = $_GET["flight_number"];
                            $airline = $_GET["airline"];
                            $airplane = $_GET["airplane"];
                            $arrival = $_GET["arrivalAirport"];
                            $departure = $_GET["departureAirport"];
                            $arrivalTime = $_GET["arrivalTime"];
                            $departureTime = $_GET["departureTime"];
                            
                            $flightSuccess = $connection->query(<<<EOD
                                INSERT INTO Flight VALUES ("$flight_number", "$airline", "$airplane");
                            EOD);
                            $departSuccess = $connection->query(<<<EOD
                                INSERT INTO DepartsFrom VALUES ("$airline", "$flight_number", "$departure", "$departureTime", null);
                            EOD);
                            $arrivalSuccess = $connection->query(<<<EOD
                                INSERT INTO ArrivesAt VALUES ("$airline", "$flight_number", "$arrival", "$arrivalTime", null);
                            EOD);

                            $days = $_GET["DaysOffered"];
                            $daySuccess = true;
                            foreach( $days as $key => $n ) {
                                $temp = $connection->query(<<<EOD
                                    INSERT INTO DayOffered VALUES ("$airline", "$flight_number", "$n");
                                EOD);
                                if (!$temp) $daySuccess = $temp;
                            }

                            if ($flightSuccess && $daySuccess && $arrivalSuccess && $departSuccess) {
                                echo " and our flight was created";
                            }

                        } catch (PDOException $e) {
                            echo "but there was an error". $e->getMessage()." ";
                            if (str_contains($e->getMessage(), "23000")) {
                                echo ": this flight number already exists with the airline.";
                            }
                            echo "Please try again.";
                            die();
                        }
                    }
                ?>
            </div>
            <div class="display_flex jc_space_between">
                <div>
                    <h2>Select an airline</h2>
                    <div class="options add_flight_airplane">
                        <?php
                            $airlines = $connection->query("SELECT AirlineCode, AirlineName FROM Airline");
                            $airlineCount = 0;
                            $savedCode = $_GET["airline"];
                            while ($row = $airlines->fetch()) {
                                $airlineCode = $row["AirlineCode"];
                                $airlineName = $row["AirlineName"];
                                // echo $airlineCode;
                                if ((empty($savedCode) && $airlineCount == 0) || $airlineCode == $savedCode) {
                                    echo <<<EOD
                                        <input type="radio" id="$airlineCode" name="airline" value="$airlineCode" CHECKED/>
                                        <label for="$airlineCode" >$airlineName ($airlineCode)</label><br> 
                                    EOD;
                                } else {
                                    echo <<<EOD
                                    <input type="radio" id="$airlineCode" name="airline" value="$airlineCode" />
                                    <label for="$airlineCode" >$airlineName ($airlineCode)</label><br> 
                                    EOD;
                                }
                                $airlineCount ++;
                            }
                            if ($airlineCount == 0) echo "No Airlines Available - you cannot create a flight";
                        ?>
                    </div>
                </div>
                <button type="submit" class="search-submit add_flight_select_airline" class="btn btn-success" method="get">
                    <img src="./img/right_arrow.png" class="add_flight_arrow"/>
                    <br/>
                    Select Airline
                </button>
                
                <div class="add_flight_airplane">
                    <div class="display_flex">
                        <h2>Select an Airplane</h2>
                    </div>
                    <div class="options add_flight_airplane">
                        <?php
                            $code = $_GET["airline"];
                            if (empty($code)) {
                                echo "No airline selected. Select an airline then click the arrow.";
                            } else {
                                $savedPlane = $_GET["airplane"];
                                $airplanes = $connection->query("SELECT AirplaneId, AirplaneTypeName FROM Airplane WHERE AirlineCode='$code'");
                                $airplaneCount = 0;
                                while ($row = $airplanes->fetch()) {
                                    $airplaneId = $row["AirplaneId"];
                                    $airplaneTypeName = $row["AirplaneTypeName"];
                                    if ((empty($savedPlane) && $airplaneCount == 0) || $airplaneId == $savedPlane) {
                                        echo <<<EOD
                                            <input type="radio" id="$airplaneId" name="airplane" value="$airplaneId" CHECKED/>
                                            <label for="$airplaneId" >$airplaneTypeName ($airplaneId)</label><br> 
                                        EOD;
                                    } else {
                                        echo <<<EOD
                                        <input type="radio" id="$airplaneId" name="airline" value="$airplaneId" />
                                        <label for="$airplaneId" >$airplaneTypeName ($airplaneId)</label><br> 
                                        EOD;
                                    }
                                    $airplaneCount ++;
                                }
                                if ($airplaneCount = 0) echo "No Airplanes Available - you cannot create a flight";
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="display_flex jc_space_between">
                <div>
                    <h2>Select your departure airport</h2>
                    <div class="options">
                        <?php
                            $airports = $connection->query("SELECT AirportCode, AirportName FROM Airport");
                            $airportCount = 0;
                            $savedAirport = $_GET["departureAirport"];
                            while ($row = $airports->fetch()) {
                                $airportCode = $row["AirportCode"];
                                $airportName = $row["AirportName"];
                                if ((empty($savedAirport) && $airportCount == 0) || $airportCode == $savedAirport) {
                                    echo <<<EOD
                                        <input type="radio" id="$airportCode" name="departureAirport" value="$airportCode" CHECKED>
                                        <label for="$airportCode" >$airportName ($airportCode)</label><br> 
                                    EOD;
                                } else {
                                    echo <<<EOD
                                    <input type="radio" id="$airportCode" name="departureAirport" value="$airportCode">
                                    <label for="$airportCode" >$airportName ($airportCode)</label><br> 
                                    EOD;
                                }
                                $airportCount ++;
                            }
                            if ($airportCount == 0) echo "No Airports Available - you cannot create a flight";
                        ?>
                    </div>
                </div>
                <div>
                    <h2>Select your arrival airport</h2>
                    <div class="options">
                        <?php
                            $airports = $connection->query("SELECT AirportCode, AirportName FROM Airport");
                            $airportCount = 0;
                            $savedAirport = $_GET["arrivalAirport"];
                            while ($row = $airports->fetch()) {
                                $airportCode = $row["AirportCode"];
                                $airportName = $row["AirportName"];
                                if ((empty($savedAirport) && $airportCount == 0) || $airportCode == $savedAirport) {
                                    echo <<<EOD
                                    <input type="radio" id="$airportCode" name="arrivalAirport" value="$airportCode" CHECKED>
                                    <label for="$airportCode" >$airportName ($airportCode)</label><br> 
                                    EOD;
                                } else {
                                    echo <<<EOD
                                    <input type="radio" id="$airportCode" name="arrivalAirport" value="$airportCode">
                                    <label for="$airportCode" >$airportName ($airportCode)</label><br> 
                                    EOD;
                                }
                                $airportCount ++;
                            }
                            if ($airportCount == 0) echo "No Airports Available - you cannot create a flight";
                        ?>
                    </div>
                </div>
            </div>

            <div class="jc_space_between display_flex">
                <h2 style="font-size: 2em;">Schedule your arrival time</h2>
                <?php
                    $arrivalTime = $_GET["arrivalTime"];
                    if (empty($arrivalTime)) {
                        echo <<<EOD
                            <input id="search" class="add_flight_code" type="text" name="arrivalTime" placeholder="Type here">
                        EOD;
                    } else {
                        echo <<<EOD
                            <input id="search" class="add_flight_code" type="text" name="arrivalTime" value="$arrivalTime">
                        EOD;
                    }
                ?>
                <h2 style="font-size: 2em;">Schedule your departure time</h2>
                <?php
                    $departureTime = $_GET["departureTime"];
                    if (empty($departureTime)) {
                        echo <<<EOD
                            <input id="search" class="add_flight_code" type="text" name="departureTime" placeholder="Type here">
                        EOD;
                    } else {
                        echo <<<EOD
                            <input id="search" class="add_flight_code" type="text" name="departureTime" value="$departureTime">
                        EOD;
                    }
                ?>
            </div>
            
            <h2>Select a number of days: </h2>
            <div class="options display_flex jc_space_between">
                <?php
                    $days = $_GET["DaysOffered"];
                    if (empty($days) || !in_array("Monday", $days)) {
                        echo <<<EOD
                            <input type="checkbox" id="Monday" name="DaysOffered[]" value="Monday" >
                            <label for="Monday">Monday</label><br>
                        EOD;
                    } else {
                        echo <<<EOD
                            <input type="checkbox" id="Monday" name="DaysOffered[]" value="Monday" CHECKED>
                            <label for="Monday">Monday</label><br>
                        EOD;
                    }
                    if (empty($days) || !in_array("Tuesday", $days)) {
                        echo <<<EOD
                            <input type="checkbox" id="Tuesday" name="DaysOffered[]" value="Tuesday">
                            <label for="Tuesday">Tuesday</label><br>
                        EOD;
                    } else {
                        echo <<<EOD
                            <input type="checkbox" id="Tuesday" name="DaysOffered[]" value="Tuesday" CHECKED>
                            <label for="Tuesday">Tuesday</label><br>
                        EOD;
                    }
                    if (empty($days) || !in_array("Wednesday", $days)) {
                        echo <<<EOD
                            <input type="checkbox" id="Wednesday" name="DaysOffered[]" value="Wednesday">
                            <label for="Wednesday">Wednesday</label><br>
                        EOD;
                    } else {
                        echo <<<EOD
                            <input type="checkbox" id="Wednesday" name="DaysOffered[]" value="Wednesday" CHECKED>
                            <label for="Wednesday">Wednesday</label><br>
                        EOD;
                    }
                    if (empty($days) || !in_array("Thursday", $days)) {
                        echo <<<EOD
                            <input type="checkbox" id="Thursday" name="DaysOffered[]" value="Thursday">
                            <label for="Thursday">Thursday</label><br>
                        EOD;
                    } else {
                        echo <<<EOD
                            <input type="checkbox" id="Thursday" name="DaysOffered[]" value="Thursday" CHECKED>
                            <label for="Thursday">Thursday</label><br>
                        EOD;
                    }
                    if (empty($days) || !in_array("Friday", $days)) {
                        echo <<<EOD
                            <input type="checkbox" id="Friday" name="DaysOffered[]" value="Friday">
                            <label for="Friday">Friday</label><br>
                        EOD;
                    } else {
                        echo <<<EOD
                            <input type="checkbox" id="Friday" name="DaysOffered[]" value="Friday" CHECKED>
                            <label for="Friday">Friday</label><br>
                        EOD;
                    }

                    if (empty($days) || !in_array("Saturday", $days)) {
                        echo <<<EOD
                            <input type="checkbox" id="Saturday" name="DaysOffered[]" value="Saturday">
                            <label for="Saturday">Saturday</label><br>
                        EOD;
                    } else {
                        echo <<<EOD
                            <input type="checkbox" id="Saturday" name="DaysOffered[]" value="Saturday" CHECKED>
                            <label for="Saturday">Saturday</label><br>
                        EOD;
                    }
                    if (empty($days) || !in_array("Sunday", $days)) {
                        echo <<<EOD
                            <input type="checkbox" id="Sunday" name="DaysOffered[]" value="Sunday">
                            <label for="Sunday">Sunday</label><br>
                        EOD;
                    } else {
                        echo <<<EOD
                            <input type="checkbox" id="Sunday" name="DaysOffered[]" value="Sunday" CHECKED>
                            <label for="Sunday">Sunday</label><br>
                        EOD;
                    }
                ?>
            </div>
            <div class="display_flex jc_space_between" style="margin-bottom: 2vh;">
                <div style="width: 30vw;" class="display_flex jc_space_between">
                    <h2>Enter a flight number:</h2>
                    <input id="search" class="add_flight_code" type="text" name="flight_number" placeholder="Type here">
                </div>
                <button type="submit" class="search-submit add_flight_submit" class="btn btn-success" method="get">
                    Create Flight
                </button>
            </div>
            
        </form>
    </div>
</body>

</html>
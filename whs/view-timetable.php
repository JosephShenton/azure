<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    date_default_timezone_set("Australia/Sydney");
    include 'timetable.class.php';

    $timetable = json_decode(Timetable::getTimetableJSON($_GET['username'], $_GET['password']) , true);

    if (isset($_GET['method']) && !empty($_GET['method']) && $_GET['method'] == "today") {
        foreach ($timetable as $key => $information) {
            if (Timetable::filterArray($information['period_start'], date('d/m/Y', time()))) {

            }
            else {
                unset($timetable[$key]);
            }
        }
    }

    $timetable = json_encode(array_values($timetable) , JSON_PRETTY_PRINT);

    if (Timetable::isWeekend(time())) {
        $timetable = '[
        {
            "period_start": "30\/01\/2018 12:00 AM",
            "fetch_time": "11\/02\/2018 12:00 AM",
            "period_end": "30\/01\/2018 11:59 PM",
            "UID": "WEEKEND",
            "teacher": "WEEKEND",
            "period": "WEEKEND",
            "class": "WEEKEND: WEEKEND",
            "year": "WEEKEND",
            "room": "WEEKEND"
        }
    ]';

        $timetable = json_decode(json_encode($timetable, JSON_PRETTY_PRINT) , true);
    }

    echo print_r($timetable, JSON_PRETTY_PRINT);

?>

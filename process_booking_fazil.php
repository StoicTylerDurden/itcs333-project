<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_selected = $_POST["date"];
    $stime_selected = $_POST["stime"];
    $etime_selected = $_POST["etime"];

    // Check if the date is empty
    if (empty($date_selected)) {
        $error_message = "Error: Date cannot be empty. Please select a date.";
    } else {
        $is_time_valid = true; // Flag to check if the time slot is valid

        // Check the selected date and time against existing bookings in the session array
        if (isset($_SESSION['Array_booked']) && !empty($_SESSION['Array_booked'])) {
            foreach ($_SESSION["Array_booked"] as $time_booked) {
                $booked_date = $time_booked["date"]; // Get the booked date
                $stime_booked = $time_booked["stime"];
                $etime_booked = $time_booked["etime"];

                // Check if the selected date matches a booked date
                if ($date_selected == $booked_date) {
                    // Check for time overlap on the same date
                    if (
                        ($stime_selected >= $stime_booked && $stime_selected < $etime_booked) || // Start time overlap
                        ($etime_selected > $stime_booked && $etime_selected <= $etime_booked) || // End time overlap
                        ($stime_selected <= $stime_booked && $etime_selected >= $etime_booked)   // Complete overlap
                    ) {
                        $error_message = "Error: Selected time slot overlaps with an already booked slot on the same date.";
                        $is_time_valid = false;
                        break;
                    }
                }
            }
        }

        if ($is_time_valid) {
            // Add the new booking to the session array
            $_SESSION["Array_booked"][] = [
                "date" => $date_selected,
                "stime" => $stime_selected,
                "etime" => $etime_selected
            ];
            $success_message = "Booking successful! Selected Date: $date_selected, Start Time: $stime_selected, End Time: $etime_selected.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .booking-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        .booking-title {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #343a40;
        }

        .alert {
            font-size: 1.1rem;
            font-weight: bold;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container booking-container">
        <div class="booking-title">Room Booking Confirmation</div>

        <!-- Display messages -->
        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($success_message)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Booking form -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="date">Select Date</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo $date_selected ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="stime">Starting Time</label>
                <input type="text" class="form-control" id="stime" name="stime" value="<?php echo $stime_selected ?? ''; ?>" placeholder="e.g. 08:00 AM" required>
            </div>
            <div class="form-group">
                <label for="etime">Ending Time</label>
                <input type="text" class="form-control" id="etime" name="etime" value="<?php echo $etime_selected ?? ''; ?>" placeholder="e.g. 09:00 AM" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Submit Booking</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCX9Rkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
</body>

</html>

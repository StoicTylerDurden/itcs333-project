<!-- I took this from bootstrap -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <style>
    body {
      padding-top: 70px;
      /* Add spacing between the navbar and content */
    }

    .navbar {
      position: sticky;
      top: 0;
      z-index: 1030;
    }

    .navbar-nav {
      margin-left: auto;
      /* Push links to the right */
    }

    .nav-link {
      transition: color 0.3s ease, background-color 0.3s ease;
    }

    .nav-link:hover {
      color: #ffc107;
      /* Change the text color on hover */
      background-color: rgba(255, 255, 255, 0.1);
      /* Subtle background effect */
      border-radius: 5px;
      /* Add rounded edges for a polished look */
    }

    :root {
      --primary-color: #002060;
      /* Navy Blue */
      --secondary-color: #D4AF37;
      /* Gold */
      --background-color: #F8F9FA;
      /* Light Gray */
      --text-color: #002060;
      /* Navy Blue */
      --accent-color: #FFFFFF;
      /* White */
    }

    /* General Body Styling */
    body {
      font-family: 'Arial', sans-serif;
      background-color: var(--background-color);
      color: var(--text-color);
      margin: 0;
      padding: 0;
    }

    /* Navbar */
    .navbar {
      background-color: var(--primary-color) !important;
    }

    .navbar-brand,
    .nav-link {
      color: var(--accent-color) !important;
    }

    .nav-link:hover {
      color: var(--secondary-color) !important;
    }
    
  </style>

</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <a class="navbar-brand" href="View_room_fazil.php">Room Booking</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="View_room_fazil.php">Browse Rooms</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="account.php">Account</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </nav>
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

</body>

</html>
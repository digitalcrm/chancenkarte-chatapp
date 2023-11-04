
<?php

 session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['admin_id'])){
    header("location: admin_login.php");
  }

function getTotalOnlineUsers() {
  $hostname = "localhost";
  $username = "root";
  $password = "";
  $dbname = "chatapp";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $onlineStatus = 'Active now';
    $sql = "SELECT COUNT(*) AS total_online FROM users WHERE status = '$onlineStatus'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_online'];
    } else {
        return "Error: " . mysqli_error($conn);
    }
    mysqli_close($conn);
	
}	
	function getTotalVisitors() {

  $hostname = "localhost";
  $username = "root";
  $password = "";
  $dbname = "chatapp";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
    $onlineStatus = 'Active now';
    $sql = "SELECT COUNT(*) AS total_visitors FROM users";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_visitors'];
    } else {
        return "Error: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}


function getOfflineUsers() {

  $hostname = "localhost";
  $username = "root";
  $password = "";
  $dbname = "chatapp";

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
    $onlineStatus = 'Offline now';
    $sql = "SELECT COUNT(*) AS total_offlines FROM users WHERE status = '$onlineStatus'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_offlines'];
    } else {
        return "Error: " . mysqli_error($conn);
    }
    mysqli_close($conn);
}

function displayRecentChats() {
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chatapp";

    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    if (!$conn) {
        echo "Database connection error" . mysqli_connect_error();
    }
    $sql = "SELECT u.fname, u.lname, 
            SEC_TO_TIME(TIME_TO_SEC(NOW()) - TIME_TO_SEC(u.logout_time)) AS logout_duration,
            DATE(u.logout_time) AS logout_date, TIME(u.logout_time) AS logout_time
            FROM users AS u
            WHERE u.status = 'Offline now' ORDER BY u.logout_time DESC";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo '<table class="mb-3">';
        echo '<tr>';
        echo '<th>Name</th>';
        echo '<th>Logout Duration</th>';
        echo '<th>Logout Date</th>';
		echo '<th>Logout Time</th>';
        echo '</tr>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['fname'] . ' ' . $row['lname'] . '</td>';
            echo '<td>' . $row['logout_duration'] . ' ago</td>';
            echo '<td>' . $row['logout_date'] . '</td>';
			echo '<td>' . $row['logout_time'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}


function getCountryCode($countryName) {
    // You can use an array like $countryCodes to map country names to country codes
    $countryCodes = array(
        "Afghanistan" => "AF",
        "Albania" => "AL",
		"India" => "IN",
		"Canada" => "CA",
        // Add more country mappings here...
    );

    if (array_key_exists($countryName, $countryCodes)) {
        return $countryCodes[$countryName];
    } else {
        return false; // Return false if country code is not found
    }
}



function getIPGeolocation($ip) {
    $apiUrl = "http://ip-api.com/json/{$ip}";
    $response = file_get_contents($apiUrl);
    return json_decode($response, true);
}

function displayOnlineVisitors() {
    // Initialize your database connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chatapp";

    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    if (!$conn) {
        echo "Database connection error: " . mysqli_connect_error();
    }

    // Define the SQL query to retrieve online and offline visitors with status and time
    $sql = "SELECT u.fname, u.lname, u.country, u.status, u.login_time, u.logout_time, u.user_ip
            FROM users AS u
            WHERE u.status IN ('Active now', 'Offline now') ORDER BY u.login_time DESC";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo '<table class="mb-3">';
        echo '<tr>';
        echo '<th>Name</th>';
        echo '<th>Location</th>';
        echo '<th>Status</th>';
        echo '<th>Login Time</th>';
        echo '<th>Logout Time</th>';
        echo '</tr>';
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['fname'] . ' ' . $row['lname'] . '</td>';
        
            $locationInfo = getIPGeolocation($row['user_ip']);
            
            if ($locationInfo && $locationInfo['status'] === 'success') {
                $location = $locationInfo['city'] . ', ' . $locationInfo['country'];
                echo '<td>' . $location;
        
                // Convert the country name to a country flag logo using Flags API
                $countryCode = getCountryCode($locationInfo['country']); // Implement getCountryCode function
        
                if ($countryCode) {
                    echo ' <img src="https://flagsapi.com/' . $countryCode . '/shiny/32.png" alt="' . $locationInfo['country'] . '">';
                }
        
                echo '</td>';
            } else {
                echo '<td>Location not available</td>';
            }
        
            echo '<td>' . $row['status'] . '</td>';
            echo '<td>' . $row['login_time'] . '</td>';
            echo '<td>' . $row['logout_time'] . '</td>';
            echo '</tr>';
        }
        

echo '</table>';

    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}






function displayLiveChats() {
    // Initialize your database connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chatapp";

    $conn = mysqli_connect($hostname, $username, $password, $dbname);
    if (!$conn) {
        echo "Database connection error" . mysqli_connect_error();
    }

    // Define the SQL query to retrieve users who are currently chatting and their login duration
    $sql = "SELECT u.fname, u.lname, DATE(u.login_time) AS login_date, TIME(u.login_time) AS login_time,
            SEC_TO_TIME(TIME_TO_SEC(NOW()) - TIME_TO_SEC(u.login_time)) AS login_duration
            FROM users AS u
            WHERE u.status = 'Active now' ORDER BY u.login_time DESC";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo '<table class="mb-3">';
        echo '<tr>';
        echo '<th>Name</th>';
        echo '<th>Login Duration</th>';
		 echo '<th>Login Date</th>';
		  echo '<th>Login Time</th>';
        echo '</tr>';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['fname'] . ' ' . $row['lname'] . '</td>';
            echo '<td>' . $row['login_duration'] . ' ago</td>';
			echo '<td>' . $row['login_date'] . '</td>';
			echo '<td>' . $row['login_time'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    // Close the database connection
    mysqli_close($conn);
}







?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Page</title>
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }

      .bd-mode-toggle {
        z-index: 1500;
      }

      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }
    </style>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
			
        }

        h1 {
            background-color: #3b4382;
            color: #fff;
            text-align: center;
            padding: 20px 0;
			margin: 0px;
        }

        .section {
            background-color: #fff;
            margin: 20px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 5px;
            text-align: left;
        }

        th {
			background-color: #3b4382;
			color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body>



	
    <header class="bg-dark text-white px-3 py-2" data-bs-theme="dark">
    <div class="d-flex align-items-center">
        <div><h4>Admin Dashboard</h4></div>
        <div class="d-flex ms-auto"><span>
            <form action="admin_logout.php" method="post">
                <input type="submit" name="logout" value="Logout">
            </form></span></div>
    </div>
    
</header>
    <!-- New Code -->
    <div class="container-fluid">
  <div class="row">
    <div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
    <div class="p-4">
    <a href="admin.php" class="d-block mb-4">Dashboard</a>
    <a href="admin.php?display_id=2" class="d-block mb-4">Live chats</a>
    <a href="admin.php?display_id=3" class="d-block mb-4">Online visitors</a>
    <a href="admin.php?display_id=1" class="d-block mb-4">Old chats</a>
    </div>    
    
    </div>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">


      <div class="table-responsive small">
      <!-- <div class="section"> -->
      <?php
      if (!isset($_GET['display_id'])) {
        echo '
        <h2>Live Statistics:</h2>
        <ul class="mb-3 mt-3">
            <li>Total Online Users: '.getTotalOnlineUsers().'</li>
            <li>Total Offline Users: '.getOfflineUsers().'</li>
            <li>Total Visitors: '.getTotalVisitors().'</li>
        </ul>';
        
        echo '<h2>Live Chats:</h2>';
        echo ' '.displayLiveChats().'';
        echo '<h2>Online Visitors:</h2>';
        echo '<ul>'.displayOnlineVisitors().'</ul>';
        echo ' <h2 >Old Chats:</h2>';
        echo ' '.displayRecentChats().'';
}
?>
<?php echo '</div>'; ?>

<?php
if (isset($_GET['display_id']) && $_GET['display_id'] == 2) {
    echo '<h2 >Live Chats:</h2>';
   echo '
        <table>

                '.displayLiveChats().'

        </table>
 ';
}
?>

<?php
if (isset($_GET['display_id']) && $_GET['display_id'] == 3) {  
    echo ' <h2>Online Visitors:</h2>';

    echo '
       
        <ul>
            '.displayOnlineVisitors().'
        </ul>
';

}
?>

<?php
     
if (isset($_GET['display_id']) && $_GET['display_id'] == 1) {
    echo '<h2>Old Chats:</h2>';
    
    echo '<table class="table">
        
                ' . displayRecentChats() . '
        
        </table>
    ';
}
?>





      </div>
    </main>
  </div>
</div>


  

  

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>




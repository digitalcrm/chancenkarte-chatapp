<?php 
session_start();

if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);

    // Check if it's a text message
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        $msg_type = 'text';
        $sql = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, msg_type, inserted)
                VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$msg_type}',NOW())";
        mysqli_query($conn, $sql);
    }

    // Check if it's a file upload
    if (isset($_FILES['image'])) {
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = $_FILES['image']['type'];
        $file_size = $_FILES['image']['size'];

        $file_destination = 'files/' . $file_name;

        if (move_uploaded_file($file_tmp, $file_destination)) {
            $msg_type = 'file';
            $sql = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, msg_type, inserted)
                    VALUES ({$incoming_id}, {$outgoing_id}, '{$file_name}', '{$msg_type}', NOW())";
            mysqli_query($conn, $sql);
        }
    }
} else {
    header("location: ../login.php");
}

    
?>
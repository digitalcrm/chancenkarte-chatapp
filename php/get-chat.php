<?php 

session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";

    $sql = "SELECT * FROM messages LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
            OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) ORDER BY msg_id";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            if ($row['outgoing_msg_id'] === $outgoing_id) {
                // Display outgoing 
                $file_path = 'files/' . $row['msg'];
                $output .= '<div class="chat outgoing">
                            <div class="details">
                                <a href="php/' . $file_path . '" download><p>' . $row['msg'] . '</p></a><br>
                                 '.$row['inserted'].'
                            </div>
                            </div>';
            } else {
                // Display incoming messages
                if ($row['msg_type'] === 'text') {
                    // Display text messages
                    $output .= '<div class="chat incoming">
                                <div class="details">
                                <p>' . $row['msg'] . '</p><br>
                                 '.$row['inserted'].'</a>
                                
                                </div>
                                </div>';
                } elseif ($row['msg_type'] === 'file') {
                    // Display file messages
                    $file_path = 'files/' . $row['msg'];
                    if (file_exists($file_path)) {
                        if (strpos($row['msg'], '.jpg') !== false || strpos($row['msg'], '.jpeg') !== false || strpos($row['msg'], '.png') !== false || strpos($row['msg'], '.gif') !== false) {
                            // Display image files
                          $output .= '<div class="chat incoming">
                                      <a href="php/' . $file_path . '" target="_blank"><img src="php/' . $file_path . '" alt="File Image">    <br>
                                 '.$row['inserted'].'</a>
                                  
                                      </div>';

                        } else {
                            // Display other file types as download links
                            $output .= '<div class="chat incoming">
                                        <div class="details">
                                            <a href="php/' . $file_path . '" download>Download File<br>
                                 '.$row['inserted'].'</a>
                                        </div>
                                        </div>';
                        }
                    }
                }
            }
        }
    } else {
        $output .= '<div class="text">No messages are available. Once you send a message, they will appear here.</div>';
    }
    echo $output;
} else {
    header("location: ../login.php");
}


?>


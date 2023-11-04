<?php 
    session_start();
    include_once "config.php";
    $admin_un = mysqli_real_escape_string($conn, $_POST['admin_un']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    if (!empty($admin_un) && !empty($password)) {
        $sql = mysqli_query($conn, "SELECT * FROM admin WHERE admin_un = '{$admin_un}'");
        if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
            $enc_pass = $row['admin_pw'];
            if($password === $enc_pass){
                if($sql){
                    $_SESSION['admin_id'] = $row['admin_id'];
                    echo "success";
                    header("location: ../admin.php");
                }else{
                    echo "Something went wrong. Please try again!";
                }
        }else{
            echo "$email - This email not Exist!";
        }
    }else{
        echo "All input fields are required!";
    }
}
?>
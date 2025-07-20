<?php
    session_start();
    require_once "../lib/base.php";
    $conn = getDB();
    $username = $_POST["username"];
    $email = $_SESSION["email"];
    $password = $_POST["password"];
    $current_password = $_POST["current_password"];

    // check if current password is correct
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if($result[0]["password"] != hash('sha256', $current_password))
    {
        echo "<script>alert('Current password is incorrect. Please try again.');</script>";
        echo "<script>window.location.href='../home.php';</script>";
        exit();
    }

    // deal with the profile picture
    $photo = $_FILES["photo"];

    $change_username = false;

    if(trim($username) != "")
    {
        // check if the new username already exists and not the same as the current username
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bindParam(1, $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if(count($result) > 0 && $result[0]["email"] != $email)
        {
            echo "<script>alert('Username already exists. Please try again.');</script>";
            echo "<script>window.location.href='../home.php';</script>";
            exit();
        }
        $change_username = true;
    }

    $change_password = false;

    if(trim($password) != "")
    {
        // check password length and  does not include at least one capital letter, a lowercase letter, and a number
        if(strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password))
        {
            echo "<script>alert('Password must be at least 6 characters long and include at least one capital letter, one lowercase letter and a number. Please try again.');</script>";
            echo "<script>window.location.href='../home.php';</script>";
            exit();
        }
        $change_password = true;
    }

    $change_photo = false;

    if($photo["name"] != "")
    {
        // size limit 5MB
        if($photo["size"] > 5 * 1024 * 1024)
        {
            echo "<script>alert('File size is too large. Please try again.');</script>";
            echo "<script>window.location.href='../home.php';</script>";
            exit();
        }

        // check file type
        $allowed = array("image/jpeg", "image/jpg", "image/png", "image/gif");
        if(!in_array($photo["type"], $allowed))
        {
            echo "<script>alert('File type is not allowed. Please try again.');</script>";
            echo "<script>window.location.href='../home.php';</script>";
            exit();
        }
        // if photo exists, delete the old one
        if($result[0]["photo"] != null)
        {
            unlink("../upload/".$result[0]["photo"]);
        }
        // upload file to ./upload
        $file_name = $photo["name"];
        $file_tmp = $photo["tmp_name"];
        $file_ext = explode(".", $file_name);
        $file_actual_ext = strtolower(end($file_ext));
        $file_name_new = uniqid('', true).".".$file_actual_ext;
        $file_destination = "../upload/".$file_name_new;
        move_uploaded_file($file_tmp, $file_destination);
        $change_photo = true;
    }

    if(!$change_username && !$change_password && !$change_photo)
    {
        echo "<script>alert('Please enter the new information.');</script>";
        echo "<script>window.location.href='../home.php';</script>";
        exit();
    }

    // update user information
    
    $encrypted_password = hash('sha256', $password);

    if($change_username)
    {
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE email = ?");
        $stmt->bindParam(1, $username, PDO::PARAM_STR);
        $stmt->bindParam(2, $email, PDO::PARAM_STR);
        $stmt->execute();
    }
    if($change_password)
    {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bindParam(1, $encrypted_password, PDO::PARAM_STR);
        $stmt->bindParam(2, $email, PDO::PARAM_STR);
        $stmt->execute();
    }
    if($change_photo)
    {
        $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE email = ?");
        $stmt->bindParam(1, $file_name_new, PDO::PARAM_STR);
        $stmt->bindParam(2, $email, PDO::PARAM_STR);
        $stmt->execute();
    }
    echo "<script>alert('Update successful.');</script>";
    echo "<script>window.location.href='../home.php';</script>";
    exit();
?>
<?php
    session_start();
    require_once "../lib/base.php";
    $conn = getDB();
    $email = $_POST["email"];
    $password = $_POST["password"];

    $encrypted_password = hash('sha256', $password);
    // bind parameters
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->bindParam(2, $encrypted_password, PDO::PARAM_STR);
    
    // if bind parameters failed, return error message
    if(!$stmt)
    {
        $_SESSION["error"] = "Incorrect username or password. Please try again.";
        session_write_close();
        echo "<script>window.location.href='../index.php';</script>";
        exit();
    }
    $stmt->execute();

    $result = $stmt->fetchAll();
    if($result != false && count($result) > 0)
    {
       
        $_SESSION["email"] = $email;
        session_write_close();
        echo "<script>window.location.href='../home.php';</script>";
        exit();
    }
    else
    {
        $_SESSION["error"] = "Incorrect username or password. Please try again.";
        session_write_close();
        echo "<script>window.location.href='../index.php';</script>";
        exit();
    }
?>
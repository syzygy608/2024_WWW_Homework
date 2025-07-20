<?php
    session_start();
    require_once "../lib/base.php";
    $conn = getDB();
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if($password != $confirm_password)
    {
        $_SESSION["error"] = "Password and comfirm password do not match. Please try again.";
        session_write_close();
        echo "<script>window.location.href='../register.php';</script>";
        exit();
    }

    // check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if($result != false && count($result) > 0)
    {
        $_SESSION["error"] = "Username or email already exists";
        session_write_close();
        echo "<script>window.location.href='../register.php';</script>";
        exit();
    }

    // check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bindParam(1, $username, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if($result != false && count($result) > 0)
    {
        $_SESSION["error"] = "Username or email already exists";
        session_write_close();
        echo "<script>window.location.href='../register.php';</script>";
        exit();
    }

    // check email format
    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $_SESSION["error"] = "Invalid email format";
        session_write_close();
        echo "<script>window.location.href='../register.php';</script>";
        exit();
    }

    // check password length and  does not include at least one capital letter, a lowercase letter, and a number
    if(strlen($password) < 6  || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password))
    {
        $_SESSION["error"] = "Invalid password format.<br>1. Password must be at least one uppercase and lowercase letter<br>2. Password must contains at least one digit.<br>3. Password must be at least 6 characters long.";
        session_write_close();
        echo "<script>window.location.href='../register.php';</script>";
        exit();
    }

    
    $encrypted_password = hash('sha256', $password);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $username, PDO::PARAM_STR);
    $stmt->bindParam(2, $email, PDO::PARAM_STR);
    $stmt->bindParam(3, $encrypted_password, PDO::PARAM_STR);
    $stmt->execute();
    session_write_close();
    echo "<script>alert('Register successfully. Please login.');</script>";
    echo "<script>window.location.href='../index.php';</script>";
    exit();
?>
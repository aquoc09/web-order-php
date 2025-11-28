<?php
if (!defined("ROOT"))
{
    echo "You don't have permission to access this page!";
    echo "<a href='../../index.php'>Trở về trang chủ</a>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PQ - Dashboard</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/dashboard.css">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
  <!-- Header -->
  <header>
    <div class="header-dashboard">
      <div class="logo">
        <div class="menu-bar">
          <i class="bi bi-list"></i>
        </div>
        <a href="../index.php">
          <img src="../img/logo/logo40x40.png" alt="logo">
        </a>
      </div>
      <div class="admin-user">
        <span>Xin chào, <strong>admin</strong></span> <img src="../img/logo/logo40x40.png" alt="avatar-admin">
      </div>
    </div>
  </header>
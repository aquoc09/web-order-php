<?php
session_start();
// __DIR__ là thư mục hiện tại (includes), /../ là lùi ra thư mục cha (web-order-php)
include_once __DIR__ . '/../database/conf.php';
// include_once __DIR__ . '/../auth/refreshToken.php';
$token = $_COOKIE['auth_token'] ?? '';

if ($token) {
    $sql = "SELECT u.* FROM user_tokens t 
            JOIN user u ON u.id = t.user_id
            WHERE t.token = ? AND t.expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $currentUser = $result->fetch_assoc();
    } else {
        $currentUser = null;
    } 
} else {
    $currentUser = null;
}

if(!$currentUser || $currentUser['role'] !== 'admin' && $currentUser['role'] !== 'manager'){
  echo "You don't have permission to access this page!";
  echo "<a href='../index.php'>Trở về trang chủ</a>";
  exit;	
}


define('ROOT', dirname(__FILE__) );//Thu muc chứa file index);
include 'includes/header.php';
include 'mod.php';

$msg = $_GET['msg'] ?? '';
?>
<!-- Toast HTML -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="msgToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body">
        <?= htmlspecialchars($msg) ?>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
<?php if(!empty($msg)): ?>
    var toastEl = document.getElementById('msgToast');
    var toast = new bootstrap.Toast(toastEl);
    toast.show();
<?php endif; ?>
</script>
  <!-- Content -->
  <main>
    <!-- Navigation -->
    <?php 
      include 'includes/sidebar.php'; 
      loadModule();
    ?>
    
  </main>
  <!-- Bootstrap -->
  <script src="../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="../js/jquery.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <!-- Chartjs -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="../js/chart.js"></script>
  <script src="../js/dashboard.js"></script>
</body>
</html>
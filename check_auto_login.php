<?php
// PDO 객체를 가지고 있고, 세션 시작 기능 포함
include_once 'config/config.php';

$userToken = $_COOKIE["UserToken"] ?? null;

if ($userToken) {
  try {
    // 1. SQL 변경: 변수 자리에 ? (placeholder)를 넣습니다.
    $sql = "SELECT user_number, email FROM user WHERE uniq_id = ?";
    
    // 2. Prepare: $pdo 객체로 쿼리를 준비합니다.
    $stmt = $pdo->prepare($sql);

    // 3. Execute: ? 자리에 $userToken 변수를 배열로 안전하게 전달하여 실행합니다.
    $stmt->execute([$userToken]);

    // 4. Fetch: mysqli_fetch_assoc 대신 fetch()로 결과를 가져옵니다.
    $user = $stmt->fetch();

    if ($user) {
      // 유저 고유번호와 닉네임을 함께 조회해서 가져온다.
      $_SESSION['user_number'] = $user['user_number'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['loggedin'] = true;
    } else {
      // 6. (추가) 쿠키는 있지만 DB에 없는(가짜) 토큰일 경우
        $_SESSION['loggedin'] = false;
    }

  } catch (PDOException $e) {
    error_log("Auto login query failed: " . $e->getMessage());
    $_SESSION['loggedin'] = false;
  }
}
?>


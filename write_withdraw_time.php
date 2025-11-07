<?php
include_once 'config/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_SESSION['email'] ?? null;

    if (empty($email)) {
        echo "0"; // 로그인 필요
        exit();
    }

    // 필요한 함수 호출
    $result = write_withdraw_text_func($pdo, $email);
  
    // 응답 데이터 반환
    if ($result) {
        echo "1"; // 성공
    } else {
        echo "0"; // 실패
    }
}
  

/**
 * (변경) 함수가 $pdo를 인자로 받도록 수정 (의존성 주입)
 * @param PDO $pdo
 * @param string $email
 * @return bool (성공 true, 실패 false)
 */
function write_withdraw_text_func($pdo, $email){
    // (변경) DB 연결 로직 삭제 (이미 $pdo로 받아옴)

    // (변경) SQL Injection 보안 적용: 변수 자리에 물음표(?) 사용
    $sql = "UPDATE user 
            SET withdraw_time = NOW() 
            WHERE email = ?";

    try {
        // (변경) 쿼리 준비 및 실행
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        // (변경) 성공 시 true 반환
        return true; 
    } catch (PDOException $e) {
        // (개선) 에러 발생 시 로그 남기기
        error_log("Withdraw time update failed: " . $e->getMessage());
        return false;
    }
}
?>
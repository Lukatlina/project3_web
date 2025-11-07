<?php

include_once 'config/config.php';

$userNumber = $_SESSION['user_number'] ?? null;
$boardNumber = $_POST['board_number'] ?? null;

// userNumber와 boardNumber가 유효한지 확인
if (empty($userNumber) || empty($boardNumber)) {
    echo "0"; // 유효하지 않은 요청
    exit();
}

try {
    $sqlCount = "SELECT count(likes_number) 
                 FROM likes 
                 WHERE board_number = ?";
    
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute([$boardNumber]);

    // 2. (변경) fetchColumn()으로 COUNT 결과(숫자)를 바로 가져오기
    $cheeringCount = $stmtCount->fetchColumn();

    // 3. (변경) 'board' 테이블에 좋아요 수 업데이트 (PDO Prepare Statement)
    $sqlUpdate = "UPDATE board 
                  SET cheering = ? 
                  WHERE board_number = ?";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([$cheeringCount, $boardNumber]);

    // 4. (동일) 최종 카운트 출력
    echo $cheeringCount;

} catch (Exception $e) {
    error_log("Like count update failed: " . $e->getMessage());
    echo "0"; // 데이터베이스 연결 실패
}
?>
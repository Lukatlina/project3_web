<?php
// 1. Config 파일 포함 (PDO, 세션)
include_once __DIR__ . '/../../../config/config.php';

// 2. 변수명 camelCase로 변경
$userNumber = $_SESSION['user_number'] ?? null;
$boardNumber = $_POST['board_number'] ?? null;
$commentText = $_POST['textarea'] ?? null; // (★주의: JS에서 'textarea'로 보냄)
$parentNumber = $_POST['reply_number'] ?? 0; // (★주의: JS에서 'reply_number'로 보냄)

// 3. 유효성 검사
if (empty($userNumber) || empty($boardNumber) || empty($commentText)) {
    echo "0"; // 실패
    exit();
}

// 4. PDO의 예외 처리(try...catch) 사용
try {
    // 5. (변경) PDO Prepared Statement로 INSERT
    $sql = "INSERT INTO comments (board_number, user_number, parent_number, comments_text, comments_save_time) 
            VALUES (?, ?, ?, ?, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$boardNumber, $userNumber, $parentNumber, $commentText]);

    echo "1"; // 성공

} catch (\PDOException $e) {
    error_log("Comment creation failed: " . $e->getMessage());
    echo "0"; // 실패
}
?>
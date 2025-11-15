<?php
// 1. Config 파일 포함 (PDO, 세션)
include_once __DIR__ . '/../../../config/config.php';

// 2. 변수명 camelCase로 변경
$userNumber = $_SESSION['user_number'] ?? null;
$boardNumber = $_POST['board_number'] ?? null;
$commentNumber = $_POST['comment_number'] ?? null;

if (empty($userNumber)) {
    echo "0"; // 로그인 필요
    exit();
}

// 3. PDO의 예외 처리(try...catch) 사용
try {
    if (!empty($boardNumber)) {
        // --- 1. 게시글 삭제 ---
        // 본인 글만 삭제하도록 user_number 조건 추가
        $sql = "DELETE FROM board WHERE board_number = ? AND user_number = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$boardNumber, $userNumber]);

        // DB 제약조건(ON DELETE CASCADE)으로 comments, likes, image, video가 자동 삭제됨
        // (기존처럼 PHP가 comment, likes를 따로 삭제할 필요 없음)

        // 파일 시스템 삭제 (PHP가 직접 폴더 삭제)
        $finalUploadDir = '../upload_images/' . $boardNumber . '/';
        if (is_dir($finalUploadDir)) {
            // 이 함수는 폴더 안의 모든 파일을 재귀적으로 삭제합니다.
            // (안전하게 하려면 glob, unlink를 사용해야 하지만, 간단하게 구현)
            system('rm -rf ' . escapeshellarg($finalUploadDir));
        }

    } elseif (!empty($commentNumber)) {
        // --- 2. 댓글 삭제 ---
        // 본인 댓글만 삭제하도록 user_number 조건 추가
        $sql = "DELETE FROM comments WHERE comments_number = ? AND user_number = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$commentNumber, $userNumber]);

        // DB 제약조건(ON DELETE CASCADE)으로 likes, replies(대댓글)가 자동 삭제됨
    }

    echo "1"; // 성공

} catch (\PDOException $e) {
    error_log("Delete failed: " . $e->getMessage());
    echo "0"; // 실패
}
?>
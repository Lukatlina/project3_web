<?php
// 1. Config 파일 포함 (PDO, 세션)
include_once __DIR__ . '/../../../config/config.php';

// 2. 변수명 camelCase로 변경
$userNumber = $_SESSION['user_number'] ?? null;
$boardNumber = $_POST['board_number'] ?? null;
$commentNumber = $_POST['comment_number'] ?? null;
$isButton = $_POST['is_button'] ?? 'false'; // 'true' 또는 'false' 문자열

if (empty($userNumber) || empty($boardNumber)) {
    echo json_encode(['status' => 'error', 'message' => 'Login or board ID missing.']);
    exit();
}

// 3. (★개선) 댓글 좋아요/게시글 좋아요 구분
$isCommentLike = !empty($commentNumber);

// 4. PDO 트랜잭션 시작
$pdo->beginTransaction();
try {
    if ($isButton === 'true') {
        // --- 1. 좋아요 (INSERT) ---
        $sql = "INSERT INTO likes (board_number, user_number, comments_number) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$boardNumber, $userNumber, $commentNumber]);
    } else {
        // --- 2. 좋아요 취소 (DELETE) ---
        $sql = "DELETE FROM likes WHERE board_number = ? AND user_number = ? AND comments_number " . ($isCommentLike ? "= ?" : "IS NULL");
        $stmt = $pdo->prepare($sql);
        if ($isCommentLike) {
            $stmt->execute([$boardNumber, $userNumber, $commentNumber]);
        } else {
            $stmt->execute([$boardNumber, $userNumber]);
        }
    }

    // 5. (★개선) 좋아요 카운트 다시 세고 업데이트 (기존 로직 개선)
    if ($isCommentLike) {
        // 댓글의 좋아요 수 업데이트
        $sqlCount = "SELECT COUNT(*) FROM likes WHERE comments_number = ?";
        $stmtCount = $pdo->prepare($sqlCount);
        $stmtCount->execute([$commentNumber]);
        $likeCount = $stmtCount->fetchColumn();

        $sqlUpdate = "UPDATE comments SET comments_cheering = ? WHERE comments_number = ?";
        $pdo->prepare($sqlUpdate)->execute([$likeCount, $commentNumber]);
    } else {
        // 게시글의 좋아요 수 업데이트
        $sqlCount = "SELECT COUNT(*) FROM likes WHERE board_number = ? AND comments_number IS NULL";
        $stmtCount = $pdo->prepare($sqlCount);
        $stmtCount->execute([$boardNumber]);
        $likeCount = $stmtCount->fetchColumn();

        $sqlUpdate = "UPDATE board SET cheering = ? WHERE board_number = ?";
        $pdo->prepare($sqlUpdate)->execute([$likeCount, $boardNumber]);
    }

    // 6. 모든 작업 완료, 트랜잭션 커밋
    $pdo->commit();

    // 7. (★변경) JS가 화면을 즉시 업데이트할 수 있도록 새 카운트를 반환
    echo $likeCount;

} catch (\PDOException $e) {
    $pdo->rollBack(); // 실패 시 롤백
    error_log("Like update failed: " . $e->getMessage());
    echo "0"; // 실패 (기존 JS 호환성)
}
?>
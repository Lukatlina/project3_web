<?php
include_once __DIR__ . '/../../../config/config.php';
include_once PROJECT_ROOT . '/back/common/functions.php';

// (변경) 변수명을 camelCase로 변경하고, ?? 연산자로 Notice 에러 방지
// (int)로 타입을 강제하여 보안을 강화합니다.
$boardNumber = (int)($_POST['board_number'] ?? 0);
$userNumber = (int)($_SESSION['user_number'] ?? 0);

// 유효성 검사
if ($boardNumber === 0 || $userNumber === 0) {
    header('Content-Type: application/json', true, 400); // 400 Bad Request
    echo json_encode(['error' => 'Invalid request.']);
    exit();
}

try {
    // SQL Injection을 방어하는 PDO Prepare Statement
    $sql = "SELECT board_number, contents, user_number FROM board WHERE board_number = ? AND user_number = ?";
    $stmt = $pdo->prepare($sql); // PDO 쿼리 준비
    $stmt->execute([$boardNumber, $userNumber]); // 
    $row = $stmt->fetch(); // (변경) mysqli_fetch_assoc 대신 fetch() 사용

    if (!$row) {
        // 게시글이 없을 경우 명확한 에러 반환
        throw new Exception("Post not found.");
    }

    $contents = $row['contents'];
    // injectMediaPaths() 함수를 호출
    // 이 함수가 $pdo를 이용해 video, image를 모두 조회하고 HTML을 완성해줍니다.
    $processedContents = injectMediaPaths($pdo, $boardNumber, $contents);

    $responseArray = [
        'boardNumber' => $row['board_number'],
        'writeUserNumber' => $row['user_number'],
        'contents' => $processedContents // 비디오/이미지 경로가 삽입된 HTML
    ];

    // JSON 응답 반환
    header('Content-Type: application/json');
    echo json_encode($responseArray);

} catch (Exception $e) {
    // (개선) 에러 발생 시 JS가 받을 수 있도록 JSON 에러 메시지 반환
    header('Content-Type: application/json', true, 500); // 500 Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}
?>
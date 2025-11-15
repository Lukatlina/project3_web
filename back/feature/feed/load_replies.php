<?php
/*
1. board_number가 같으면서 부모댓글이 0인 댓글들을 comment_number의 역순으로 보여준다.
*/

include_once __DIR__ . '/../../../config/config.php';
include_once PROJECT_ROOT . '/back/common/functions.php';

$userNumber = (int)($_SESSION['user_number'] ?? 0);
$lastItemNumber = (int)($_POST['lastItemNumber'] ?? 0); // JS에서 ''로 보낼 경우 0이 됨
$boardNumber = (int)($_POST['board_number'] ?? 0);
$parentNumber = (int)($_POST['parent_number'] ?? 0);

const REPLIES_PER_PAGE = 10;

$replies = [];

// (변경) $boardNumber와 $parentNumber가 유효할 때만 쿼리 실행
if ($userNumber > 0 && $boardNumber > 0 && $parentNumber > 0) {

    // 6. (★핵심★) Step 9-2에서 만든 공용 함수 호출
    // 모든 복잡한 로직이 이 함수 호출 한 줄로 끝납니다.
    $replies = getRepliesForComment(
        $pdo, 
        $boardNumber, 
        $parentNumber,
        $userNumber, 
        $lastItemNumber, 
        REPLIES_PER_PAGE
    );
} 

// 유효하지 않은 요청이거나 결과가 없으면 빈 배열 '[]'을 반환
header('Content-Type: application/json');
    echo json_encode($replies);
?>
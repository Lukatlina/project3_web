<?php
/*
1. board_number가 같으면서 부모댓글이 0인 댓글들을 comment_number의 역순으로 보여준다.
*/
include_once 'config/config.php';

$userNumber = (int)($_SESSION['user_number'] ?? 0);
$lastItemNumber = (int)($_POST['lastItemNumber'] ?? 0);
$scrollCount = (int)($_POST['scrollCount'] ?? 0);
$boardNumber = (int)($_POST['board_number'] ?? 0);

const COMMENTS_PER_PAGE = 20;

$comments = []; // 빈 배열 초기화

// (변경) $scrollCount와 $boardNumber가 유효할 때만 실행
if ($scrollCount > 0 && $boardNumber > 0) {

    $comments = getCommentsForPost(
        $pdo, 
        $boardNumber, 
        $userNumber, 
        $lastItemNumber, 
        COMMENTS_PER_PAGE
    );
}

// 유효하지 않은 요청이거나 결과가 없으면 빈 배열 '[]'을 반환
    header('Content-Type: application/json');
    echo json_encode($comments);

?>
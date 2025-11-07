<?php
// 1. config.php 포함 (PDO, 세션, 에러 리포팅 담당)
include_once 'config/config.php';

$posts = []; // 응답 배열 초기화

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 2. (변경) SQL 쿼리 (LIMIT 20)
        $sql = "SELECT board_number, board.user_number, contents, contents_save_time, cheering, nickname 
                FROM board 
                LEFT JOIN user ON board.user_number=user.user_number 
                ORDER BY board.board_number DESC 
                LIMIT 20";

        // 3. (변경) PDO 쿼리 준비 및 실행
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        // 4. (변경) fetchAll로 모든 결과 한 번에 가져오기
        $allPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. (변경) for 루프 대신 foreach 사용
        foreach ($allPosts as $boardRow) {
            // (변경) 변수명 camelCase로 변경
            $boardNumber = $boardRow['board_number'];
            $boardUserNumber = $boardRow['user_number'];
            $contents = $boardRow['contents'];
            $contentsSaveTime = $boardRow['contents_save_time'];
            $cheering = $boardRow['cheering'];
            $writeUserNickname = $boardRow['nickname'];

            // 날짜 포맷 변경을 위한 DateTime 함수 선언. 
            $dateTime = new DateTime($contentsSaveTime);

            // (변경) 배열 키를 camelCase로 변경
            $posts[] = [
                'id' => $boardNumber,
                'writeUserNumber' => $boardUserNumber,
                'writeUserNickname' =>  $writeUserNickname,
                'dateTime' => $dateTime, // (참고) JSON 인코딩 시 DateTime 객체는 특정 형식으로 변환됩니다.
                'contents' => $contents,
                'cheering' => $cheering
            ];
        }

    } catch (PDOException $e) {
        // (개선) 에러 처리
        error_log("resetBoard20 failed: " . $e->getMessage());
        // $posts는 빈 배열인 상태로 넘어갑니다.
    }
}

// 6. (변경) POST 요청이 아니거나 결과가 없으면 빈 배열 '[]'이 인코딩됩니다.
header('Content-Type: application/json'); // (개선) JSON 헤더 명시
echo json_encode($posts);

?>
<?php
/*
1. 전체 데이터 수 집계
2. 전체 데이터 수 / 보여줄 데이터 수 = 올림(결과) -> 올림한 결과값 만큼 스크롤 했을 때 보여야 한다.
3. 먼저 for 문으로 반복해서 보여줄 수 있는지 확인할 것
4. for문으로 보여줄 수 있다면 스크롤 예제를 찾은 후 예제를 맞게 고친다.
*/

include 'server_connect.php';

$userNumber = (int)($_SESSION['user_number'] ?? 0);
$lastItemNumber = (int)($_POST['lastItemNumber'] ?? 0);
$scrollCount = (int)($_POST['scrollCount'] ?? 0);

// 상수 선언
const POSTS_PER_PAGE = 20;

// 파일의 최종 JSON 응답 배열을 미리 선언
$postsResponse = [];

// (로직 동일) 스크롤 횟수나 마지막 아이템 번호가 유효할 때만 실행
if ($scrollCount > 0 && $lastItemNumber > 0) {

    try {
        // 1. (변경) $sql: 변수가 들어갈 자리를 ? (placeholder)로 변경
        // (참고) LIMIT에는 ? placeholder를 바로 쓰기보다 명시적으로 바인딩하는 것이 안전합니다.
        $sql = "SELECT board_number, board.user_number, contents, contents_save_time, cheering, nickname
                FROM board LEFT JOIN user ON board.user_number=user.user_number
                WHERE board.board_number < :lastItemNumber
                ORDER BY board.board_number DESC
                LIMIT :limit";

        // 2. (변경) $pdo로 쿼리 준비
        $stmt = $pdo->prepare($sql);

        // 3. (변경) placeholder에 변수 바인딩
        // (int)로 타입을 강제하여 숫자가 아닌 값이 들어오는 것을 막습니다.
        $limitCount = (int)POSTS_PER_PAGE;
        $stmt->bindParam(':lastItemNumber', $lastItemNumber, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limitCount, PDO::PARAM_INT);

        // 4. (변경) 쿼리 실행
        $stmt->execute();

        /**
         * 5. (★핵심 변경) fetchAll()
         * mysqli_num_rows + for + mysqli_fetch_array 3줄이
         * 이 fetchAll() 한 줄로 대체됩니다!
         * $allPosts에는 20개의 게시물 정보가 배열로 즉시 담깁니다.
         */
        $allPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. (★핵심 변경) 받아온 $allPosts 배열을 foreach로 순회
        foreach ($allPosts as $post) {

            // 7. (변경) $board_row['...'] 대신 $post['...'] 사용, 변수명 camelCase로 변경
            $boardNumber = (int)$post['board_number'];
            $userNumber = (int)$post['user_number'];
            $contents = $post['contents'];
            $contentsSaveTime = $post['contents_save_time'];
            $cheering = $post['cheering'];
            $writeUserNickname = $post['nickname'];

            // 8. (★핵심 변경) `include 'find_image.php';` -> `injectMediaPaths()` 함수 호출
            // Step 3에서 만든 함수를 호출하여 $contents 안의 <img> 태그를 완성시킵니다.
            $processedContents = injectMediaPaths($pdo, $boardNumber, $contents);

            // 9. (변경) 날짜 포맷팅 (기존 로직과 동일, 함수로 빼도 좋습니다)
            $dateTime = new DateTime($contentsSaveTime);
            if ($dateTime->format('Y') === date('Y')) {
                $formattedDateTime = $dateTime->format('m. d. H:i');
            } else {
                $formattedDateTime = $dateTime->format('Y. m. d. H:i');
            }

            // 10. (★핵심 변경) `include 'count_likes.php';` -> `getLikeStatus()` 함수 호출
            // Step 2에서 만든 함수를 호출하여, 현재 로그인한 유저($userNumber)가
            // 이 게시물($boardNumber)에 좋아요를 눌렀는지(1 또는 0) 확인합니다.
            $likeStatus = getLikeStatus($pdo, $userNumber, $boardNumber, null); // 댓글이 아니므로 null

            // 11. (변경) JS로 보낼 최종 JSON 배열에 camelCase 키로 저장
            $postsResponse[] = [
                'userNumber' => $userNumber,
                'id' => $boardNumber, // JS가 'id'를 사용하므로 'id'로 보냅니다.
                'writeUserNumber' => (int)$post['user_number'],
                'writeUserNickname' => $writeUserNickname,
                'dateTime' => $formattedDateTime,
                'contents' => $processedContents,
                'cheering' => $cheering,
                'likesRowCount' => $likeStatus // $likes_row_count -> $likeStatus
            ];
        }

        // 12. (변경) $postsResponse 배열을 JSON으로 인코딩하여 출력
        header('Content-Type: application/json');
        echo json_encode($postsResponse);

    } catch (PDOException $e) {
        // 13. (개선) DB 에러 발생 시 JS가 알 수 있도록 500 에러와 JSON 메시지 반환
        header('Content-Type: application/json', true, 500);
        echo json_encode(['error' => $e->getMessage()]);
    }

} else {
    // (변경) $scrollCount가 0이거나 $lastItemNumber가 0이면
    // 빈 JSON 배열 '[]'을 반환합니다. (기존 '0'보다 JS가 처리하기 좋습니다)
    header('Content-Type: application/json');
    echo json_encode($postsResponse);
}
?>
<?php
/**
 * 사용자가 게시글/댓글에 좋아요를 눌렀는지 확인합니다.
 * (기존 count_likes.php의 역할을 대체합니다)
 *
 * @param PDO $pdo PDO 객체
 * @param int $userId 확인할 사용자 번호
 * @param int $boardId 게시글 번호
 * @param int|null $commentId (선택) 댓글 번호
 * @return int 1 (좋아요 누름) 또는 0 (안 누름)
 */

function getLikeStatus($pdo, $userId, $boardId, $commentId = null) {

    // 1. 변수 유효성 검사
    if (empty($userId) || empty($boardId)) {
        return 0;
    }

    try {
        if (!empty($commentId)) {
            // 댓글 좋아요 확인
            $sql = "SELECT COUNT(*) FROM likes WHERE user_number = ? AND comments_number = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $commentId]);
        } else {
            // 게시글 좋아요 확인
            $sql = "SELECT COUNT(*) FROM likes WHERE board_number = ? AND user_number = ? AND comments_number IS NULL";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$boardId, $userId]);
        }

        // COUNT(*)의 결과 (1 또는 0)를 정수로 반환
        return (int) $stmt->fetchColumn();

    } catch (PDOException $e) {
        error_log("getLikeStatus failed: " . $e->getMessage());
        return 0; // 에러 발생 시 0 반환
    }
}

/**
 * DB에서 이미지/비디오 경로를 찾아 $contents HTML 문자열에 주입합니다.
 * (기존 find_image.php와 find_video.php의 역할을 모두 대체합니다)
 *
 * @param PDO $pdo PDO 객체
 * @param int $boardNumber 게시글 번호
 * @param string $contents 원본 HTML (<img> 태그의 src가 비어있음)
 * @return string 미디어(src)가 주입된 HTML
 */
function injectMediaPaths(PDO $pdo, $board_number, $contents, $context = 'detail') {
    
    try {
        // 1. $board_number로 모든 미디어 정보 한 번에 조회 (PDO Prepared Statement 사용)
        $image_sql = "SELECT image_path, image_id, video_number, is_thumbnail 
                      FROM image WHERE board_number = ?";
        
        $stmt = $pdo->prepare($image_sql);
        $stmt->execute([$board_number]); // 파라미터 바인딩

        $images = array();
        while ($image_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // image_id를 키로 사용하는 배열로 재구성 (검색 속도 향상)
            $images[$image_row['image_id']] = $image_row;
        }

        // 미디어가 없으면 원본 반환 (mysqli_num_rows 대신 empty() 체크)
        if (empty($images)) {
            return $contents; 
        }

        // 2. 비디오 정보 미리 조회 (N+1 쿼리 방지)
        $video_sql = "SELECT video_number, video_path FROM video WHERE board_number = ?";
        $video_stmt = $pdo->prepare($video_sql);
        $video_stmt->execute([$board_number]);
        
        // PDO::FETCH_KEY_PAIR : 1번 열(video_number)을 키로, 2번 열(video_path)을 값으로 하는
        // 연관 배열을 자동으로 생성합니다. (매우 효율적)
        $videos = $video_stmt->fetchAll(PDO::FETCH_KEY_PAIR);


        // 3. HTML 파싱 및 교체 (이 로직은 DB와 무관하므로 변경 없음)
        $pattern = '/<img id="(\d+)">/'; // (예: <img id="1">)

        $newContents = preg_replace_callback(
            $pattern,
            function ($matches) use ($images, $videos, $context) {
                $imageId = $matches[1]; // "1"

                // <img> 태그 ID에 해당하는 미디어 정보가 DB에 없으면, 태그 삭제
                if (!isset($images[$imageId])) {
                    return ''; // (또는 $matches[0]를 반환하여 원본 유지)
                }

                $image = $images[$imageId];
                $is_thumbnail = (int)$image['is_thumbnail'];
                $image_path = $image['image_path'];

                $replacement_html = '';

                // 2. --- 썸네일(비디오)일 경우 ---
                if ($is_thumbnail === 1) {
                    $video_number = $image['video_number'];
                    // $videos 배열에서 키(video_number)로 경로(video_path)를 즉시 찾음
                    $video_path = $videos[$video_number] ?? ''; 
                    $image_path = $image['image_path']; // 썸네일 이미지 경로

                    // [★ 핵심] $context에 따라 교체할 HTML 문자열을 "생성"
                    if ($context === 'detail') {
                        // "상세" 페이지: <video> 태그와 썸네일 <img> 태그 모두 생성
                        $replacement_html = "<video id=\"video_$imageId\" controls><source src=\"$video_path\" type=\"video/mp4\"></video>";
                                        
                    } else {
                        // "피드" 또는 "수정" 모달: 썸네일 <img> 태그만 생성
                        $replacement_html = "<img id=\"$imageId\" src=\"$image_path\">";
                    }

                // 3. --- 일반 사진일 경우 ---
                } else {
        
                    
                    // "상세", "피드", "수정" 모두 <img> 태그 생성
                    $replacement_html = "<img id=\"$imageId\" src=\"$image_path\">";
                }

                // 4. --- preg_replace 실행 ---
                // $pattern에 일치하는 부분을 위에서 조건에 맞게 생성한 $replacement_html 문자열로 교체합니다.
                return $replacement_html;
            },
            $contents
        );

        return $newContents;

    } catch (PDOException $e) {
        // 실제 운영 환경에서는 오류를 로깅해야 합니다.
        // error_log("Media injection failed: " . $e->getMessage());
        
        // DB 오류 발생 시, 원본 콘텐츠를 그대로 반환하여 사이트가 깨지는 것을 방지
        return $contents;
    }
}
    
/**
 * 특정 게시물의 댓글 목록을 가져옵니다.
 * (post_detail_page.php와 post/load_comments.php가 공통으로 사용)
 *
 * @param PDO $pdo PDO 객체
 * @param int $boardNumber 게시글 번호
 * @param int $userNumber 현재 로그인한 사용자 번호 (좋아요 상태 확인용)
 * @param int $lastItemNumber 마지막으로 로드된 댓글 ID (이 ID보다 큰 댓글을 로드)
 * @param int $limit 로드할 댓글 개수
 * @return array 댓글 정보가 담긴 배열
 */
function getCommentsForPost($pdo, $boardNumber, $userNumber, $lastItemNumber = 0, $limit = 20) {

    $commentsResponse = []; // 반환할 빈 배열 선언

    // (추가) 유효성 검사
    if ($boardNumber <= 0 || $userNumber <= 0) {
        return $commentsResponse; // 빈 배열 반환
    }

    try {
        // 1. (Step 8-3과 동일) SQL 쿼리 준비
        $sql = "SELECT comments_number, comments.user_number, comments_text, 
                       comments_save_time, comments_cheering, user.nickname
                FROM comments LEFT JOIN user ON comments.user_number=user.user_number
                WHERE comments.board_number = :boardNumber 
                  AND comments.parent_number = 0
                  AND comments.comments_number > :lastItemNumber
                ORDER BY comments.comments_number 
                LIMIT :limit";

        $stmt = $pdo->prepare($sql);

        // 2. (Step 8-3과 동일) 변수 바인딩
        $stmt->bindParam(':boardNumber', $boardNumber, PDO::PARAM_INT);
        $stmt->bindParam(':lastItemNumber', $lastItemNumber, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. (Step 8-3과 동일) 대댓글 개수/좋아요 쿼리 미리 준비
        $replyCountSql = "SELECT COUNT(*) FROM comments WHERE board_number = ? AND parent_number = ?";
        $stmtReplyCount = $pdo->prepare($replyCountSql);

        // 4. (Step 8-3과 동일) foreach 루프
        foreach ($allComments as $comment) {
            $commentsNumber = (int)$comment['comments_number'];

            // 대댓글 개수 조회
            $stmtReplyCount->execute([$boardNumber, $commentsNumber]);
            $numberOfReply = (int)$stmtReplyCount->fetchColumn();

            // 날짜 포맷팅
            $dateTime = new DateTime($comment['comments_save_time']);
            if ($dateTime->format('Y') === date('Y')) {
                $formattedDateTime = $dateTime->format('m. d. H:i');
            } else {
                $formattedDateTime = $dateTime->format('Y. m. d. H:i');
            }

            // 좋아요 상태 조회 (config/functions.php에 있는 함수 호출)
            $likeStatus = getLikeStatus($pdo, $userNumber, $boardNumber, $commentsNumber);

            // 5. (Step 8-3과 동일) 최종 배열에 추가
            // (js/post_detail_page.js가 'dateTime'을 사용하므로 키를 'dateTime'으로 함)
            $commentsResponse[] = [
                'userNumber' => $userNumber,
                'id' => $commentsNumber,
                'boardNumber' => $boardNumber,
                'writeUserNumber' => (int)$comment['user_number'],
                'writeUserNickname' => $comment['nickname'],
                'dateTime' => $formattedDateTime, // Step 10-4에서 JS와 맞춘 키
                'commentsText' => $comment['comments_text'],
                'commentsCheering' => $comment['comments_cheering'],
                'likesRowCount' => $likeStatus,
                'numberOfReply' => $numberOfReply
            ];
        }
    } catch (PDOException $e) {
        error_log("getCommentsForPost failed: " . $e->getMessage());
        // 에러 시 빈 배열 반환
    }

    // 6. 최종 배열 반환
    return $commentsResponse;
}

/**
 * 특정 부모 댓글의 대댓글 목록을 가져옵니다.
 * (post_detail_page.php와 post/load_replies.php가 공통으로 사용)
 *
 * @param PDO $pdo PDO 객체
 * @param int $boardNumber 게시글 번호
 * @param int $parentNumber 부모 댓글 번호
 * @param int $userNumber 현재 로그인한 사용자 번호 (좋아요 상태 확인용)
 * @param int $lastItemNumber 마지막으로 로드된 대댓글 ID (이 ID보다 큰 댓글을 로드)
 * @param int $limit 로드할 대댓글 개수
 * @return array 대댓글 정보가 담긴 배열
 */
function getRepliesForComment($pdo, $boardNumber, $parentNumber, $userNumber, $lastItemNumber = 0, $limit = 10) {

    $repliesResponse = []; // 반환할 빈 배열 선언

    // 1. (추가) 유효성 검사
    if ($boardNumber <= 0 || $parentNumber <= 0) {
        return $repliesResponse; // 빈 배열 반환
    }

    try {
        // 1. (변경) $sql: 'parent_number = :parentNumber' 조건이 추가됨
        $sql = "SELECT comments_number, comments.user_number, comments_text, 
                       comments_save_time, comments_cheering, user.nickname
                FROM comments LEFT JOIN user ON comments.user_number=user.user_number
                WHERE comments.board_number = :boardNumber 
                  AND comments.parent_number = :parentNumber 
                  AND comments.comments_number > :lastItemNumber
                ORDER BY comments.comments_number 
                LIMIT :limit";

        $stmt = $pdo->prepare($sql);

        // 2. (변경) 변수 바인딩 (parentNumber 추가)
        $stmt->bindParam(':boardNumber', $boardNumber, PDO::PARAM_INT);
        $stmt->bindParam(':parentNumber', $parentNumber, PDO::PARAM_INT);
        $stmt->bindParam(':lastItemNumber', $lastItemNumber, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        $allReplies = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. (변경) foreach 루프 (대댓글 개수 쿼리는 *필요 없음*)
        foreach ($allReplies as $reply) {
            $commentsNumber = (int)$reply['comments_number'];

            // 날짜 포맷팅
            $dateTime = new DateTime($reply['comments_save_time']);
            if ($dateTime->format('Y') === date('Y')) {
                $formattedDateTime = $dateTime->format('m. d. H:i');
            } else {
                $formattedDateTime = $dateTime->format('Y. m. d. H:i');
            }

            // 좋아요 상태 조회 (config/functions.php에 있는 함수 호출)
            $likeStatus = getLikeStatus($pdo, $userNumber, $boardNumber, $commentsNumber);

            // 4. (변경) 최종 배열에 추가
            $repliesResponse[] = [
                'userNumber' => $userNumber,
                'id' => $commentsNumber,
                'boardNumber' => $boardNumber,
                'writeUserNumber' => (int)$reply['user_number'],
                'writeUserNickname' => $reply['nickname'],
                'dateTime' => $formattedDateTime,
                'contents' => $reply['comments_text'],
                'cheering' => $reply['comments_cheering'],
                'likesRowCount' => $likeStatus
                // (참고: 대댓글의 대댓글(3단계)은 없으므로 'numberOfReply' 키는 불필요)
            ];
        }
    } catch (PDOException $e) {
        error_log("getRepliesForComment failed: " . $e->getMessage());
        // 에러 시 빈 배열 반환
    }

    // 5. 최종 배열 반환
    return $repliesResponse;
}
?>
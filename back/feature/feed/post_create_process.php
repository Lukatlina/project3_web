<?php
// 1. Config 파일 포함 (PDO, 세션)
include_once __DIR__ . '/../../../config/config.php';

// 2. 변수명을 camelCase로 변경
$userNumber = $_SESSION['user_number'] ?? null;
$divContent = $_POST['divContent'] ?? null;
$confirmTextLength = (int)($_POST['confirmText'] ?? 0); // 글자 수

// 3. 유효성 검사
if ($confirmTextLength > 10000 || empty($userNumber) || empty($divContent)) {
    echo "0"; // 실패
    exit();
}

// 4. (구조 변경) 트랜잭션과 Try/Catch가 모든 DB 작업을 감싸도록 수정
$pdo->beginTransaction(); 
try {
    
    // 5. (구조 변경) board 테이블 INSERT는 공통 작업이므로 먼저 실행
    $sqlBoard = "INSERT INTO board (user_number, contents, contents_save_time) VALUES (?, ?, NOW())";
    $stmtBoard = $pdo->prepare($sqlBoard);
    $stmtBoard->execute([$userNumber, $divContent]);

    // 6. (안전) 방금 INSERT된 board_number 가져오기
    $boardNumber = $pdo->lastInsertId();

    // 7. (기존 로직) 이미지/비디오가 있는지 확인
    $imagesJson = $_POST['images'] ?? [];
    $idArray = $_POST['id'] ?? [];
    $types = $_POST['widget-type'] ?? [];

    if (!empty($imagesJson)) {
        // --- 8a. 이미지/비디오가 첨부된 경우 ---
        $tempUploadDir = PROJECT_ROOT . '/uploads/temp/'; // (경로) 임시 폴더
        $finalUploadDir = PROJECT_ROOT . '/uploads/board/' . $boardNumber . '/'; // (경로) 최종 저장 폴더

        if (!is_dir($finalUploadDir)) {
            mkdir($finalUploadDir, 0755, true);
        }

        // PDO INSERT 준비
        $sqlVideo = "INSERT INTO video (board_number, video_path, video_id) VALUES (?, ?, ?)";
        $stmtVideo = $pdo->prepare($sqlVideo);
        $sqlImage = "INSERT INTO image (board_number, image_path, image_id, video_number, is_thumbnail) VALUES (?, ?, ?, ?, ?)";
        $stmtImage = $pdo->prepare($sqlImage);

        for ($i = 0; $i < count($imagesJson); $i++) {
            $imageInfo = json_decode($imagesJson[$i], true);
            if (json_last_error() !== JSON_ERROR_NONE) continue;

            $numericId = preg_replace("/[^0-9]/", "", $idArray[$i]);
            $type = $types[$i];

            // 8b. (수정됨) 썸네일/이미지 파일 이동 로직
            $thumbFileName = basename($imageInfo['fileName']);
            $tempThumbPath = $tempUploadDir . $thumbFileName;
            $finalThumbPath = $finalUploadDir . $thumbFileName;
            $finalThumbDbPath = BASE_PATH . '/uploads/board/' . $boardNumber . '/' . $thumbFileName; 

            if (file_exists($tempThumbPath) && copy($tempThumbPath, $finalThumbPath)) {
                unlink($tempThumbPath); // 임시 파일 삭제
            } else {
                throw new Exception("Failed to move thumbnail file: " . $thumbFileName);
            }

            // 8c. (수정됨) $type을 확인하여 비디오일 경우에만 비디오 파일 이동
            if ($type === 'video') {
                // 비디오 파일 이동
                $videoFileName = basename($imageInfo['videofileName']);
                $tempVideoPath = $tempUploadDir . $videoFileName;
                $finalVideoPath = $finalUploadDir . $videoFileName;
                $finalVideoDbPath = BASE_PATH . '/uploads/board/' . $boardNumber . '/' . $videoFileName;
                
                if (file_exists($tempVideoPath) && copy($tempVideoPath, $finalVideoPath)) {
                    unlink($tempVideoPath); 
                } else {
                    throw new Exception("Failed to move video file: " . $videoFileName);
                }

                // video 테이블 INSERT
                $stmtVideo->execute([$boardNumber, $finalVideoDbPath, $numericId]);
                $videoNumber = $pdo->lastInsertId();

                // image(썸네일) 테이블 INSERT
                $stmtImage->execute([$boardNumber, $finalThumbDbPath, $numericId, $videoNumber, 1]);
            
            } else if ($type === 'photo') {
                // image(사진) 테이블 INSERT
                $stmtImage->execute([$boardNumber, $finalThumbDbPath, $numericId, null, 0]);
            }
        } // end for
    }

    
    // 9. 모든 쿼리가 성공했으므로 DB에 최종 반영
    $pdo->commit();
    echo "1"; // 성공

} catch (\Exception $e) { // PDOException + Exception
    // 10. 쿼리 중 하나라도 실패하면 모든 변경사항 롤백
    $pdo->rollBack();
    error_log("Post creation failed: " . $e->getMessage()); 
    echo "0"; // 실패
}

?>
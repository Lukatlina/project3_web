<?php
// 1. Config 파일 포함 (PDO, 세션)
include_once '../config/config.php';

// 2. 변수명을 camelCase로 변경
$userNumber = $_SESSION['user_number'] ?? null;
$boardNumber = (int)($_POST['board_number'] ?? 0); // (★수정) board_number를 받음
$divContent = $_POST['divContent'] ?? null;
$confirmTextLength = (int)($_POST['confirmText'] ?? 0);

// 3. 유효성 검사
if ($confirmTextLength > 10000 || empty($userNumber) || empty($divContent) || empty($boardNumber)) {
    echo "0"; // 실패
    exit();
}

// 4. (★핵심) PDO Transaction 시작
$pdo->beginTransaction();
try {
    // (★보안) 본인 확인: 이 글이 정말 내 글인지 확인
    $sqlCheck = "SELECT user_number FROM board WHERE board_number = ?";
    $stmtCheck = $pdo->prepare($sqlCheck);
    $stmtCheck->execute([$boardNumber]);
    $owner = $stmtCheck->fetchColumn(); // user_number만 가져옴

    if ($owner != $userNumber) {
         throw new Exception("Forbidden: You are not the owner.");
    }

    // 5. (★변경) board 테이블 UPDATE (PDO)
    $sqlBoard = "UPDATE board SET contents = ?, contents_update_time = NOW() 
                 WHERE board_number = ? AND user_number = ?";
    $stmtBoard = $pdo->prepare($sqlBoard);
    $stmtBoard->execute([$divContent, $boardNumber, $userNumber]);

    // 6. (★변경) 기존의 모든 image/video 레코드 삭제 (새 파일로 덮어쓰기 위함)
    $sqlDelete = "DELETE FROM image WHERE board_number = ?";
    $pdo->prepare($sqlDelete)->execute([$boardNumber]);
    $sqlDelete = "DELETE FROM video WHERE board_number = ?";
    $pdo->prepare($sqlDelete)->execute([$boardNumber]);

    // 7. (★중요) 임시 폴더(uploads) -> 최종 폴더(upload_images)로 파일 이동 및 DB 저장
    $imagesJson = $_POST['images'] ?? [];
    $idArray = $_POST['id'] ?? [];
    $types = $_POST['widget-type'] ?? [];

    if (!empty($imagesJson)) {
        // (이후 로직은 post_create_process.php와 동일)
        $tempUploadDir = '../uploads/';
        $finalUploadDir = '../upload_images/' . $boardNumber . '/';

        if (!is_dir($finalUploadDir)) {
            mkdir($finalUploadDir, 0755, true);
        }

        $sqlVideo = "INSERT INTO video (board_number, video_path, video_id) VALUES (?, ?, ?)";
        $stmtVideo = $pdo->prepare($sqlVideo);
        $sqlImage = "INSERT INTO image (board_number, image_path, image_id, video_number, is_thumbnail) VALUES (?, ?, ?, ?, ?)";
        $stmtImage = $pdo->prepare($sqlImage);

        for ($i = 0; $i < count($imagesJson); $i++) {
            $imageInfo = json_decode($imagesJson[$i], true);
            if (json_last_error() !== JSON_ERROR_NONE) continue;
            $numericId = preg_replace("/[^0-9]/", "", $idArray[$i]);
            $type = $types[$i];

            $thumbFileName = basename($imageInfo['fileName']);
            $tempThumbPath = $tempUploadDir . $thumbFileName;
            $finalThumbPath = $finalUploadDir . $thumbFileName;
            $finalThumbDbPath = 'upload_images/' . $boardNumber . '/' . $thumbFileName; 

            if (file_exists($tempThumbPath) && copy($tempThumbPath, $finalThumbPath)) {
                unlink($tempThumbPath);
            } else {
                throw new Exception("Failed to move thumbnail file: " . $thumbFileName);
            }

            if ($type === 'video') {
                $videoFileName = basename($imageInfo['videofileName']);
                $tempVideoPath = $tempUploadDir . $videoFileName;
                $finalVideoPath = $finalUploadDir . $videoFileName;
                $finalVideoDbPath = 'upload_images/' . $boardNumber . '/' . $videoFileName; 

                if (file_exists($tempVideoPath) && copy($tempVideoPath, $finalVideoPath)) {
                    unlink($tempVideoPath); 
                } else {
                    throw new Exception("Failed to move video file: " . $videoFileName);
                }

                $stmtVideo->execute([$boardNumber, $finalVideoDbPath, $numericId]);
                $videoNumber = $pdo->lastInsertId();
                $stmtImage->execute([$boardNumber, $finalThumbDbPath, $numericId, $videoNumber, 1]);

            } else if ($type === 'photo') {
                $stmtImage->execute([$boardNumber, $finalThumbDbPath, $numericId, null, 0]);
            }
        } // end for
    }

    // 9. (★핵심) 모든 쿼리가 성공하면 DB에 최종 반영
    $pdo->commit();
    echo "1"; // 성공

} catch (\Exception $e) { // PDOException + Exception
    // 10. (★핵심) 쿼리 중 하나라도 실패하면 모든 변경사항 롤백
    $pdo->rollBack();
    error_log("Post update failed: " . $e->getMessage()); 
    echo "0"; // 실패
}
?>
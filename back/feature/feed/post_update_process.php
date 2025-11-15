<?php
// 1. Config 파일 포함 (PDO, 세션)
include_once __DIR__ . '/../../../config/config.php';

// AJAX 요청이 POST 방식으로 전송된 경우
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // $result 변수 초기화 (오류 방지)
    $result = false; 

    // PDO는 오류 발생 시 예외를 던지므로 try-catch로 감쌉니다.
    try {
        // 보낸 변수 모두 저장 : id, images, board_number, divContent, confirmText
        $user_number = $_SESSION['user_number'];
        $board_number = $_POST['board_number'];
        $divContent = $_POST['divContent'];
        $idArray = $_POST['id'] ?? [];
        $images = $_POST['images'] ?? [];
        $types = $_POST['widget-type'] ?? [];

        // 1. 이미지 유무를 먼저 확인한다.
        if (!empty($images)) {
            // 텍스트 먼저 업데이트 하기
            // $pdo 객체를 함수에 전달합니다.
            updateDivContent($pdo, $board_number, $user_number, $divContent);

            // 2. [수정] 폴더 경로를 PROJECT_ROOT (서버 파일 시스템 경로) 기준으로 설정
            $finalUploadDir = PROJECT_ROOT . '/uploads/board/' . $board_number;
            if (!is_dir($finalUploadDir)) {
                mkdir($finalUploadDir, 0755, true); // 재귀적으로 생성
            }
            // [수정] 임시 파일이 있는 폴더 경로
            $tempUploadDir = PROJECT_ROOT . '/uploads/temp/';

            // 3. 파일 처리 루프
            for ($i = 0; $i < count($images); $i++) {
                // JSON 디코딩
                $imageInfo = json_decode($images[$i], true);
                if (json_last_error() !== JSON_ERROR_NONE) continue; // JSON 파싱 실패 시 건너뛰기

                $fileName = $imageInfo['fileName']; // 썸네일 파일 이름
                
                // [수정] 원본 임시 파일 경로 (JS가 보낸 destinationPath는 웹 경로이므로 사용X)
                $tempThumbPath = $tempUploadDir . $fileName;

                // [수정] 최종 저장될 파일 시스템 경로
                $finalThumbPath_Filesystem = $finalUploadDir . '/' . $fileName;
                
                // [수정] DB에 저장될 웹 접근 가능 경로
                $finalThumbPath_DB = BASE_PATH . '/uploads/board/' . $board_number . '/' . $fileName;

                $type = $types[$i];
                $id = preg_replace("/[^0-9]/", "", $idArray[$i]);

                // 4. 임시 썸네일 파일을 최종 경로로 복사
                if (file_exists($tempThumbPath)) {
                    if (!copy($tempThumbPath, $finalThumbPath_Filesystem)) {
                        throw new Exception("썸네일 파일 복사 실패: " . $fileName);
                    }
                } else {
                     throw new Exception("임시 썸네일 파일 없음: " . $fileName);
                }

                // 5. 타입에 따라 분기
                if ($type === 'video') {
                    // 영상 파일 이름 및 경로 처리
                    $videoFileName = $imageInfo['videofileName'];
                    $tempVideoPath = $tempUploadDir . $videoFileName;
                    $finalVideoPath_Filesystem = $finalUploadDir . '/' . $videoFileName;
                    $finalVideoPath_DB = BASE_PATH . '/uploads/board/' . $board_number . '/' . $videoFileName;
                    
                    // 5-1. 임시 비디오 파일을 최종 경로로 복사
                    if (file_exists($tempVideoPath)) {
                        if (!copy($tempVideoPath, $finalVideoPath_Filesystem)) {
                             throw new Exception("비디오 파일 복사 실패: " . $videoFileName);
                        }
                    } else {
                        throw new Exception("임시 비디오 파일 없음: " . $videoFileName);
                    }

                    // 5-2. video 테이블에 INSERT (prepared statement 사용)
                    $video_sql = "INSERT INTO video (board_number, video_path, video_id) VALUES (?, ?, ?)";
                    $stmt_vid_insert = $pdo->prepare($video_sql);
                    // [수정] $finalVideoPath_DB (웹 경로)를 저장
                    $stmt_vid_insert->execute([$board_number, $finalVideoPath_DB, $id]);

                    // 5-3. 방금 삽입된 비디오의 PK (video_number) 가져오기
                    $new_video_number = $pdo->lastInsertId();

                    // 5-4. image 테이블에 썸네일 정보 저장 (UPDATE 또는 INSERT)
                    // [수정] $finalThumbPath_DB (웹 경로)를 저장
                    $result = updateImage($pdo, $board_number, $id, $finalThumbPath_DB, $new_video_number);

                } else if ($type === 'photo') {
                    // 5-5. image 테이블에 사진 정보 저장 (UPDATE 또는 INSERT)
                    // [수정] $finalThumbPath_DB (웹 경로)를 저장
                    $result = updateImage($pdo, $board_number, $id, $finalThumbPath_DB, null); // video_number는 NULL
                }
            } // end for loop

            // 6. 임시 폴더 안의 모든 파일 삭제 (루프가 끝난 후)
            $tempDirToClean = PROJECT_ROOT . '/uploads/temp/'; // [수정] 정확한 임시 폴더 경로
            $files = glob($tempDirToClean . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            
            // 최종 결과 반환 (true/false를 1/0으로)
            echo $result ? "1" : "0";

        } else if (empty($_POST['images'])) {
            // 이미지가 없다면 텍스트만 저장
            $result = updateDivContent($pdo, $board_number, $user_number, $divContent);
            echo $result ? "1" : "0";
        
        } else {
            echo "0"; // 'No data were uploaded.'
        }

    } catch (PDOException $e) {
        // 데이터베이스 관련 오류 처리
        error_log("DB 오류: " . $e->getMessage()); // 오류 로그에 기록
        echo "0"; // 실패
    } catch (Exception $e) {
        // 기타 일반 오류 처리 (파일 이동 실패 등)
        error_log("일반 오류: " . $e->getMessage()); // 오류 로그에 기록
        echo "0"; // 실패
    } finally {
        // 스크립트 종료 시 PDO 연결 닫기 (config.php에서 연결을 관리한다면 불필요)
        $pdo = null;
    }
}

// --- 함수 정의 ---
// [수정] 모든 함수가 $pdo 객체를 받도록 하고, prepared statement를 사용합니다.

function updateDivContent($pdo, $board_number, $user_number, $divContent) {
    // include 'server_connect.php'; // [제거]
    if ($_POST['confirmText'] < 10000) {
        // [수정] SQL 인젝션 방지
        $sql = "UPDATE board
                SET contents = ?,
                contents_update_time = now()
                WHERE board_number = ? AND user_number = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$divContent, $board_number, $user_number]);
        return $result; // execute()는 성공 시 true, 실패 시 false 반환
    } else {
        return false;
    }
}

function findSavedImage($pdo, $board_number, $id) {
    // include 'server_connect.php'; // [제거]
    // [수정] SQL 인젝션 방지
    $sql = "SELECT COUNT(*) FROM image WHERE board_number = ? AND image_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$board_number, $id]);
    
    // [수정] rowCount() 대신 COUNT(*) 값 가져오기
    $result = $stmt->fetchColumn(); 
    return $result; // 0 또는 1 (혹은 그 이상)
}

function updateImage($pdo, $board_number, $id, $destinationPath_DB, $new_video_number) {
    // include 'server_connect.php'; // [제거]
    
    // [수정] findSavedImage 함수에도 $pdo 전달
    if (findSavedImage($pdo, $board_number, $id) === 0) {
        // [수정] INSERT: prepared statement 사용
        $image_sql = "INSERT INTO image (
                          board_number, image_path, image_id, video_number, is_thumbnail
                      ) VALUES (?, ?, ?, ?, ?)"; // is_thumbnail은 video 여부에 따라 달라져야 함
        $is_thumbnail = ($new_video_number !== null) ? 1 : 0;
        $stmt = $pdo->prepare($image_sql);
        $result = $stmt->execute([$board_number, $destinationPath_DB, $id, $new_video_number, $is_thumbnail]);
        return $result;

    } else { // 1 이상일 때 (이미 존재할 때)
        // [수정] UPDATE: prepared statement 사용
        $sql = "UPDATE image SET image_path = ?, video_number = ?, is_thumbnail = ?
                WHERE board_number = ? AND image_id = ?";
        $is_thumbnail = ($new_video_number !== null) ? 1 : 0;
        $stmt = $pdo->prepare($sql);
        // [수정] image_id는 WHERE 조건에만 씁니다. (덮어쓰기 방지)
        $result = $stmt->execute([$destinationPath_DB, $new_video_number, $is_thumbnail, $board_number, $id]);
        return $result;
    }
}
?>
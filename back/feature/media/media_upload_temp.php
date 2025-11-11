<?php
include_once '../config/config.php';
// TODO:
/*
4. 1초 부분의 이미지를 추출하고 기존과 같이 저장한다.
5. submit을 눌렀을 때 uploads/board_number/로 폴더가 생성되고 모든 관련 파일을 옮긴다.
6. DB에 이미지와 영상이 저장된다.
*/

$responseArray = []; // JS에게 보낼 최종 응답(파일 목록)

try {
    if (empty($_FILES['files']) || empty($_SESSION['user_number'])) {
        throw new Exception('No files or user session.');
    }

    $files = $_FILES['files'];
    $uploadDir = '../uploads/'; // (★경로) 임시 업로드 폴더
    $userNumber = $_SESSION['user_number'];

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // 2. (기존 로직) 여러 파일 처리
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            throw new Exception("File upload error: " . $files['error'][$i]);
        }

        $tempFilePath = $files['tmp_name'][$i];
        $fileType = $files["type"][$i];
        $originalName = $files['name'][$i];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $uniqueId = uniqid('', true); // 고유 ID 생성

        $item = []; // 이 파일 1개에 대한 정보

        // 3. (기존 로직) 파일 타입 분기
        if (strpos($fileType, 'video') !== false) {
            // --- 1. 비디오 처리 ---

            // 1a. (★변경) 원본 비디오 대신 인코딩된 비디오 저장
            $videoFileName = $userNumber . '_' . $uniqueId . '_encoded.mp4';
            $videoDestinationPath = $uploadDir . $videoFileName;

            // (보안) escapeshellarg로 명령어 실행 보안 강화
            $command = sprintf(
                'ffmpeg -i %s -c:v libx264 -c:a aac %s',
                escapeshellarg($tempFilePath),
                escapeshellarg($videoDestinationPath)
            );
            exec($command, $output, $returnCode);
            if ($returnCode !== 0) {
                throw new Exception('FFmpeg encoding failed for ' . $originalName);
            }

            // 1b. 썸네일 추출
            $thumbFileName = $userNumber . '_' . $uniqueId . '_thumbnail.jpg';
            $thumbDestinationPath = $uploadDir . $thumbFileName;
            $thumbnailTime = "00:00:01";

            $ffmpegCommand = sprintf(
                'ffmpeg -i %s -ss %s -vframes 1 %s',
                escapeshellarg($videoDestinationPath), // 인코딩된 영상에서 썸네일 추출
                $thumbnailTime,
                escapeshellarg($thumbDestinationPath)
            );
            exec($ffmpegCommand, $output, $returnCode);
            if ($returnCode !== 0) {
                throw new Exception('FFmpeg thumbnail extraction failed for ' . $originalName);
            }

            // 1c. (★중요) JS가 원하는 JSON 형식 맞추기
            $item = [
                "destinationPath" => $thumbDestinationPath,    // 썸네일 경로
                "fileName" => $thumbFileName,            // 썸네일 파일명
                "videodestinationPath" => $videoDestinationPath, // 인코딩된 비디오 경로
                "videofileName" => $videoFileName,         // 인코딩된 비디오 파일명
            ];

        } else if (strpos($fileType, 'image') !== false) {
            // --- 2. 이미지 처리 ---
            $imageFileName = $userNumber . '_' . $uniqueId . '.' . $extension;
            $imageDestinationPath = $uploadDir . $imageFileName;

            if (!move_uploaded_file($tempFilePath, $imageDestinationPath)) {
                throw new Exception('Failed to move image file: ' . $originalName);
            }

            // 2a. (★중요) JS가 원하는 JSON 형식 맞추기
            $item = [
                "destinationPath" => $imageDestinationPath,
                "fileName" => $imageFileName
            ];
        } else {
            // 지원하지 않는 파일
            continue;
        }

        $responseArray[] = $item; // 배열에 파일 정보 추가
    }

    // 4. (변경) JS가 받을 수 있도록 JSON으로 응답
    header('Content-Type: application/json');
    echo json_encode($responseArray);

} catch (\Exception $e) {
    // 5. 파일 이동/인코딩 실패
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
<?php
// 1. (변경) config.php의 세션 시작 기능을 사용합니다.
include_once __DIR__ . '/../../../config/config.php';

setcookie("UserToken", "", time() - (86400 * 30) , "/");

// 3. (기존 로직 유지) 세션 파일 삭제
session_destroy();

exit();
?>
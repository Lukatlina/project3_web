<?php
// 1. (변경) config.php의 세션 시작 기능을 사용합니다.
include_once '../config/config.php';

setcookie("UserToken", "", time() - (86400 * 30) , "/");

// 3. (기존 로직 유지) 세션 파일 삭제
session_destroy();

// 4. (개선) 로그아웃 후 메인 페이지로 이동시킵니다.
header("Location: ../weverse_main.php");
exit();
?>
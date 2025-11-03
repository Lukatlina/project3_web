<?php
// 이 스크립트에서 발생하는 모든(ALL) 종류의 PHP 에러와 경고, 알림을 보고 report
error_reporting(E_ALL);
// 1번에서 보고된 그 에러들을 화면(display)에 보이도록 설정
ini_set('display_errors', 1);

//세션 ID(session_id)가 아직 존재하지 않는다면(!), 지금 바로 세션(session)을 시작(start)
if (!session_id()) {
    session_start();
}
?>
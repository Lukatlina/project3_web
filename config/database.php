<?php
$dbHost = "127.0.0.1";
$dbName = "weverse";
$dbUser = "lunamoon";
$dbPass = "digda1210";
// 통신할 때 사용할 언어 세트입니다.
$charset = "utf8mb4";

// DSN(Data Source Name)은 PDO에 접속 정보를 알려주는 "명령서"
// mysql 타입에 접속, 주소는 $dbHost, DB이름은 $dbName
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$charset";
$options = [
    // (가장 중요) DB 에러 발생 시, 경고(Warning) 대신 예외(Exception)를 발생시킵니다.
    // 이 설정 덕분에 4번의 'try...catch' 구문이 에러를 잡아낼 수 있습니다.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // DB에서 데이터를 가져올 때, ['nickname' => 'test'] 처럼 '연관 배열'(Key-Value)로만 가져오게 합니다.
    // (이걸 안 하면 [0 => 'test', 'nickname' => 'test'] 처럼 중복 데이터를 가져옵니다.)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // (보안) PDO의 "가짜 Prepared Statements" 기능을 끄고,
    // MariaDB가 제공하는 "진짜 Prepared Statements"를 사용하도록 강제합니다.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // 1~3단계에서 만든 정보($dsn, $dbUser, $dbPass, $options)를 사용해
    // 'new PDO' (새로운 DB 연결 객체)를 생성(접속 시도)합니다.
    // 접속에 성공하면, 앞으로 다른 파일에서 이 `$pdo` 변수를 통해 DB를 제어할 수 있습니다.
     $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (\PDOException $e) {
    // 만약 접속 시도(try)가 실패하면(catch) (예: 비밀번호가 틀리면),
     // 프로그램을 즉시 멈추고 에러 메시지를 화면에 출력합니다.
     // (이것이 500 에러 대신 정확한 원인을 볼 수 있게 해줬던 코드입니다. 
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
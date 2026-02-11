<?php
/* ━━━━━━━━━ 0. 접속 보호 ━━━━━━━━━ */
$realm = 'Mangoi Secure Area';
$pass  = 'mangoi@#0505#';

if (!isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW'] !== $pass) {
    header("WWW-Authenticate: Basic realm=\"$realm\"");
    header('HTTP/1.0 401 Unauthorized');
    echo '비밀번호를 입력해야 합니다.';
    exit;
}

/* ━━━━━━━━━ 1. 개발용 오류 표시 ━━━━━━━━━ */
ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ━━━━━━━━━ 2. DB 연결 ━━━━━━━━━ */
$pdo = new PDO(
    'mysql:host=localhost;dbname=mangoi;charset=utf8mb4',
    'mangoi', 'mi!@#2019',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4']
);

/* ━━━━━━━━━ 3. AES-복호 유틸 ━━━━━━━━━ */
function mysqlAesKey(string $k): string {
    $r = str_repeat("\0", 16);
    for ($i=0,$l=strlen($k); $i<$l; $i++) $r[$i%16] = $r[$i%16] ^ $k[$i];
    return $r;
}
function dec(string $hex, string $rawKey): string {
    if ($hex==='' || strlen($hex)%2 || !ctype_xdigit($hex)) return '';
    $plain = openssl_decrypt(hex2bin($hex), 'AES-128-ECB',
        mysqlAesKey($rawKey), OPENSSL_RAW_DATA);
    return $plain===false ? '' : trim($plain);
}

/* ━━━━━━━━━ 4. 기간 파라미터 ━━━━━━━━━ */
$today  = date('Y-m-d');
$first  = date('Y-m-01');
$start  = $_GET['start'] ?? $first;
$end    = $_GET['end']   ?? $today;
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$start)) $start=$first;
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$end))   $end=$today;
if (strtotime($start) > strtotime($end)) [$start,$end]=[$end,$start];

/* ━━━━━━━━━ 5. 조회 실행 여부 ━━━━━━━━━ */
$queried = isset($_GET['start']) && isset($_GET['end']);

/* ━━━━━━━━━ 6. 데이터 조회(필요 시) ━━━━━━━━━ */
$tsv ="이름\t전화번호\n";            // 기본 헤더
$csv = "\xEF\xBB\xBF"."이름,전화번호\r\n";
$csvReady = false;

if ($queried) {
    $encKey = md5('kr.ahsol');
    $stmt   = $pdo->prepare(
        "SELECT m.MemberName, m.MemberPhone1 enc
         FROM Members m
         JOIN Classes c ON c.MemberID=m.MemberID
         WHERE c.StartDateTime BETWEEN :a AND :b
           AND c.ClassAttendState NOT IN (0,99)
           AND m.MemberPhone1 <> ''");
    $stmt->execute([
        ':a'=>$start.' 00:00:00',
        ':b'=>$end  .' 23:59:59'
    ]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $valid = '/^\d{3}-\d{4}-\d{4}$/';
    $uniq  = [];
    foreach ($rows as $r){
        $p=dec($r['enc'],$encKey);
        if($p===''||!preg_match($valid,$p)) continue;
        $uniq[$p]=$r['MemberName'];
    }
    ksort($uniq);
    foreach ($uniq as $p=>$n){
        $safe=str_replace(["\t","\n","\r"],' ',$n);
        $tsv .= "$safe\t$p\n";
        $csv .= '"'.str_replace('"','""',$safe)."\",$p\r\n";
    }
    $csvReady = true;
    $csvB64   = base64_encode($csv);
}

/* ━━━━━━━━━ 7. HTML 출력 ━━━━━━━━━ */
?>
<!doctype html><html lang="ko"><head>
    <meta charset="utf-8">
    <title>학생 연락처 조회</title>
    <style>
        body{font-family:Consolas,monospace;margin:2rem}
        label{margin-right:.5rem}
        input[type=date]{padding:.2rem}
        button{padding:.35rem .9rem;border:0;border-radius:4px;font-size:.9rem;cursor:pointer}
        #dlBtn[disabled]{background:#aaa;color:#fff;cursor:not-allowed}
        #dlBtn:not([disabled]){background:#0069d9;color:#fff}
        pre{white-space:pre;background:#f7f7f7;border:1px solid #ddd;padding:1rem;margin-top:1rem}
    </style>
</head><body>

<h1>학생 연락처 조회</h1>
<h2>(시작일 ~ 종료일 구간 내 수업이 존재하는 학생 연락처 목록을 반환합니다)</h2>
<br>
<h4>- 오기입/비정상적인 전화번호는 목록에서 생략되었습니다.</h4>
<h4>- 수업이 생성된 이력이 있었으나, 현재 삭제된 수업이거나 미등록 상태이면 목록에 노출되지 않습니다.</h4>
<h4>- 조회 기간을 설정하시고, 조회 버튼을 클릭하시면 잠시 뒤 CSV 다운로드 버튼이 활성화됩니다.</h4>
<h4>- 학생명, 휴대폰 번호는 민감 정보 이므로 사용 후 즉시 삭제하시고, 보관 시 암호를 걸어 보관하시기를 권장합니다.</h4>
<br>

<form>
    <label>시작일
        <input type="date" name="start" value="<?=htmlspecialchars($start)?>">
    </label>
    <label>종료일
        <input type="date" name="end" value="<?=htmlspecialchars($end)?>">
    </label>
    <button type="submit">조회</button>
    <button type="button" id="dlBtn" <?= $csvReady?'':'disabled' ?>>
        <?= $csvReady?'CSV 다운로드':'조회 대기 중...' ?>
    </button>
</form>

<?php if($queried): ?>
    <p>조회 기간: <strong><?=$start?></strong> ~ <strong><?=$end?></strong></p>
    <pre><?=htmlspecialchars($tsv,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8')?></pre>
<?php endif; ?>

<?php if($csvReady): ?>
    <script>
        document.getElementById('dlBtn').addEventListener('click',function(){
            const link=document.createElement('a');
            link.href='data:text/csv;base64,<?=$csvB64?>';
            link.download='student_list_<?=str_replace("-","",$start)?>_<?=str_replace("-","",$end)?>.csv';
            document.body.appendChild(link);link.click();link.remove();
        });
    </script>
<?php endif; ?>

</body></html>

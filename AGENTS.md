# Repository Guidelines

## 프로젝트 구조 및 모듈 구성
- 루트: 레거시 PHP 앱(문서 루트 = 리포지토리 루트, `index.php`가 메인 진입점).
- 코어 인클루드: `includes/` (DB 연결: `dbopen.php`, 공통 설정/헬퍼: `common.php`, 인증/세션 체크: `member_check.php`, 공통 헤더/푸터).
- 프런트 자산: `css/`, `js/`, `images/`, `fonts/`, `assets/`, `site_icons/`, `sounds/`.
- 기능 영역
  - 웹/관리: `mypage/`, `lms/`, `dms/`, `tms/`, `api/`
  - 앱/버전별 엔드포인트: `app_v20/`, `app_v21/`, `app_v22/` (JSONP/파일 업로드 등 다수)
  - 푸시/배치(크론): `app_push_cron/` (CLI 실행 전제, 운영 데이터 주의)
  - DB 유틸 페이지: `db_pages/` (점검/관리 목적, 운영 데이터 주의)
- 에디터/서드파티/벤더 복사본(가능하면 수정 최소화)
  - 문서 뷰어/리더: `ViewerJS/`
  - 엑셀: `PHPExcel-1.8/`, `tms/PHPExcel-1.8/`
  - 차트/캘린더/UI: `amcharts/`, `amcharts4/`, `kendo/`, `datetimepicker-master/`, `js/datetimepicker/`, `js/tooltipster/`
  - WYSIWYG: `editors/` (Froala 등)
  - 결제/연동: `kcp_batch_pc/`, `kcp_batch_mobile/`, `popbill/`, `google_maps/`
- WebSocket 샌드박스: `ws/` (Python `SimpleWebSocketServer` 포함, 본 서비스와 분리된 예제 성격)

## 빌드, 테스트, 로컬 개발
- 필수 런타임: PHP(현재 CLI 기준 7.3.x), MySQL, PHP 확장 `pdo_mysql`(필수) + `mbstring`/`gd`/`curl`(기능에 따라 필요).
- 로컬 실행(간단): `php -S localhost:8000 -t .`
  - 주의: `includes/common.php`에서 HTTPS 강제 리다이렉트가 있어 로컬 HTTP에서 바로 접속이 막힐 수 있음.
  - 빠른 우회(개발용): `HTTPS=on php -S localhost:8000 -t .` (환경변수로 `$_SERVER['HTTPS']`가 잡히는 환경에서만 유효)
  - 권장(개발용): 로컬 리버스 프록시(Caddy/Nginx 등)로 HTTPS 종료 후 PHP로 프록시하거나, 개발 브랜치에서 리다이렉트 로직을 조건부로 처리.
- 로컬 실행 시 자주 막히는 지점
  - `includes/common.php`의 Sentry 초기화가 운영 경로(`require '/home/hosting_users/mangoi/www/vendor/autoload.php';`)에 하드코딩되어 로컬에서 `require` 에러가 날 수 있음.
  - 해결은 보통 (1) 개발 환경에서만 Sentry 블록 비활성화/가드 처리 또는 (2) 로컬에 해당 경로를 맞추는 방식(심볼릭 링크 등) 중 하나로 함.
- 서버 대안(Apache/Nginx): 문서 루트를 리포지토리 루트로 지정, `includes/` 및 각 모듈 디렉터리 읽기 권한 보장.
- Composer/Node(일부 서브모듈에만 존재)
  - `lms/`: `composer.json` 기반(필요 시 `composer install`), `lms/vendor/`가 이미 포함되어 있을 수 있음.
  - `ViewerJS/`, `datetimepicker-master/`, `google_maps/*`, `editors/*` 등: `package.json` 존재(수정 시 각 디렉터리에서 `npm install`/빌드가 필요할 수 있음).

## 코딩 스타일 및 네이밍
- PHP: 탭 들여쓰기 유지(기존 파일 스타일 존중).
- PHP 오픈 태그: `<?`(short tag)와 `<?php`가 혼재 — 파일 단위로 일관되게 유지(부분적으로 섞어서 바꾸지 않기). 로컬/서버에서 `short_open_tag=On`이 필요한 파일이 많음.
- 인코딩 주의: 레거시 자산/템플릿이 섞여 있어 파일 인코딩/개행(CRLF) 변경으로 인한 깨짐이 발생할 수 있음(대량 자동 포맷팅 지양).
- 파일명: 소문자+언더스코어 예) `ajax_set_class_order.php`.
- 함수/변수: 파일 내부 관례 준수, 교차 파일 전역 리네이밍 지양.
- HTML/CSS/JS: 인라인 스크립트 최소화, 자산은 `css/`, `js/`에 배치.

## 테스트 지침
- 공식 테스트 스위트 없음 — 영향 범위 스모크 테스트 수행.
- 검증: 로그인/세션, 메인(`index.php`), 변경한 페이지/모듈, 관련 `ajax_*.php`/`jsonp_*.php` 엔드포인트.
- 데이터: 개발 DB 사용(운영 DB에 직접 연결 금지), 배치/정산/결제 관련 스크립트는 특히 파괴적 작업 금지.

## 커밋 및 PR 가이드
- 커밋: 간결한 명령형 요약; 한/영 가능. 모듈/페이지 표기 예) "수업 통합 배정: 중복 배정 방지".
- 브랜치: `feature/<scope>`, `fix/<scope>`; 기본 타깃은 `develop`.
- PR: 문제 요약, 변경 사항, 영향 페이지/엔드포인트, 재현/검증 단계, UI 변경은 스크린샷 포함.

## 보안 및 구성 팁
- 시크릿/키: `includes/dbopen.php`(DB 계정), `includes/common.php`(Sentry DSN/연동키 등)에는 민감정보가 포함될 수 있으니 공유/외부 유출 금지.
- 암호화 키: `includes/common.php`의 `$EncryptionKey`는 변경 시 데이터 복구 불가 성격이므로 변경 금지(관련 로직 수정 시 영향 범위 확인 필수).
- 접근 제어: 페이지/AJAX/JSONP 엔드포인트는 인증 체크 포함 여부를 확인(`includes/member_check.php`, `includes/member_check_app.php` 등과 일치).
- 운영 가드: `includes/dbopen.php`의 점검 모드/허용 IP 로직(`$site_status`, `$allowed_ip`) 변경 시 서비스 영향이 크므로 주의.

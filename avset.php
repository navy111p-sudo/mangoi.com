<!DOCTYPE html>
<html>
<head>
    <title>BODA 장치 설정 마법사</title>
    <style>
        /* 중앙 정렬을 위한 스타일 */
        html, body {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f0f0; /* 배경색 설정 */
        }
        .loader {
            /* 로더 이미지 크기 조정 가능 */
            width: 100px;
            height: 100px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="./bodaApi.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            // 페이지 로딩 시 alert 창 표시
            alert("BODA(보다) 앱/프로그램이 실행되면 정상적으로 설치된 것입니다.\n\n만약 실행되지 않는다면 BODA 앱을 재설치해주세요.\n\n(*) 윈도우 PC 이외의 기기에서는 BODA 앱이 실행된 직후, 종료될 수 있으나 이는 정상동작입니다.");


            // MvApi 기본 설정
            MvApi.defaultSettings({
                tcps: {
                    // key: 'MTgwLjE1MC4yMzAuMTk1OjcwMDE',
                    key: 'MTIxLjE3MC4xNjQuMjMxOjcwMDE',
                    secondIp: false
                },
                company: {
                    code: 2, // 여기에 올바른 회사 코드를 입력하세요.
                    authKey: '1577840400',
                    siteId: ''
                },
                client: {
                    encrypt: false,
                    windows: {
                        product: 'BODA'
                    },
                    mobile: {
                        store: false,
                        Scheme: 'mangoi',
                        Packagename: 'zone.mangoi'
                    },
                    mac: {
                        store: false,
                        Scheme: 'mangoi',
                        Packagename: 'zone.mangoi.mac'
                    },
                    language: 'ko',
                    theme: 3,
                    btnType: 1,
                    appMode: 2
                },
                agent: {
                    port: {http: 13741, https: 13741},
                    onlyHttps: true
                },
                web: {
                    url: 'https://www.mangoiclass.co.kr:8080'
                }
            });

            // BODA 장치 설정 마법사 실행
            MvApi.avset({}, function() {}, function() {});
        });
    </script>
</head>
<body>
<img class="loader" src="images/loading.gif" alt="Loading..." />
</body>
</html>
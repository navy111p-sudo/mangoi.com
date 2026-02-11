<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script
        src="https://sentry.datacredit.kr/js-sdk-loader/b54b113cf958c1cf9a890d1431ff0ae3.min.js"
        crossorigin="anonymous"
        onload="window.sentryOnLoad()"
></script>

<style>
    /* 오버레이 & 카드 모달 기본 스타일 (기존 스타일 유지) */
    #fb-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);display:flex;justify-content:center;align-items:center;z-index:9999}
    #fb-card{background:#fff;border-radius:12px;width:360px;max-width:90%;padding:24px;font-family:-apple-system,BlinkMacSystemFont,"Apple SD Gothic Neo",sans-serif;box-shadow:0 4px 18px rgba(0,0,0,.25)}
    #fb-card h3{margin:0 0 8px;font-size:1.2rem}
    #fb-card p {margin:0 0 16px;font-size:.9rem;line-height:1.45}
    #fb-card input,#fb-card textarea{width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;font-size:.9rem;margin-bottom:10px}
    #fb-card button{border:none;border-radius:6px;padding:8px 12px;font-size:.85rem;cursor:pointer}
    #fb-shot {background:#5c6bc0;color:#fff}
    #fb-send {background:#43a047;color:#fff;margin-left:6px}
    #fb-close{background:none;color:#666;float:right;margin-top:-8px}
    #fb-screenshots-container { display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 10px; }
    #fb-screenshots-container img { max-width: 100px; max-height: 75px; border: 1px solid #ddd; cursor: pointer; }
    #fb-preview-container { margin-bottom: 10px; }
    #fb-preview { max-width: 100%; border: 1px solid #ddd; border-radius: 6px; }
</style>

<script>
    const DSN = 'https://b54b113cf958c1cf9a890d1431ff0ae3@sentry.datacredit.kr/2';
    const SCREENSHOT_INTERVAL_MS = 5000; // 2초마다 스크린샷 캡처
    const MAX_SCREENSHOTS_HISTORY = 3; // 최대 3개의 이전 스크린샷 보관

    let screenshotHistory = []; // Blob 데이터를 저장할 배열
    let screenshotIntervalId = null;
    let currentErrorScreenshotBlob = null; // 오류 발생 시점의 스크린샷 Blob
    let originalErrorScreenshotURL = null; // 오류 발생 시점 스크린샷 미리보기 URL

    // 주기적으로 스크린샷 캡처하는 함수
    async function captureHistoryScreenshot() {
        try {
            const canvas = await html2canvas(document.body, {
                useCORS: true, // CORS 이미지 로드 허용
                logging: false // 콘솔 로그 줄이기
            });
            canvas.toBlob(blob => {
                if (blob) {
                    screenshotHistory.push(blob);
                    if (screenshotHistory.length > MAX_SCREENSHOTS_HISTORY) {
                        screenshotHistory.shift(); // 가장 오래된 스크린샷 제거
                    }
                }
            }, 'image/png');
        } catch (e) {
            console.warn('Error capturing history screenshot:', e);
        }
    }

    function startScreenshotCapture() {
        if (screenshotIntervalId) clearInterval(screenshotIntervalId);
        screenshotHistory = []; // 초기화
        // 즉시 한 번 캡처 후 인터벌 시작
        captureHistoryScreenshot();
        screenshotIntervalId = setInterval(captureHistoryScreenshot, SCREENSHOT_INTERVAL_MS);
    }

    function stopScreenshotCapture() {
        if (screenshotIntervalId) clearInterval(screenshotIntervalId);
        screenshotIntervalId = null;
    }


    window.sentryOnLoad = function () {
        Sentry.init({
            dsn: DSN,
            tracesSampleRate: 1.0,
            integrations: [
                // Breadcrumbs 자동 수집 (기본값으로 포함되지만 명시적으로 추가 가능)
                Sentry.breadcrumbsIntegration(),
                Sentry.browserTracingIntegration(),
            ],
            beforeSend(event, hint) {
                // 오류 이벤트에 스크린샷 히스토리 첨부
                if (hint.originalException && screenshotHistory.length > 0) {
                    event.attachments = event.attachments || [];
                    screenshotHistory.forEach((blob, index) => {
                        event.attachments.push({
                            filename: `history_screenshot_${index + 1}.png`,
                            data: blob,
                            contentType: 'image/png'
                        });
                    });
                    // 오류 발생 시점 스크린샷도 첨부 (피드백과는 별개로 이벤트 자체에)
                    if (currentErrorScreenshotBlob) {
                        event.attachments.push({
                            filename: 'error_screenshot.png',
                            data: currentErrorScreenshotBlob,
                            contentType: 'image/png'
                        });
                    }
                }
                return event;
            }
        });

        // 화면 녹화(스크린샷 시퀀스) 시작
        startScreenshotCapture();

        window.addEventListener('error', async (ev) => handleError(ev.error || new Error(ev.message)));
        window.addEventListener('unhandledrejection', async (ev) => handleError(ev.reason));

        // 테스트용 카운트다운 (기존과 동일)
        const countdown = document.createElement('div');
        countdown.style.position = 'fixed';
        countdown.style.top = '100px';
        countdown.style.right = '100px';
        countdown.style.background = 'rgba(0,0,0,.5)';
        countdown.style.color = '#fff';
        countdown.style.padding = '10px';
        countdown.style.borderRadius = '10px';
        countdown.style.zIndex = '9999';
        countdown.style.fontSize = '2rem';
        countdown.style.fontFamily = '-apple-system,BlinkMacSystemFont,"Apple SD Gothic Neo",sans-serif';
        countdown.innerText = 'Sentry TEST in 10 seconds...'; // 시간 약간 늘림
        document.body.appendChild(countdown);
        let count = 10;
        const interval = setInterval(() => {
            count--;
            countdown.innerText = `Sentry TEST in ${count} seconds...`;
            if (count <= 0) {
                clearInterval(interval);
                countdown.remove();
            }
        }, 1000);

        setTimeout(()=>{ throw new Error('🚨 테스트 크래시 발생'); }, 11000); // 시간 약간 늘림
    };

    async function handleError(err){
        // 주기적 스크린샷 캡처 중지 (오류 발생 시점 이후는 불필요)
        stopScreenshotCapture();

        /* 1) ‘오류 당시 화면’ 스크린샷 캡처 */
        let canvas;
        try {
            canvas = await html2canvas(document.body, { useCORS: true, logging: false });
        } catch (e) {
            console.error("Error taking error screenshot with html2canvas:", e);
            // 빈 캔버스라도 생성해서 Blob 변환 시도 (선택적)
            canvas = document.createElement('canvas');
            canvas.width = 1; canvas.height = 1;
        }

        currentErrorScreenshotBlob = await new Promise(res => canvas.toBlob(res, 'image/png'));
        originalErrorScreenshotURL = URL.createObjectURL(currentErrorScreenshotBlob);

        /* 2) 이벤트 전송 & eventId 확보 (스크린샷은 beforeSend에서 첨부됨) */
        // 사용자 정보가 있다면 Sentry.setUser()를 통해 미리 설정해두는 것이 좋습니다.
        // 예: Sentry.setUser({ id: 'user123', email: 'user@example.com', username: 'John Doe' });
        const eventId = Sentry.captureException(err); // beforeSend에서 스크린샷 첨부됨

        /* 3) 피드백 모달 생성 */
        if(document.getElementById('fb-overlay')) return;

        // 이전 스크린샷 히스토리 미리보기 HTML 생성
        let historyThumbnailsHTML = '';
        if (screenshotHistory.length > 0) {
            historyThumbnailsHTML = `
                <p style="margin-bottom:5px; font-size:0.8rem; color:#555;">최근 오류 발생 시점 화면 (선택해주세요):</p>
                <div id="fb-screenshots-container">
            `;
            screenshotHistory.forEach((blob, index) => {
                const url = URL.createObjectURL(blob);
                historyThumbnailsHTML += `<img src="${url}" alt="History ${index + 1}" data-blob-index="${index}" title="클릭하여 크게 보기">`;
            });
            historyThumbnailsHTML += `</div>`;
        }

        document.body.insertAdjacentHTML('beforeend',`
    <div id="fb-overlay">
      <div id="fb-card">
        <button id="fb-close">✕</button>
        <h3>죄송합니다, 문제가 발생했습니다.</h3>
        <p>불편을 드려 대단히 죄송합니다.<br>재발 방지를 위해, 최근에 수행하신 동작/행동/상황을 설명해주시면 문제 해결에 큰 도움이 됩니다. <br>화면상의 개인정보는 마스킹(*) 되어 제출됩니다.<br>[Event ID: ${eventId}]</p>
        ${historyThumbnailsHTML}
        <p style="margin-bottom:5px; font-size:0.8rem; color:#555;">제출할 오류 발생 시점 화면 (아래):</p>
        <div id="fb-preview-container">
            <img id="fb-preview" src="${originalErrorScreenshotURL}" alt="Error Screenshot">
        </div>
        <input id="fb-name" placeholder="이름 (선택)">
        <input id="fb-email" type="email" placeholder="이메일 또는 아이디 (선택)">
        <textarea id="fb-msg" placeholder="수행하신 동작/행동/상황 (필수)" rows="4"></textarea>
        <button id="fb-shot">현재 화면 다시찍기</button>
        <button id="fb-send">제출 (전송하기)</button>
      </div>
    </div>`);

        const fbOverlay = document.getElementById('fb-overlay');
        const fbPreviewImg = document.getElementById('fb-preview');

        // 히스토리 스크린샷 클릭 시 크게 보기 (fb-preview에 표시)
        document.querySelectorAll('#fb-screenshots-container img').forEach(img => {
            img.onclick = () => {
                fbPreviewImg.src = img.src; // 간단히 URL 재사용
                // 필요하다면 선택된 히스토리 스크린샷을 currentErrorScreenshotBlob으로 교체하는 로직 추가 가능
            };
        });

        /* 닫기 */
        document.getElementById('fb-close').onclick = () => {
            fbOverlay.remove();
            URL.revokeObjectURL(originalErrorScreenshotURL);
            screenshotHistory.forEach(blob => URL.revokeObjectURL(URL.createObjectURL(blob))); // 메모리 해제
            startScreenshotCapture(); // 다시 주기적 캡처 시작 (페이지를 떠나지 않는 경우)
        };

        /* 다시찍기 (오류 발생 시점 화면) */
        let retakeShotURL = originalErrorScreenshotURL; // 재촬영시 이전 URL 해제용
        document.getElementById('fb-shot').onclick = async () => {
            try {
                const c = await html2canvas(document.body, { useCORS: true, logging: false });
                c.toBlob(newBlob => {
                    if (retakeShotURL) URL.revokeObjectURL(retakeShotURL);
                    retakeShotURL = URL.createObjectURL(newBlob);
                    fbPreviewImg.src = retakeShotURL;
                    currentErrorScreenshotBlob = newBlob; // 제출할 Blob 업데이트
                }, 'image/png');
            } catch (e) {
                console.error("Error retaking screenshot:", e);
                alert('죄송합니다. 스크린샷 재촬영에 실패했습니다. 기존 스크린샷 중에서 선택해주세요.');
            }
        };

        /* 제출 */
        document.getElementById('fb-send').onclick = async () => {
            const msg = document.getElementById('fb-msg').value.trim();
            if (!msg) { alert('죄송합니다. 수행하신 동작/행동/상황을 입력해주세요.'); return; }

            const userName = document.getElementById('fb-name').value;
            const userEmail = document.getElementById('fb-email').value;

            // Sentry에 사용자 정보 업데이트 (피드백 제출 시점)
            if (userName || userEmail) {
                Sentry.setUser({ name: userName, email: userEmail });
            }

            // Sentry User Feedback API 사용 방식 (선택1)
            // Sentry.captureUserFeedback({
            //     event_id: eventId,
            //     name: userName,
            //     email: userEmail,
            //     comments: msg,
            // });
            // alert('제출해 주셔서 감사합니다. 담당자가 빠르게 확인하겠습니다!');
            // fbOverlay.remove();
            // URL.revokeObjectURL(originalErrorScreenshotURL);
            // if(retakeShotURL !== originalErrorScreenshotURL) URL.revokeObjectURL(retakeShotURL);
            // screenshotHistory.forEach(blob => URL.revokeObjectURL(URL.createObjectURL(blob)));
            // startScreenshotCapture();

            // 기존 FormData 방식 (선택2 - 스크린샷을 피드백과 직접 전송 시)
            const form = new FormData();
            form.append('event_id', eventId);
            form.append('name', userName);
            form.append('email', userEmail);
            form.append('comments', msg);

            // 현재 미리보기 중인 스크린샷 (오류 시점 또는 재촬영된 것)을 피드백과 함께 보냄
            if (currentErrorScreenshotBlob) {
                form.append('screenshot', currentErrorScreenshotBlob, 'user_feedback_screenshot.png');
            }
            // 만약 히스토리 스크린샷도 피드백 폼에 함께 보내고 싶다면,
            // 반복문으로 form.append('history_screenshot_N', blob, `history_N.png`) 추가 가능
            // (단, 서버 API가 다중 파일 업로드를 지원해야 함)

            const url = new URL('https://sentry.datacredit.kr/api/embed/error-page/feedback/');
            url.searchParams.set('dsn', DSN);
            try {
                const resp = await fetch(url.toString(), { method:'POST', body:form, mode:'cors' });
                if(resp.ok){
                    alert('제출해 주셔서 감사합니다. 담당자가 빠르게 확인하겠습니다.');
                    fbOverlay.remove();
                    URL.revokeObjectURL(originalErrorScreenshotURL);
                    if(retakeShotURL !== originalErrorScreenshotURL) URL.revokeObjectURL(retakeShotURL);
                    screenshotHistory.forEach(blob => URL.revokeObjectURL(URL.createObjectURL(blob))); // 메모리 해제
                    startScreenshotCapture(); // 다시 주기적 캡처 시작
                } else {
                    const errorText = await resp.text();
                    console.error('Feedback submission failed:', resp.status, errorText);
                    alert('제출 실패 - 상태코드: ' + resp.status + '\n메시지: ' + errorText);
                }
            } catch(e) {
                console.error('Network error during feedback submission:', e);
                alert('네트워크 오류로 스크린샷 제출에 실패했습니다. 발생된 오류는 성공적으로 보고되었습니다.');
            }
        };

        // 화면 녹화(스크린샷 시퀀스) 다시 시작 (만약 모달을 닫지 않고 페이지에 오래 머무른다면)
        startScreenshotCapture(); // handleError 진입 시 이미 stop 했으므로, 모달 닫힐 때 다시 시작
    }
</script>
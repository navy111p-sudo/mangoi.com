// ============================================================
// [1] 교사 이름 드롭다운 관련 코드
// ============================================================

// --- localStorage 키 이름 ---
// localStorage: 브라우저에 데이터를 영구 저장하는 공간이에요.
// 브라우저를 껐다 켜도 데이터가 유지됩니다!
const TEACHER_STORAGE_KEY = "savedTeacherNames";

// --- 저장된 교사 이름 목록 가져오기 ---
// JSON.parse: 글자(문자열)를 배열/객체로 바꿔주는 기능이에요.
function getTeacherNames() {
  const saved = localStorage.getItem(TEACHER_STORAGE_KEY);
  // 저장된 게 있으면 배열로 변환, 없으면 빈 배열 []
  return saved ? JSON.parse(saved) : [];
}

// --- 교사 이름 저장하기 ---
// JSON.stringify: 배열/객체를 글자(문자열)로 바꿔주는 기능이에요.
function saveTeacherName(name) {
  // trim(): 앞뒤 공백 제거
  const trimmedName = name.trim();
  if (!trimmedName) return; // 빈 이름이면 저장 안 함

  const names = getTeacherNames();

  // 이미 있는 이름이면 저장하지 않음 (중복 방지)
  if (names.includes(trimmedName)) return;

  // 새 이름을 배열 맨 앞에 추가
  names.unshift(trimmedName);
  localStorage.setItem(TEACHER_STORAGE_KEY, JSON.stringify(names));
}

// --- 드롭다운 리스트 렌더링(그리기) ---
function renderTeacherList() {
  const listEl = document.getElementById("teacherList");
  const names = getTeacherNames();

  // 기존 내용 비우기
  listEl.innerHTML = "";

  // 저장된 이름이 없으면 안내 문구 표시
  if (names.length === 0) {
    const emptyItem = document.createElement("li");
    emptyItem.className = "calculator__teacher-empty";
    emptyItem.textContent = "저장된 이름이 없습니다 (No saved names)";
    listEl.appendChild(emptyItem);
    return;
  }

  // 저장된 이름들을 리스트 아이템으로 만들기
  names.forEach(function (name) {
    const li = document.createElement("li");
    li.className = "calculator__teacher-item";
    li.textContent = name;

    // 이름을 클릭하면 input에 자동 입력!
    li.addEventListener("click", function () {
      document.getElementById("teacherName").value = name;
      closeTeacherDropdown();
    });

    listEl.appendChild(li);
  });
}

// --- 드롭다운 열기 ---
function openTeacherDropdown() {
  const listEl = document.getElementById("teacherList");
  const toggleBtn = document.getElementById("teacherToggle");

  renderTeacherList(); // 최신 목록으로 갱신
  listEl.classList.remove("hidden"); // 리스트 보이기
  toggleBtn.classList.add("calculator__teacher-toggle--active"); // 삼각표 회전
}

// --- 드롭다운 닫기 ---
function closeTeacherDropdown() {
  const listEl = document.getElementById("teacherList");
  const toggleBtn = document.getElementById("teacherToggle");

  listEl.classList.add("hidden"); // 리스트 숨기기
  toggleBtn.classList.remove("calculator__teacher-toggle--active"); // 삼각표 원래대로
}

// --- 드롭다운 열기/닫기 토글 ---
function toggleTeacherDropdown() {
  const listEl = document.getElementById("teacherList");
  // hidden 클래스가 있으면 닫혀있는 상태 -> 열기
  if (listEl.classList.contains("hidden")) {
    openTeacherDropdown();
  } else {
    closeTeacherDropdown();
  }
}

// --- 페이지 로드 시 이벤트 연결 ---
// DOMContentLoaded: HTML이 다 읽힌 뒤 실행되는 이벤트예요.
document.addEventListener("DOMContentLoaded", function () {
  // --- 샘플 교사 이름 초기 세팅 ---
  // localStorage에 저장된 이름이 없을 때만 샘플 데이터를 넣어줘요.
  // 이미 사용 중이라면(데이터가 있다면) 건드리지 않아요!
  if (!localStorage.getItem(TEACHER_STORAGE_KEY)) {
    const sampleNames = [
      "Jane",
      "John",
      "Smith",
      "Brown",
      "Tina",
      "Suzi",
      "Wong",
      "Melca",
      "MaiMai",
      "Bert",
      "Lee",
    ];
    localStorage.setItem(TEACHER_STORAGE_KEY, JSON.stringify(sampleNames));
  }

  // 삼각표 버튼 클릭 -> 드롭다운 토글
  const toggleBtn = document.getElementById("teacherToggle");
  toggleBtn.addEventListener("click", function (e) {
    e.stopPropagation(); // 클릭 이벤트가 바깥으로 퍼지지 않게 막기
    toggleTeacherDropdown();
  });

  // 바깥 아무 곳이나 클릭하면 드롭다운 닫기
  document.addEventListener("click", function (e) {
    const wrapper = document.getElementById("teacherWrapper");
    // wrapper 바깥을 클릭했을 때만 닫기
    if (!wrapper.contains(e.target)) {
      closeTeacherDropdown();
    }
  });
});

// ============================================================
// [2] 수업료 계산 함수
// ============================================================

// '계산하기' 버튼을 눌렀을 때 실행되는 마법의 주문(함수)이에요!
function calculateSalary() {
  // 0. 교사 이름을 localStorage에 저장해요!
  const teacherName = document.getElementById("teacherName").value;
  saveTeacherName(teacherName);

  // 1. 화면(HTML)에서 입력한 점수들을 가져와요.
  // parseFloat(파스 플로트): 글자로 된 숫자를 진짜 '숫자'로 바꿔주는 마법이에요.
  const attendance = parseFloat(document.getElementById("attendance").value); // 근태 점수
  const studentEval = parseFloat(document.getElementById("student").value); // 학생 평가
  const bossEval = parseFloat(document.getElementById("boss").value); // 상사 평가
  const yearlyRate = parseFloat(document.getElementById("yearly").value); // 출석률 점수

  // 2. 평균 점수를 구해요 (네 가지를 더해서 4로 나눠요!)
  const averageScore = (attendance + studentEval + bossEval + yearlyRate) / 4;

  // 3. 조건에 따라 10분당 수업료를 정해요.
  let rate = 0; // 수업료를 담을 변수 (처음엔 0)
  let grade = ""; // 등급을 담을 변수

  if (averageScore >= 4) {
    rate = 150;
    grade = "우수";
  } else if (averageScore >= 3) {
    rate = 140;
    grade = "양호";
  } else if (averageScore >= 2) {
    rate = 120;
    grade = "보통";
  } else {
    rate = 100;
    grade = "미달";
  }

  // 4. 결과를 화면에 보여줘요!
  // ---------- 안내 문구 숨기고, 결과 영역 보여주기 ----------
  document.getElementById("resultPlaceholder").classList.add("hidden");
  document.getElementById("resultContent").classList.remove("hidden");

  // ---------- 각 결과값 넣기 ----------
  // toFixed(1) : 소수점 첫째 자리까지만 표시해주는 기능이에요.
  document.getElementById("avgScore").innerText =
    averageScore.toFixed(1) + "점";
  document.getElementById("gradeText").innerText = grade;
  document.getElementById("salaryRate").innerText = rate + " 페소";

  // ---------- 등급에 따라 색상 바꾸기 ----------
  const gradeEl = document.getElementById("gradeText");
  // 기존 색상 클래스 모두 제거
  gradeEl.className =
    "calculator__result-value calculator__result-value--grade";

  // 등급별로 다른 색상 클래스 추가
  if (grade === "우수") {
    gradeEl.classList.add("grade--excellent");
  } else if (grade === "양호") {
    gradeEl.classList.add("grade--good");
  } else if (grade === "보통") {
    gradeEl.classList.add("grade--normal");
  } else {
    gradeEl.classList.add("grade--low");
  }

  // ---------- 결과 영역에 애니메이션 효과 ----------
  const resultArea = document.getElementById("resultArea");
  resultArea.classList.add("calculator__result--active");
}

// ============================================================
// [1] 교사 이름 드롭다운 관련 코드
// ============================================================

// --- localStorage 키 이름 ---
// localStorage: 브라우저에 데이터를 영구 저장하는 공간이에요.
// 브라우저를 껐다 켜도 데이터가 유지됩니다!
const TEACHER_STORAGE_KEY = "savedTeacherNames";

// ---// [1] 교사 데이터 관리 (이름 + 점수 저장)
const TEACHER_DATA_KEY = "teacherRecords_final";

// 1. 저장된 교사 데이터 가져오기 (없으면 빈 배열)
function getTeacherRecords() {
  const saved = localStorage.getItem(TEACHER_DATA_KEY);
  return saved ? JSON.parse(saved) : [];
}

// 2. 교사 데이터 저장하기 (이름과 점수 갱신)
function saveTeacherData(name, score) {
  const trimmedName = name.trim();
  if (!trimmedName) return;

  let records = getTeacherRecords();
  const existingIndex = records.findIndex((r) => r.name === trimmedName);

  if (existingIndex !== -1) {
    // 이미 있으면 점수 업데이트
    records[existingIndex].score = score;
  } else {
    // 없으면 새로 추가
    records.push({ name: trimmedName, score: score });
  }

  // 점수 높은 순으로 정렬하여 저장 (동점일 경우 이름순)
  records.sort((a, b) => {
    if (b.score !== a.score) return b.score - a.score;
    return a.name.localeCompare(b.name);
  });

  localStorage.setItem(TEACHER_DATA_KEY, JSON.stringify(records));
}

// 3. 교사 이름 목록만 가져오기 (드롭다운용)
function getTeacherNames() {
  const records = getTeacherRecords();
  return records.map((r) => r.name);
}

// [2] UI 관련 함수 (드롭다운, 초기화)

function renderTeacherList() {
  const teacherList = document.getElementById("teacherList");
  const names = getTeacherNames();

  teacherList.innerHTML = "";

  if (names.length === 0) {
    const emptyItem = document.createElement("li");
    emptyItem.className = "calculator__teacher-empty";
    emptyItem.innerText = "저장된 교사가 없습니다.";
    teacherList.appendChild(emptyItem);
    return;
  }

  names.forEach((name) => {
    const li = document.createElement("li");
    li.className = "calculator__teacher-item";
    li.innerText = name;
    li.onclick = function () {
      selectTeacher(name);
    };
    teacherList.appendChild(li);
  });
}

function selectTeacher(name) {
  const input = document.getElementById("teacherName");
  input.value = name;
  closeTeacherDropdown();
  resetForm(); // 교사 선택 시 폼 초기화
}

function openTeacherDropdown() {
  renderTeacherList();
  document.getElementById("teacherList").classList.remove("hidden");
  document
    .getElementById("teacherToggle")
    .classList.add("calculator__teacher-toggle--active");
}

function closeTeacherDropdown() {
  document.getElementById("teacherList").classList.add("hidden");
  document
    .getElementById("teacherToggle")
    .classList.remove("calculator__teacher-toggle--active");
}

function toggleTeacherDropdown() {
  const list = document.getElementById("teacherList");
  if (list.classList.contains("hidden")) {
    openTeacherDropdown();
  } else {
    closeTeacherDropdown();
  }
}

// 폼 입력값 및 결과 초기화 함수
function resetForm() {
  // 점수 입력 필드 초기화
  document.getElementById("attendance").value = "";
  document.getElementById("student").value = "";
  document.getElementById("boss").value = "";
  document.getElementById("yearly").value = "";

  // 결과창 숨기기
  document.getElementById("resultPlaceholder").classList.remove("hidden");
  document.getElementById("resultContent").classList.add("hidden");
  document
    .getElementById("resultArea")
    .classList.remove("calculator__result--active");
  document.getElementById("feedbackSection").classList.add("hidden"); // 피드백 섹션 숨기기
}

// [3] 핵심 계산 로직

// 순위 계산 함수
function getTeacherRank(name, currentScore) {
  const records = getTeacherRecords();
  // 현재 점수로 리스트에서 가상 순위 확인을 위해 임시 정렬
  // (실제 저장은 calculateSalary 마지막에 하지만, 보여줄 때는 현재 점수 기준이어야 함)

  // 현재 교사가 리스트에 있다면 점수만 갱신해서 비교, 없다면 추가해서 비교
  const existingIndex = records.findIndex((r) => r.name === name);
  let compareList = [...records];

  if (existingIndex !== -1) {
    compareList[existingIndex].score = currentScore;
  } else {
    compareList.push({ name: name, score: currentScore });
  }

  // 다시 정렬
  compareList.sort((a, b) => b.score - a.score);

  // 등수 찾기 (1등부터 시작)
  const rank = compareList.findIndex((r) => r.name === name) + 1;
  const total = compareList.length;

  return { rank, total };
}

// 피드백 생성 함수
function generateFeedback(scores) {
  const feedbackList = [];
  const { attendance, student, boss, yearly } = scores;
  const THRESHOLD = 3.0; // 3.0 미만이면 피드백 제공

  if (attendance < THRESHOLD) {
    feedbackList.push(
      "Your attendance score is low. Please pay more attention to punctuality.<br><span class='ko-feedback'>(근태 점수가 낮습니다. 시간 엄수에 조금 더 신경 써주세요.)</span>",
    );
  }
  if (student < THRESHOLD) {
    feedbackList.push(
      "Your student evaluation score is low. Please put more effort into class preparation and student management.<br><span class='ko-feedback'>(학생 평가 점수가 낮습니다. 수업 준비와 학생 관리에 더 노력해주세요.)</span>",
    );
  }
  if (boss < THRESHOLD) {
    feedbackList.push(
      "Your boss evaluation score is insufficient. Improvement in work attitude is needed.<br><span class='ko-feedback'>(상사 평가 점수가 부족합니다. 업무 태도 개선이 필요해 보입니다.)</span>",
    );
  }
  if (yearly < THRESHOLD) {
    feedbackList.push(
      "Your yearly attendance rate is low. Consistent attendance is important.<br><span class='ko-feedback'>(연간 출석률이 저조합니다. 꾸준한 출석이 중요합니다.)</span>",
    );
  }

  // 모두 훌륭할 때
  if (feedbackList.length === 0) {
    feedbackList.push(
      "All items are excellent! Please keep up the good work! 👍<br><span class='ko-feedback'>(모든 항목이 훌륭합니다! 지금처럼 계속 화이팅해주세요!)</span>",
    );
  }

  return feedbackList;
}

document.addEventListener("DOMContentLoaded", function () {
  // [초기 데이터 세팅] 저장된 데이터 확인 및 복구
  let savedData = localStorage.getItem(TEACHER_DATA_KEY);
  let parsedData = [];

  try {
    parsedData = savedData ? JSON.parse(savedData) : [];
  } catch (e) {
    parsedData = [];
  }

  // 데이터가 없거나 비어스면 샘플 데이터 강제 주입
  if (!Array.isArray(parsedData) || parsedData.length === 0) {
    const sampleNames = ["John", "Jane", "Smith", "Lee", "Brown"];
    const initialRecords = sampleNames.map((name) => ({
      name: name,
      score: 0,
    }));
    localStorage.setItem(TEACHER_DATA_KEY, JSON.stringify(initialRecords));
    console.log("초기 샘플 데이터가 생성되었습니다.");
  }

  const toggleBtn = document.getElementById("teacherToggle");
  const teacherInput = document.getElementById("teacherName");

  if (toggleBtn) {
    toggleBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      toggleTeacherDropdown();
    });
  }

  // 교사 이름 입력 시에도 폼 초기화
  teacherInput.addEventListener("input", function () {
    resetForm();
  });

  document.addEventListener("click", function (e) {
    const wrapper = document.querySelector(".calculator__teacher-wrapper");
    if (!wrapper.contains(e.target)) {
      closeTeacherDropdown();
    }
  });

  // 페이지 로드 시 드롭다운 렌더링 준비
  renderTeacherList();
});

// [4] 2단계 클릭 로직 (Confirm 말풍선)
let isConfirmMode = false; // 현재 확인 대기 상태인지
let confirmTimer = null; // 자동 취소 타이머

function handleCalculateClick() {
  if (!isConfirmMode) {
    // 첫 번째 클릭: 말풍선 보여주기
    isConfirmMode = true;
    document.getElementById("calculateBtn").innerHTML =
      "<span class='calculator__button-en'>Click to Confirm</span><span class='calculator__button-ko'>(확인)</span>";

    // 3초 후 자동으로 원래 상태로 돌아감
    confirmTimer = setTimeout(function () {
      resetConfirm();
    }, 3000);
  } else {
    // 두 번째 클릭: 실제 계산 실행
    clearTimeout(confirmTimer);
    resetConfirm();
    calculateSalary();
  }
}

function resetConfirm() {
  isConfirmMode = false;
  document.getElementById("calculateBtn").innerHTML =
    "<span class='calculator__button-en'>Calculate</span><span class='calculator__button-ko'>(계산하기)</span>";
}

function calculateSalary() {
  const teacherName = document.getElementById("teacherName").value.trim();

  if (!teacherName) {
    alert("교사 이름을 입력해주세요. (Please enter teacher's name)");
    return;
  }

  // 1. 점수 가져오기
  const attendance = parseFloat(document.getElementById("attendance").value);
  const studentEval = parseFloat(document.getElementById("student").value);
  const bossEval = parseFloat(document.getElementById("boss").value);
  const yearlyRate = parseFloat(document.getElementById("yearly").value);

  // 유효성 검사 (1.0 ~ 5.0)
  if (
    [attendance, studentEval, bossEval, yearlyRate].some(
      (val) => isNaN(val) || val < 1 || val > 5,
    )
  ) {
    alert(
      "모든 점수는 1.0에서 5.0 사이로 입력해주세요.\n(Please enter scores between 1.0 and 5.0)",
    );
    return;
  }

  // 2. 평균 점수 계산
  const averageScore = (attendance + studentEval + bossEval + yearlyRate) / 4;

  // 3. 등급 및 수업료 결정
  let rate = 0;
  let grade = "";

  if (averageScore >= 4.7) {
    rate = 160;
    grade = "Best (최우수)";
  } else if (averageScore >= 4) {
    rate = 150;
    grade = "Excellent (우수)";
  } else if (averageScore >= 3) {
    rate = 140;
    grade = "Good (양호)";
  } else if (averageScore >= 2) {
    rate = 120;
    grade = "Average (보통)";
  } else {
    rate = 100;
    grade = "Below Average (미달)";
  }

  // 데이터 저장 및 순위 계산
  saveTeacherData(teacherName, averageScore);
  const { rank, total } = getTeacherRank(teacherName, averageScore);

  // 4. 결과 화면 표시
  document.getElementById("resultPlaceholder").classList.add("hidden");
  document.getElementById("resultContent").classList.remove("hidden");

  // 값 채우기
  document.getElementById("avgScore").innerText =
    averageScore.toFixed(1) + " Points (점)";
  document.getElementById("gradeText").innerText = grade;
  document.getElementById("salaryRate").innerText = rate + " Peso (페소)";
  document.getElementById("rankText").innerText =
    `Ranked ${rank} out of ${total} (${total}명 중 ${rank}등)`;

  // 등급 색상 적용
  const gradeEl = document.getElementById("gradeText");
  gradeEl.className =
    "calculator__result-value calculator__result-value--grade";

  if (grade === "최우수") {
    gradeEl.classList.add("grade--best");
  } else if (grade === "우수") {
    gradeEl.classList.add("grade--excellent");
  } else if (grade === "양호") {
    gradeEl.classList.add("grade--good");
  } else if (grade === "보통") {
    gradeEl.classList.add("grade--normal");
  } else {
    gradeEl.classList.add("grade--low");
  }

  // 피드백 생성 및 표시
  const feedbacks = generateFeedback({
    attendance: attendance,
    student: studentEval,
    boss: bossEval,
    yearly: yearlyRate,
  });
  const feedbackListEl = document.getElementById("feedbackList");
  feedbackListEl.innerHTML = "";

  feedbacks.forEach((msg) => {
    const li = document.createElement("li");
    li.innerHTML = msg; // 태그 적용을 위해 innerHTML 사용
    feedbackListEl.appendChild(li);
  });

  document.getElementById("feedbackSection").classList.remove("hidden");

  // 결과 애니메이션 효과
  const resultArea = document.getElementById("resultArea");
  resultArea.classList.remove("calculator__result--active"); // 애니메이션 리셋을 위해 제거
  void resultArea.offsetWidth; // 트리거 리플로우
  resultArea.classList.add("calculator__result--active");
}

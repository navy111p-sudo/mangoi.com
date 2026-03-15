// js/report.js
document.addEventListener("DOMContentLoaded", () => {
  renderReportTables();
});

// ==================== Grade 자동 계산 함수 ====================
function calcGradeFromWeighted(weighted) {
  if (weighted >= 4.75) return "Outstanding";
  if (weighted >= 4.50) return "Very Satisfactory";
  if (weighted >= 3.50) return "Satisfactory";
  return "Needs Improvement";
}

function getGradeColor(grade) {
  switch (grade) {
    case "Outstanding": return "#10b981";
    case "Very Satisfactory": return "#3b82f6";
    case "Satisfactory": return "#6b7280";
    case "Needs Improvement": return "#ef4444";
    default: return "#000000";
  }
}

function getGradeBg(grade) {
  switch (grade) {
    case "Outstanding": return "#E2F9EE";
    case "Very Satisfactory": return "#E0EDFF";
    case "Satisfactory": return "#F0F0F0";
    case "Needs Improvement": return "#FDE8E8";
    default: return "#FFFFFF";
  }
}

function getGradeLabel(grade, lang) {
  const labels = {
    "Outstanding":        { ko: "최우수", en: "Outstanding", tl: "Pinakamahusay" },
    "Very Satisfactory":  { ko: "매우 우수", en: "Very Satisfactory", tl: "Napakahusay" },
    "Satisfactory":       { ko: "우수", en: "Satisfactory", tl: "Mahusay" },
    "Needs Improvement":  { ko: "개선 요망", en: "Needs Improvement", tl: "Kailangan Pagbutihin" }
  };
  const l = (typeof currentLang !== 'undefined') ? currentLang : 'ko';
  return labels[grade] ? labels[grade][lang || l] || grade : grade;
}

// ==================== 총 수업수 변경사항 저장/로드 ====================
const CLASSES_OVERRIDE_KEY = "mangoi_classes_override";

function getClassesOverrides() {
  const saved = localStorage.getItem(CLASSES_OVERRIDE_KEY);
  return saved ? JSON.parse(saved) : {};
}

function saveClassesOverride(teacherName, newClasses) {
  const overrides = getClassesOverrides();
  overrides[teacherName] = newClasses;
  localStorage.setItem(CLASSES_OVERRIDE_KEY, JSON.stringify(overrides));
}

// 교사의 현재 총 수업수 가져오기 (override가 있으면 override, 없으면 원래 계산값)
function getTeacherClasses(t) {
  const overrides = getClassesOverrides();
  if (overrides.hasOwnProperty(t.name)) {
    return overrides[t.name];
  }
  const rate = t.rate || 0;
  const salary = t.salary || 0;
  return rate > 0 ? Math.floor(salary / rate) : 0;
}

// 교사의 Monthly Salary 계산 (총 수업수 × rate)
function getTeacherMonthlySalary(t) {
  const classes = getTeacherClasses(t);
  const rate = t.rate || 0;
  return classes * rate;
}

// teachers 데이터에 salary 반영 (다른 차트/페이지에서 사용)
function applyClassesOverridesToTeachers() {
  if (typeof teachers === "undefined" || !teachers.length) return;
  const overrides = getClassesOverrides();
  teachers.forEach((t) => {
    if (overrides.hasOwnProperty(t.name)) {
      const newClasses = overrides[t.name];
      const rate = t.rate || 0;
      t.salary = newClasses * rate;
    }
  });
  // localStorage에도 반영
  if (typeof saveData === "function") {
    saveData();
  }
}

// ==================== 렌더링 ====================
function renderReportTables() {
  // Global teachers 객체 (data.js에서 로드됨)
  if (typeof teachers === "undefined" || !teachers.length) return;

  // override 적용
  applyClassesOverridesToTeachers();

  // 1. SCORE SUMMARY — 등급 컬럼 추가
  const scoreSummaryBody = document.getElementById("scoreSummaryBody");
  if (scoreSummaryBody) {
    scoreSummaryBody.innerHTML = "";
    teachers.forEach((t, index) => {
      const bgColor = index % 2 === 0 ? "#F5F5F5" : "#FFFFFF";
      const grade = calcGradeFromWeighted(t.weighted);
      const gradeColor = getGradeColor(grade);
      const gradeBg = getGradeBg(grade);

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td style="color:#000000;font-size:10.0pt;text-align:general"></td>
        <td style="color:#000000;font-weight:bold;font-size:9.0pt;background:${bgColor};text-align:left;vertical-align:middle">${t.name}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.scores.inst.toFixed(1)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.scores.ret.toFixed(1)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.scores.punct.toFixed(1)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.scores.admin.toFixed(1)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.scores.contrib.toFixed(1)}</td>
        <td style="color:#000000;font-weight:bold;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.weighted.toFixed(2)}</td>
        <td style="color:${gradeColor};font-weight:bold;font-size:9.0pt;background:${gradeBg};text-align:center;vertical-align:middle">${grade}</td>
      `;
      scoreSummaryBody.appendChild(tr);
    });
  }

  // 2. RATE / 10 MIN VERIFICATION
  const rateVerificationBody = document.getElementById("rateVerificationBody");
  let totalSalary = 0;
  let totalClasses = 0;
  let totalRateShown = 0;
  let totalCalculated = 0;

  if (rateVerificationBody) {
    rateVerificationBody.innerHTML = "";
    teachers.forEach((t, index) => {
      const bgColor = index % 2 === 0 ? "#F5F5F5" : "#FFFFFF";
      const salary = getTeacherMonthlySalary(t);
      const rate = t.rate || 0;
      const classes = getTeacherClasses(t);
      totalSalary += salary;
      totalClasses += classes;
      totalRateShown += rate;
      totalCalculated += rate;

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td style="color:#000000;font-size:10.0pt;text-align:general"></td>
        <td style="color:#000000;font-weight:bold;font-size:9.0pt;background:${bgColor};text-align:left;vertical-align:middle">${t.name}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${salary.toFixed(2)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${classes}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${(rate ? rate : 0).toFixed(2)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${(rate ? rate : 0).toFixed(2)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">0</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">OK</td>
        <td style="font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">0</td>
      `;
      rateVerificationBody.appendChild(tr);
    });

    // TOTAL ROW
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td style="color:#000000;font-size:10.0pt;text-align:general"></td>
      <td style="font-weight:bold;font-size:9.0pt;background:#E8EEF4;text-align:right;vertical-align:middle">TOTAL</td>
      <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">${totalSalary.toFixed(2)}</td>
      <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">${totalClasses}</td>
      <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">${totalRateShown.toFixed(2)}</td>
      <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">${totalCalculated.toFixed(2)}</td>
      <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">0</td>
      <td style="font-weight:bold;font-size:9.0pt;background:#E8EEF4;text-align:center;vertical-align:middle"></td>
      <td style="font-weight:bold;font-size:9.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">0</td>
    `;
    rateVerificationBody.appendChild(tr);
  }

  // 3. TOP 5 OFFICE TEACHERS
  const top5OfficeBody = document.getElementById("top5OfficeBody");
  if (top5OfficeBody) {
    top5OfficeBody.innerHTML = "";
    const officeTeachers = teachers
      .filter((t) => t.status === "office" && !t.name.includes("HT"))
      .sort((a, b) => b.weighted - a.weighted || b.years - a.years)
      .slice(0, 5);

    officeTeachers.forEach((t, index) => {
      const isOdd = index % 2 === 0;
      const bgColor = isOdd ? "#FFF8E1" : index === 1 ? "#FFFFFF" : "#F5F5F5";
      const rankColor = index === 0 ? "#F9A825" : "#000000";
      const estChange = 0;

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td style="color:#000000;font-size:10.0pt;text-align:general"></td>
        <td style="color:${rankColor};font-weight:bold;font-size:11.0pt;background:${bgColor};text-align:center;vertical-align:middle">${index + 1}</td>
        <td style="color:#000000;font-weight:bold;font-size:9.0pt;background:${bgColor};text-align:left;vertical-align:middle">${t.name}</td>
        <td style="color:#000000;font-weight:bold;font-size:10.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.weighted.toFixed(2)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.years} Years</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.grade}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">Office</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${(t.rate ? t.rate : 0).toFixed(2)}</td>
        <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${(t.rate ? t.rate : 0).toFixed(2)}</td>
        <td style="font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">0</td>
        <td style="font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${estChange}</td>
      `;
      top5OfficeBody.appendChild(tr);
    });
  }

  // 4. TEACHER GRADE SUMMARY
  const gradeSummaryBody = document.getElementById("gradeSummaryBody");
  if (gradeSummaryBody) {
    gradeSummaryBody.innerHTML = "";
    teachers.forEach((t, index) => {
      const bgColor = index % 2 === 0 ? "#F7FAFF" : "#FFFFFF";
      const instC = "#1B3A6B";
      const retC = "#FF0000";
      const rowNum = index + 1;
      const rate = t.rate || 0;
      const classes = getTeacherClasses(t);
      const salary = classes * rate;

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td style="color:#000000;font-size:11.0pt"></td>
        <td style="color:#000000;font-size:11.0pt;background:${bgColor};text-align:center;vertical-align:middle">${rowNum}</td>
        <td style="color:#000000;font-weight:bold;font-size:11.0pt;background:${bgColor};text-align:left;vertical-align:middle">${t.name}</td>
        <td style="color:#000000;font-size:11.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.status}</td>
        <td style="color:#000000;font-size:11.0pt;background:${bgColor};text-align:center;vertical-align:middle">${t.years}</td>
        <td style="color:${instC};font-weight:bold;font-size:11.0pt;background:#EEF4FF;text-align:center;vertical-align:middle">${t.scores.inst.toFixed(1)}</td>
        <td style="color:${retC};font-weight:bold;font-size:11.0pt;background:#EEF4FF;text-align:center;vertical-align:middle">${t.scores.ret.toFixed(1)}</td>
        <td style="color:${instC};font-weight:bold;font-size:11.0pt;background:#EEF4FF;text-align:center;vertical-align:middle">${t.scores.punct.toFixed(1)}</td>
        <td style="color:${instC};font-weight:bold;font-size:11.0pt;background:#EEF4FF;text-align:center;vertical-align:middle">${t.scores.admin.toFixed(1)}</td>
        <td style="color:${instC};font-weight:bold;font-size:11.0pt;background:#EEF4FF;text-align:center;vertical-align:middle">${t.scores.contrib.toFixed(1)}</td>
        <td style="color:#1B3A6B;font-weight:bold;font-size:11.0pt;background:#D6E4F7;text-align:center;vertical-align:middle">${t.weighted.toFixed(2)}</td>
        <td style="color:${getGradeColor(t.grade)};font-weight:bold;font-size:10.0pt;background:${getGradeBg(t.grade)};text-align:center;vertical-align:middle">${t.grade}</td>
        <td class="salary-cell" data-teacher="${t.name}" style="color:#000000;font-size:11.0pt;background:${bgColor};text-align:right;vertical-align:middle">${salary.toFixed(2)}</td>
        <td class="classes-cell" data-teacher="${t.name}" data-original="${classes}" style="color:#1B3A6B;font-weight:bold;font-size:11.0pt;background:${bgColor};text-align:center;vertical-align:middle;padding:2px 4px">
          <div style="display:flex;align-items:center;justify-content:center;gap:2px">
            <div style="display:flex;flex-direction:column;gap:1px">
              <button class="btn-arrow btn-arrow-up" data-teacher="${t.name}" data-dir="up" style="background:#3D6B99;color:#fff;border:none;cursor:pointer;font-size:8px;line-height:1;padding:1px 4px;border-radius:2px" title="증가">▲</button>
              <button class="btn-arrow btn-arrow-down" data-teacher="${t.name}" data-dir="down" style="background:#3D6B99;color:#fff;border:none;cursor:pointer;font-size:8px;line-height:1;padding:1px 4px;border-radius:2px" title="감소">▼</button>
            </div>
            <span class="classes-value" data-teacher="${t.name}" style="min-width:30px;display:inline-block;text-align:center">${classes}</span>
            <button class="btn-confirm-classes" data-teacher="${t.name}" style="display:none;background:#10b981;color:#fff;border:none;cursor:pointer;font-size:9px;padding:2px 6px;border-radius:3px;font-weight:bold;white-space:nowrap" title="변경 확인">✓</button>
          </div>
        </td>
        <td style="color:#000000;font-size:11.0pt;background:${bgColor};text-align:right;vertical-align:middle">${rate.toFixed(2)}</td>
      `;
      gradeSummaryBody.appendChild(tr);
    });

    // ▲▼ 화살표 클릭 이벤트
    gradeSummaryBody.querySelectorAll(".btn-arrow").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const teacherName = btn.dataset.teacher;
        const dir = btn.dataset.dir;
        const valueSpan = gradeSummaryBody.querySelector(`.classes-value[data-teacher="${teacherName}"]`);
        const confirmBtn = gradeSummaryBody.querySelector(`.btn-confirm-classes[data-teacher="${teacherName}"]`);
        const classesCell = gradeSummaryBody.querySelector(`.classes-cell[data-teacher="${teacherName}"]`);

        let currentVal = parseInt(valueSpan.textContent) || 0;
        if (dir === "up") {
          currentVal += 1;
        } else {
          currentVal = Math.max(0, currentVal - 1);
        }
        valueSpan.textContent = currentVal;

        // 원래 값과 다르메 확인 버튼 표시
        const originalVal = parseInt(classesCell.dataset.original);
        if (currentVal !== originalVal) {
          confirmBtn.style.display = "inline-block";
          valueSpan.style.color = "#ef4444";
          valueSpan.style.fontWeight = "900";
        } else {
          confirmBtn.style.display = "none";
          valueSpan.style.color = "";
          valueSpan.style.fontWeight = "";
        }
      });
    });

    // 확인(✓) 버튼 클릭 이벤트
    gradeSummaryBody.querySelectorAll(".btn-confirm-classes").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        e.preventDefault();
        const teacherName = btn.dataset.teacher;
        const valueSpan = gradeSummaryBody.querySelector(`.classes-value[data-teacher="${teacherName}"]`);
        const classesCell = gradeSummaryBody.querySelector(`.classes-cell[data-teacher="${teacherName}"]`);
        const salaryCell = gradeSummaryBody.querySelector(`.salary-cell[data-teacher="${teacherName}"]`);

        const newClasses = parseInt(valueSpan.textContent) || 0;

        const ok = confirm("Are you sure to change?");
        if (ok) {
          // 1. localStorage에 override 저장
          saveClassesOverride(teacherName, newClasses);

          // 2. teachers 데이터의 salary 업데이트
          const teacher = teachers.find((t) => t.name === teacherName);
          if (teacher) {
            teacher.salary = newClasses * (teacher.rate || 0);
          }

          // 3. UI 업데이트: salary 셀
          if (salaryCell && teacher) {
            salaryCell.textContent = teacher.salary.toFixed(2);
            // 변경 하이라이트 효과
            salaryCell.style.transition = "background-color 0.5s";
            salaryCell.style.backgroundColor = "#FEF3C7";
            setTimeout(() => {
              salaryCell.style.backgroundColor = "";
            }, 2000);
          }

          // 4. data-original 갱신, 확인 버트 숨기기
          classesCell.dataset.original = newClasses;
          btn.style.display = "none";
          valueSpan.style.color = "#10b981";
          valueSpan.style.fontWeight = "bold";
          setTimeout(() => {
            valueSpan.style.color = "";
            valueSpan.style.fontWeight = "";
          }, 1500);

          // 5. localStorage에 전체 teachers 저장 (히트맵/트리맶에서 읽을 수 있도록)
          if (typeof saveData === "function") {
            saveData();
          }

          // 6. 다른 테이블도 갱신 (Rate Verification 등)
          refreshRateVerification();

          // 7. 히트맵/트리맵이 같은 페이지 내에 있으메 갱신
          if (typeof renderGridHeatmap === "function") {
            renderGridHeatmap();
          }
          if (typeof renderTreemap === "function") {
            renderTreemap();
          }
        } else {
          // 취소 → 원래 값으로 복원
          const originalVal = parseInt(classesCell.dataset.original);
          valueSpan.textContent = originalVal;
          btn.style.display = "none";
          valueSpan.style.color = "";
          valueSpan.style.fontWeight = "";
        }
      });
    });
  }
}

// Rate Verification 테이블만 갱신 (전체 리렌더 없이)
function refreshRateVerification() {
  if (typeof teachers === "undefined" || !teachers.length) return;

  const rateVerificationBody = document.getElementById("rateVerificationBody");
  if (!rateVerificationBody) return;

  let totalSalary = 0;
  let totalClasses = 0;
  let totalRateShown = 0;
  let totalCalculated = 0;

  rateVerificationBody.innerHTML = "";
  teachers.forEach((t, index) => {
    const bgColor = index % 2 === 0 ? "#F5F5F5" : "#FFFFFF";
    const salary = getTeacherMonthlySalary(t);
    const rate = t.rate || 0;
    const classes = getTeacherClasses(t);
    totalSalary += salary;
    totalClasses += classes;
    totalRateShown += rate;
    totalCalculated += rate;

    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td style="color:#000000;font-size:10.0pt;text-align:general"></td>
      <td style="color:#000000;font-weight:bold;font-size:9.0pt;background:${bgColor};text-align:left;vertical-align:middle">${t.name}</td>
      <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${salary.toFixed(2)}</td>
      <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${classes}</td>
      <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${(rate ? rate : 0).toFixed(2)}</td>
      <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">${(rate ? rate : 0).toFixed(2)}</td>
      <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">0</td>
      <td style="color:#000000;font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">OK</td>
      <td style="font-size:9.0pt;background:${bgColor};text-align:center;vertical-align:middle">0</td>
    `;
    rateVerificationBody.appendChild(tr);
  });

  // TOTAL ROW
  const tr = document.createElement("tr");
  tr.innerHTML = `
    <td style="color:#000000;font-size:10.0pt;text-align:general"></td>
    <td style="font-weight:bold;font-size:9.0pt;background:#E8EEF4;text-align:right;vertical-align:middle">TOTAL</td>
    <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">${totalSalary.toFixed(2)}</td>
    <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">${totalClasses}</td>
    <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">${totalRateShown.toFixed(2)}</td>
    <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">${totalCalculated.toFixed(2)}</td>
    <td style="color:#000000;font-size:10.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">0</td>
    <td style="font-weight:bold;font-size:9.0pt;background:#E8EEF4;text-align:center;vertical-align:middle"></td>
    <td style="font-weight:bold;font-size:9.0pt;background:#E8EEF4;text-align:center;vertical-align:middle">0</td>
  `;
  rateVerificationBody.appendChild(tr);
}

// 명시적으로 전역 데이터(`teachers`)를 로컬 스토리지에서 다시 읽薴와서 리포트 렌더링을 갱신
// ※ salary, rate는 원래 값(data.js 하드코딩)을 유지 → 수업수(classes) 보존
window.updateReportFromStorage = function () {
  const stored = localStorage.getItem("mangoi_teachers");
  if (stored) {
    const savedTeachers = JSON.parse(stored);
    // 기존 teachers의 원래 salary/rate를 보존하면서 점수/등급만 업데이트
    savedTeachers.forEach((saved) => {
      const orig = window.teachers.find((t) => t.name === saved.name);
      if (orig) {
        orig.scores = saved.scores;
        orig.weighted = saved.weighted;
        orig.grade = saved.grade;
        // salary, rate는 원래 data.js 값을 유지 (수업수 보존)
      }
    });
  }
  renderReportTables();
};

// ==================== 히트맵/트리맵용 전역 함수 (다른 페이지에서 호출 가능) ====================
// 히트맵/트리맵 페이지에서 salary를 표시할 때 이 함수를 사용하면 override된 수업수가 반영됨
window.getTeacherClasses = getTeacherClasses;
window.getTeacherMonthlySalary = getTeacherMonthlySalary;
window.getClassesOverrides = getClassesOverrides;
window.applyClassesOverridesToTeachers = applyClassesOverridesToTeachers;

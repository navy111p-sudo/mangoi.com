// js/heatmap.js
// 교사 평가 점수를 표(Grid) 형태의 히트맵으로 시각화

const scoreCategories = [
  { key: "inst", label: "수업 (Instruction)" },
  { key: "ret", label: "학생유지 (Retention)" },
  { key: "punct", label: "출석 (Punctuality)" },
  { key: "admin", label: "행정 (Admin)" },
  { key: "contrib", label: "기여도 (Contribution)" },
];

// 점수에 따른 배경색(히트맵 컬러) 반환 (1점: 빨강, 5점: 초록)
function getHeatmapColor(score) {
  // 1.0 ~ 5.0 range
  if (score >= 4.5) return "bg-green-500 text-white";
  if (score >= 4.0) return "bg-blue-500 text-white";
  if (score >= 3.0) return "bg-yellow-300 text-gray-800";
  if (score >= 2.0) return "bg-orange-400 text-white";
  return "bg-red-500 text-white";
}

// 급여 히트맵 색상 (급여 범위에 따른 색상)
function getSalaryHeatColor(salary) {
  if (salary >= 18000) return "bg-green-600 text-white";
  if (salary >= 14000) return "bg-green-400 text-white";
  if (salary >= 10000) return "bg-blue-400 text-white";
  if (salary >= 6000) return "bg-yellow-400 text-gray-800";
  if (salary > 0) return "bg-orange-400 text-white";
  return "bg-gray-200 text-gray-500";
}

// 범례 다국어 �W�퍼
function getLang() {
  return (typeof currentLang !== 'undefined') ? currentLang : 'ko';
}
function legendLabel() {
  const m = { ko: '점수 범례:', en: 'Score Legend:', tl: 'Gabay sa Marka:' };
  return m[getLang()] || m.ko;
}
function legendItem(color) {
  const items = {
    green:  { ko: '4.5 이상 (최우수)', en: '4.5+ (Outstanding)', tl: '4.5+ (Pinakamahusay)' },
    blue:   { ko: '4.0 이상 (우수)',   en: '4.0+ (Very Good)',   tl: '4.0+ (Napakahusay)' },
    yellow: { ko: '3.0 이상 (보통)',   en: '3.0+ (Average)',     tl: '3.0+ (Katamtaman)' },
    orange: { ko: '2.0 이상 (주의)',   en: '2.0+ (Caution)',     tl: '2.0+ (Pag-iingat)' },
    red:    { ko: '2.0 미만 (위험)',   en: 'Below 2.0 (Risk)',   tl: 'Mas mababa sa 2.0 (Panganib)' }
  };
  const lang = getLang();
  return items[color] ? (items[color][lang] || items[color].ko) : '';
}

// ==================== 총 수업수 override 관련 (report.js와 공유) ====================
const CLASSES_OVERRIDE_KEY = "mangoi_classes_override";

function getClassesOverrides() {
  const saved = localStorage.getItem(CLASSES_OVERRIDE_KEY);
  return saved ? JSON.parse(saved) : {};
}

function getTeacherClasses(t) {
  const overrides = getClassesOverrides();
  if (overrides.hasOwnProperty(t.name)) {
    return overrides[t.name];
  }
  const rate = t.rate || 0;
  const salary = t.salary || 0;
  return rate > 0 ? Math.floor(salary / rate) : 0;
}

function getTeacherMonthlySalary(t) {
  const classes = getTeacherClasses(t);
  const rate = t.rate || 0;
  return classes * rate;
}

function renderGridHeatmap() {
  const container = document.getElementById("heatmap-container");
  if (!container) return;

  // 전체 교사를 가중 점수(weighted) 기준으로 내림차순 정렬
  const sortedTeachers = [...teachers].sort((a, b) => b.weighted - a.weighted);

  let html = `
    <table class="w-full text-left text-sm whitespace-nowrap">
      <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
        <tr>
          <th class="py-3 px-4 sticky left-0 bg-gray-50 z-10 w-48">교사 이름</th>
          <th class="py-3 px-4 text-center border-l border-gray-200">종합 등급</th>
          <th class="py-3 px-4 text-center border-l border-gray-200">평균 가중치</th>
  `;

  scoreCategories.forEach((cat) => {
    html += `<th class="py-3 px-4 text-center border-l border-gray-200">${cat.label}</th>`;
  });

  // 총급여 컬럼 추가
  html += `<th class="py-3 px-4 text-center border-l border-gray-200" style="background:#FFF8E1">총급여 (Monthly Salary)</th>`;

  html += `
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
  `;

  sortedTeachers.forEach((t) => {
    const monthlySalary = getTeacherMonthlySalary(t);
    const salaryColor = getSalaryHeatColor(monthlySalary);

    html += `
      <tr class="hover:bg-gray-50 transition-colors">
        <td class="py-3 px-4 sticky left-0 bg-white z-10 font-bold border-r border-gray-100">
          <div class="flex flex-col">
            <span class="text-gray-900 text-base">${t.name}</span>
            <span class="text-xs text-gray-500 font-normal mt-0.5">${t.status.toUpperCase()} (${t.years}년)</span>
          </div>
        </td>
        <td class="py-3 px-2 text-center border-l border-gray-100">
          <span class="inline-block px-2 py-1 rounded text-xs font-bold text-white shadow-sm" style="background-color: ${gradeColor[t.grade]}">
            ${t.grade}
          </span>
        </td>
        <td class="py-3 px-4 text-center font-mono font-bold text-gray-700 border-l border-gray-100">
          ${t.weighted.toFixed(2)}
        </td>
    `;

    scoreCategories.forEach((cat) => {
      const score = t.scores[cat.key];
      const colorClass = getHeatmapColor(score);
      html += `
        <td class="py-2 px-2 border-l border-gray-100">
          <div class="flex items-center justify-center h-10 w-full rounded ${colorClass} font-mono font-bold shadow-sm transition-transform hover:scale-105 cursor-default" title="${t.name} - ${cat.label}: ${score}점">
            ${score.toFixed(1)}
          </div>
        </td>
      `;
    });

    // 총급여 셀
    html += `
        <td class="py-2 px-2 border-l border-gray-100">
          <div class="flex items-center justify-center h-10 w-full rounded ${salaryColor} font-mono font-bold shadow-sm transition-transform hover:scale-105 cursor-default" title="${t.name} - 총급여: ₱${monthlySalary.toLocaleString()}">
            ₱${monthlySalary.toLocaleString()}
          </div>
        </td>
    `;

    html += `</tr>`;
  });

  html += `
      </tbody>
    </table>

    <!-- 범례 (3개국어) -->
    <div class="mt-6 flex flex-wrap gap-4 items-center justify-end text-xs text-gray-600 p-4 bg-gray-50 rounded-lg">
      <span class="font-bold mr-2 text-gray-700">${legendLabel()}</span>
      <div class="flex items-center gap-2"><div class="w-6 h-6 rounded bg-green-500"></div> ${legendItem('green')}</div>
      <div class="flex items-center gap-2"><div class="w-6 h-6 rounded bg-blue-500"></div> ${legendItem('blue')}</div>
      <div class="flex items-center gap-2"><div class="w-6 h-6 rounded bg-yellow-300"></div> ${legendItem('yellow')}</div>
      <div class="flex items-center gap-2"><div class="w-6 h-6 rounded bg-orange-400"></div> ${legendItem('orange')}</div>
      <div class="flex items-center gap-2"><div class="w-6 h-6 rounded bg-red-500"></div> ${legendItem('red')}</div>
    </div>
  `;

  container.innerHTML = html;
}

document.addEventListener("DOMContentLoaded", () => {
  renderGridHeatmap();
});

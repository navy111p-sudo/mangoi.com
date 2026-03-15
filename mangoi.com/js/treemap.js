// js/treemap.js
// 교사별 월급여를 트리맵(면적 비율)으로 시각화

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

// ==================== 등급별 색상 ====================
const gradeColors = {
  "Outstanding":       { bg: "#10b981", text: "#fff" },
  "Very Satisfactory": { bg: "#3b82f6", text: "#fff" },
  "Satisfactory":      { bg: "#6b7280", text: "#fff" },
  "Needs Improvement": { bg: "#ef4444", text: "#fff" },
};

function getGradeColor(grade) {
  return gradeColors[grade] || { bg: "#9ca3af", text: "#fff" };
}

// ==================== Squarified Treemap 알고리즘 ====================
function squarify(data, rect) {
  if (data.length === 0) return [];

  const totalValue = data.reduce((s, d) => s + d.value, 0);
  if (totalValue <= 0) return [];

  const results = [];
  _squarifyRecursive([...data], rect, totalValue, results);
  return results;
}

function _squarifyRecursive(data, rect, totalValue, results) {
  if (data.length === 0) return;
  if (data.length === 1) {
    results.push({ ...data[0], x: rect.x, y: rect.y, w: rect.w, h: rect.h });
    return;
  }

  const area = rect.w * rect.h;
  const isWide = rect.w >= rect.h;

  let row = [];
  let rowSum = 0;
  let bestAspect = Infinity;

  for (let i = 0; i < data.length; i++) {
    const item = data[i];
    const testSum = rowSum + item.value;
    const testRow = [...row, item];

    const aspect = _worstAspect(testRow, testSum, totalValue, area, isWide ? rect.w : rect.h);

    if (aspect <= bestAspect) {
      row = testRow;
      rowSum = testSum;
      bestAspect = aspect;
    } else {
      // 현재 행 레이아웃
      const rowRect = _layoutRow(row, rowSum, totalValue, area, rect, isWide);
      rowRect.forEach(r => results.push(r));

      // 남은 영역 계산
      const rowFraction = rowSum / totalValue;
      let newRect;
      if (isWide) {
        const usedW = rect.w * rowFraction;
        newRect = { x: rect.x + usedW, y: rect.y, w: rect.w - usedW, h: rect.h };
      } else {
        const usedH = rect.h * rowFraction;
        newRect = { x: rect.x, y: rect.y + usedH, w: rect.w, h: rect.h - usedH };
      }

      const remaining = data.slice(i);
      const remainingTotal = remaining.reduce((s, d) => s + d.value, 0);
      _squarifyRecursive(remaining, newRect, remainingTotal, results);
      return;
    }
  }

  // 모든 데이터가 한 행에 들어감
  const rowRect = _layoutRow(row, rowSum, totalValue, area, rect, isWide);
  rowRect.forEach(r => results.push(r));
}

function _worstAspect(row, rowSum, totalValue, area, side) {
  const rowArea = (rowSum / totalValue) * area;
  const rowSide = rowArea / side;
  let worst = 0;

  for (const item of row) {
    const itemArea = (item.value / totalValue) * area;
    const itemSide = itemArea / rowSide;
    const aspect = Math.max(itemSide / rowSide, rowSide / itemSide);
    if (aspect > worst) worst = aspect;
  }
  return worst;
}

function _layoutRow(row, rowSum, totalValue, area, rect, isWide) {
  const results = [];
  const rowFraction = rowSum / totalValue;

  if (isWide) {
    const rowW = rect.w * rowFraction;
    let currentY = rect.y;
    for (const item of row) {
      const itemH = rect.h * (item.value / rowSum);
      results.push({ ...item, x: rect.x, y: currentY, w: rowW, h: itemH });
      currentY += itemH;
    }
  } else {
    const rowH = rect.h * rowFraction;
    let currentX = rect.x;
    for (const item of row) {
      const itemW = rect.w * (item.value / rowSum);
      results.push({ ...item, x: currentX, y: rect.y, w: itemW, h: rowH });
      currentX += itemW;
    }
  }
  return results;
}

// ==================== 범례 다국어 ====================
function getLang() {
  return (typeof currentLang !== 'undefined') ? currentLang : 'ko';
}

function renderTreemapLegend() {
  const legend = document.getElementById("treemap-legend");
  if (!legend) return;

  const lang = getLang();
  const labels = {
    title: { ko: '등급 범례:', en: 'Grade Legend:', tl: 'Gabay sa Grado:' },
    outstanding: { ko: 'Outstanding (최우수)', en: 'Outstanding', tl: 'Pinakamahusay' },
    very: { ko: 'Very Satisfactory (우수)', en: 'Very Satisfactory', tl: 'Napakahusay' },
    satisfactory: { ko: 'Satisfactory (보통)', en: 'Satisfactory', tl: 'Katamtaman' },
    needs: { ko: 'Needs Improvement (주의)', en: 'Needs Improvement', tl: 'Kailangan Pagbutihin' },
    note: { ko: '면적 = 월급여 비율', en: 'Area = Monthly Salary Ratio', tl: 'Lugar = Ratio ng Buwanang Sahod' },
  };

  legend.innerHTML = `
    <span class="font-bold mr-2 text-gray-700">${labels.title[lang] || labels.title.ko}</span>
    <div class="flex items-center gap-2"><div class="w-6 h-6 rounded" style="background:#10b981"></div> ${labels.outstanding[lang] || labels.outstanding.ko}</div>
    <div class="flex items-center gap-2"><div class="w-6 h-6 rounded" style="background:#3b82f6"></div> ${labels.very[lang] || labels.very.ko}</div>
    <div class="flex items-center gap-2"><div class="w-6 h-6 rounded" style="background:#6b7280"></div> ${labels.satisfactory[lang] || labels.satisfactory.ko}</div>
    <div class="flex items-center gap-2"><div class="w-6 h-6 rounded" style="background:#ef4444"></div> ${labels.needs[lang] || labels.needs.ko}</div>
    <div class="ml-4 text-gray-500 italic">${labels.note[lang] || labels.note.ko}</div>
  `;
}

// ==================== 트리맵 렌더링 ====================
function renderTreemap() {
  const container = document.getElementById("treemap-container");
  if (!container) return;

  // 데이터 준비 (급여 기준 내림차순 정렬)
  const data = teachers.map(t => {
    const salary = getTeacherMonthlySalary(t);
    return {
      name: t.name,
      status: t.status,
      grade: t.grade,
      weighted: t.weighted,
      salary: salary,
      classes: getTeacherClasses(t),
      rate: t.rate || 0,
      value: Math.max(salary, 1), // 최소 1 (0이면 면적 계산 불가)
    };
  }).sort((a, b) => b.value - a.value);

  // 급여가 0인 교사 필터링 (선택적)
  const validData = data.filter(d => d.salary > 0);

  if (validData.length === 0) {
    container.innerHTML = '<div class="flex items-center justify-center h-full text-gray-400 text-lg">데이터가 없습니다.</div>';
    return;
  }

  const rect = { x: 0, y: 0, w: container.offsetWidth, h: container.offsetHeight };
  const layout = squarify(validData, rect);

  const totalSalary = validData.reduce((s, d) => s + d.salary, 0);

  let html = '';
  layout.forEach(item => {
    const color = getGradeColor(item.grade);
    const pct = ((item.salary / totalSalary) * 100).toFixed(1);
    const minDim = Math.min(item.w, item.h);

    // 크기에 따라 텍스트 표시 조정
    let content = '';
    if (minDim > 60) {
      content = `
        <div class="font-bold text-sm truncate" style="max-width:${item.w - 12}px">${item.name}</div>
        <div class="text-xs opacity-90 mt-0.5">₱${item.salary.toLocaleString()}</div>
        <div class="text-xs opacity-75">${pct}%</div>
      `;
    } else if (minDim > 35) {
      content = `
        <div class="font-bold text-xs truncate" style="max-width:${item.w - 8}px">${item.name}</div>
        <div class="text-xs opacity-80">₱${item.salary.toLocaleString()}</div>
      `;
    } else if (minDim > 20) {
      content = `<div class="font-bold truncate" style="font-size:9px;max-width:${item.w - 6}px">${item.name}</div>`;
    }

    html += `
      <div class="absolute flex flex-col items-center justify-center overflow-hidden cursor-default transition-opacity hover:opacity-90"
           style="left:${item.x}px;top:${item.y}px;width:${item.w}px;height:${item.h}px;
                  background:${color.bg};color:${color.text};
                  border:1px solid rgba(255,255,255,0.3);border-radius:4px;padding:4px;"
           title="${item.name} (${item.grade})\n월급여: ₱${item.salary.toLocaleString()} (${pct}%)\n순업수: ${item.classes}회 × ₱${item.rate}/10분\n가중평균: ${item.weighted.toFixed(2)}">
        ${content}
      </div>
    `;
  });

  // 총급여 요약
  html += `
    <div class="absolute bottom-0 right-0 bg-black bg-opacity-60 text-white text-xs px-3 py-1.5 rounded-tl-lg" style="z-index:10">
      총급여 합계: ₱${totalSalary.toLocaleString()}
    </div>
  `;

  container.innerHTML = html;

  // 범례 렌더링
  renderTreemapLegend();
}

// ==================== 초기화 ====================
document.addEventListener("DOMContentLoaded", () => {
  renderTreemap();

  // 윈도우 리사이즈 시 재렌더링
  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(renderTreemap, 200);
  });
});

<?php
require __DIR__ . '/auth.php';
requireLoginPage();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>دفتر الجمعيات والحسابات</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  :root{
    --ink:#F4EFE0;
    --ink-dim:#CBC2A6;
    --cover:#12332A;
    --cover-2:#0C241D;
    --gold:#C9962C;
    --gold-light:#E6B95C;
    --paper:#FBF7EC;
    --paper-2:#F2ECDA;
    --line:#D8CFB0;
    --danger:#A3402F;
    --success:#2F6B4F;
    --shadow: 0 10px 30px rgba(0,0,0,0.15);
  }
  *{box-sizing:border-box;}
  body{
    margin:0;
    background: var(--cover-2);
    font-family:'Tajawal', sans-serif;
    color:var(--ink);
    min-height:100vh;
  }
  .app{ max-width:920px; margin:0 auto; padding:0 0 60px; }
  header.cover{
    background: linear-gradient(180deg, var(--cover) 0%, var(--cover-2) 100%);
    padding:28px 20px 22px;
    border-bottom: 3px double var(--gold);
    position:relative;
  }
  header.cover::after{
    content:""; position:absolute; left:0; right:0; bottom:-8px; height:6px;
    background-image: repeating-linear-gradient(90deg, var(--gold) 0 10px, transparent 10px 20px);
    opacity:0.6;
  }
  .brand{ display:flex; align-items:center; gap:14px; }
  .seal{
    width:52px;height:52px;border-radius:50%;
    background: radial-gradient(circle at 35% 30%, var(--gold-light), var(--gold) 60%, #8a6a1c 100%);
    display:flex;align-items:center;justify-content:center;
    font-family:'Amiri', serif; font-weight:700; font-size:22px; color:var(--cover-2);
    flex-shrink:0; box-shadow: inset 0 0 0 2px rgba(0,0,0,0.15);
  }
  h1{ font-family:'Amiri', serif; font-weight:700; font-size:26px; margin:0; color:var(--gold-light); }
  .sub{ margin:2px 0 0; font-size:13px; color:var(--ink-dim); }
  .body-layout{ display:flex; align-items:flex-start; min-height:60vh; }
  nav.tabs-sidebar{
    flex:0 0 120px; width:120px; background:var(--cover); display:flex; flex-direction:column;
    gap:8px; padding:14px 10px; box-sizing:border-box; max-height:calc(100vh - 120px);
    overflow-y:auto; scrollbar-width:none; border-left:3px double var(--gold);
  }
  nav.tabs-sidebar::-webkit-scrollbar{ display:none; }
  nav.tabs-sidebar button{
    width:100%; background:transparent; border:1px solid rgba(230,185,92,0.35); color:var(--ink-dim);
    padding:12px 6px; font-family:'Tajawal',sans-serif; font-weight:700; font-size:13px;
    border-radius:8px; cursor:pointer; word-break:break-word; text-align:center; line-height:1.3;
  }
  nav.tabs-sidebar button.active{ background:var(--paper); color:var(--cover-2); border-color:var(--paper); }
  nav.tabs-sidebar button.gear{ font-size:18px; padding:10px 6px; margin-top:auto; }
  main{ flex:1; min-width:0; }
  .logout-link{
    display:inline-flex; align-items:center; gap:4px; font-size:12px; color:var(--ink-dim);
    text-decoration:none; margin-top:14px; border:1px solid rgba(230,185,92,0.35); padding:6px 12px; border-radius:8px;
  }
  .logout-link:hover{ color:var(--gold-light); border-color:var(--gold-light); }
  .top-user{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; }
  main{ background:var(--paper); min-height:60vh; padding:22px 18px 40px; color:#000000; }
  .toolbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; gap:10px; flex-wrap:wrap; }
  .toolbar h2{ font-family:'Amiri', serif; color:var(--cover); font-size:20px; margin:0; }
  button.primary{
    background:var(--cover); color:var(--gold-light); border:none; padding:10px 16px; border-radius:8px;
    font-family:'Tajawal',sans-serif; font-weight:700; font-size:14px; cursor:pointer;
  }
  button.primary:hover{ background:var(--cover-2); }
  button.ghost{
    background:transparent; border:1px solid var(--line); color:var(--cover); padding:8px 12px;
    border-radius:8px; font-family:'Tajawal',sans-serif; font-weight:700; font-size:13px; cursor:pointer;
  }
  button.danger-outline{
    background:transparent; border:1px solid var(--danger); color:var(--danger); padding:6px 10px;
    border-radius:8px; font-size:12px; font-weight:700; cursor:pointer;
  }
  .empty{ text-align:center; padding:50px 20px; color:#8b8368; border:2px dashed var(--line); border-radius:12px; }
  .empty .icon{ font-size:34px; margin-bottom:8px; }
  .card{ background:#fff; border:1px solid var(--line); border-radius:12px; padding:16px; margin-bottom:14px; position:relative; }
  .card::before{ content:""; position:absolute; top:14px; bottom:14px; right:0; width:4px; background:var(--gold); border-radius:0 4px 4px 0; }
  .card-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:10px; margin-bottom:10px; }
  .card-title{ font-weight:900; font-size:16px; color:var(--cover); }
  .card-meta{ font-size:12px; color:#6b6248; margin-top:3px; }
  .badge{ display:inline-block; background:var(--paper-2); border:1px solid var(--line); color:var(--cover); font-size:12px; font-weight:700; padding:3px 10px; border-radius:20px; }
  .stats-row{ display:flex; gap:10px; margin:10px 0; flex-wrap:wrap; }
  .turn-stat{ cursor:pointer; border:1px solid #3B54C9; }
  .turn-stat .value{ color:#1E2E6B !important; }
  .turn-stat:hover{ background:#E3E9FA; }
  .stat{ background:var(--paper-2); border-radius:8px; padding:8px 12px; flex:1; min-width:110px; }
  .stat .label{ font-size:12px; color:#6b6248; }
  .stat .value{ font-size:20px; font-weight:900; color:var(--cover); font-variant-numeric:tabular-nums; }
  .card-actions{ display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; }
  .list{ display:flex; flex-direction:column; gap:8px; }
  .list-item{
    background:#fff; border:1px solid var(--line); border-radius:12px; padding:14px 16px;
    display:flex; justify-content:space-between; align-items:center; gap:10px; cursor:pointer;
    transition:border-color 0.15s; flex-wrap:wrap;
  }
  .list-item:hover{ border-color:var(--gold); }
  .list-item-main{ min-width:0; flex:1 1 140px; }
  .list-item-side{ display:flex; align-items:center; gap:8px; flex-shrink:0; flex-wrap:wrap; justify-content:flex-end; }
  .chevron{ font-size:20px; color:var(--gold); font-weight:900; }
  .list-btn{
    border:none; color:#fff; font-weight:700; font-size:12px;
    padding:7px 14px; border-radius:7px; cursor:pointer; white-space:nowrap;
  }
  .list-btn.edit{ background:var(--cover); }
  .list-btn.edit:hover{ background:var(--cover-2); }
  .list-btn.delete{ background:var(--danger); }
  .list-btn.delete:hover{ background:#7d2e21; }
  .back-btn{
    background:transparent; border:none; color:var(--cover); font-weight:700; font-size:14px;
    cursor:pointer; padding:6px 0; margin-bottom:12px; display:inline-block;
  }
  .back-btn:hover{ text-decoration:underline; }
  table.sched{ width:100%; border-collapse:collapse; margin-top:8px; font-size:15px; color:#000000; }
  table.sched th, table.sched td{ text-align:right; padding:9px 8px; border-bottom:1px solid var(--paper-2); }
  table.sched th{ color:#6b6248; font-weight:700; font-size:13px; }
  table.sched td.amount{ font-variant-numeric:tabular-nums; font-weight:700; }
  table.sched td:first-child, table.sched th:first-child{ font-size:15px; }
  table.sched td:nth-child(2){ font-size:15px; font-weight:700; }
  table.sched tr.paid-row{ background:#DCF0E3; }
  table.sched tr.paid-row td{ color:#1E4A34; font-weight:700; border-bottom-color:#BFE3CE; }
  table.sched tr.my-turn-row{ background:#E3E9FA; }
  table.sched tr.my-turn-row td{ color:#1E2E6B; font-weight:700; border-bottom-color:#C6D0F2; }
  table.sched tr.my-turn-row.paid-row{ background:linear-gradient(90deg,#DCF0E3 50%,#E3E9FA 50%); }
  .turn-badge{ display:inline-flex; align-items:center; gap:4px; background:#3B54C9; color:#fff; border:1px solid #3B54C9; border-radius:20px; font-size:11px; font-weight:700; padding:3px 10px; }
  .turn-btn{ border:1px solid #3B54C9; background:#E3E9FA; color:#1E2E6B; border-radius:6px; font-size:11px; padding:5px 10px; cursor:pointer; font-weight:900; }
  .turn-btn:hover{ background:#C6D0F2; }
  .turn-undo{ border:none; background:transparent; color:#1E2E6B; font-size:11px; text-decoration:underline; cursor:pointer; padding:2px; font-weight:700; }
  .pay-cell{ display:flex; align-items:center; gap:8px; justify-content:flex-end; flex-wrap:wrap; }
  .paid-badge{ display:inline-flex; align-items:center; gap:4px; background:var(--success); color:#fff; border:1px solid var(--success); border-radius:20px; font-size:11px; font-weight:700; padding:3px 10px; }
  .pay-btn{ border:1px solid var(--gold); background:var(--gold-light); color:#4A3702; border-radius:6px; font-size:12px; padding:5px 12px; cursor:pointer; font-weight:900; }
  .pay-btn:hover{ background:var(--gold); }
  .undo-btn{ border:none; background:transparent; color:#1E4A34; font-size:11px; text-decoration:underline; cursor:pointer; padding:2px; font-weight:700; }
  .members-list{ margin-top:10px; border-top:1px dashed var(--line); padding-top:10px; }
  .member-row{ display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--paper-2); font-size:14px; }
  .member-row .name{ font-weight:700; color:#000000; }
  .member-row .phone{ font-size:12px; color:#6b6248; }
  .member-row .amount{ font-weight:900; font-size:16px; font-variant-numeric:tabular-nums; }
  .icon-btn{
    border:none; cursor:pointer; font-size:14px; width:32px; height:32px; border-radius:8px;
    color:#fff; background:var(--cover); display:inline-flex; align-items:center; justify-content:center;
    flex-shrink:0;
  }
  .icon-btn:hover{ background:var(--cover-2); }
  .icon-btn.del{ background:var(--danger); }
  .icon-btn.del:hover{ background:#7d2e21; }
  .overlay{ position:fixed; inset:0; background:rgba(12,36,29,0.6); display:flex; align-items:flex-end; justify-content:center; z-index:50; }
  .sheet{ background:var(--paper); width:100%; max-width:520px; border-radius:16px 16px 0 0; padding:20px; max-height:88vh; overflow-y:auto; box-shadow: var(--shadow); color:#000000; }
  @media (min-width:600px){ .overlay{ align-items:center; } .sheet{ border-radius:16px; } }
  .sheet h3{ font-family:'Amiri', serif; color:var(--cover); margin:0 0 14px; font-size:19px; }
  .field{ margin-bottom:12px; }
  .field label{ display:block; font-size:13px; font-weight:700; color:var(--cover); margin-bottom:5px; }
  .field input, .field select{ width:100%; padding:10px; border-radius:8px; border:1px solid var(--line); font-family:'Tajawal',sans-serif; font-size:14px; background:#fff; color:var(--cover); }
  .field-row{ display:flex; gap:10px; }
  .field-row .field{ flex:1; }
  .sheet-actions{ display:flex; gap:10px; margin-top:16px; }
  .sheet-actions button{ flex:1; padding:11px; }
  .error-msg{ color:var(--danger); font-size:12px; margin-top:4px; display:none; }
  .invoice-wrap{ text-align:center; }
  .invoice{ background:#fff; border:1px solid var(--line); border-radius:10px; padding:22px; text-align:right; margin-bottom:16px; }
  .invoice h4{ font-family:'Amiri',serif; color:var(--cover); text-align:center; margin:0 0 4px; font-size:20px; }
  .invoice .inv-sub{ text-align:center; color:#6b6248; font-size:12px; margin-bottom:16px; }
  .invoice table{ width:100%; border-collapse:collapse; font-size:13px; }
  .invoice td, .invoice th{ padding:7px 4px; border-bottom:1px solid var(--paper-2); text-align:right; }
  .invoice .total-row td{ font-weight:900; font-size:16px; border-top:2px solid var(--cover); border-bottom:none; }

  .ledger-table{ border:1px solid #999 !important; font-size:17px !important; }
  .ledger-table th{
    background:#D9D9D9; color:#1414A0; font-weight:900; text-align:center !important;
    border:1px solid #999 !important; font-size:17px !important; padding:11px 8px !important;
  }
  .ledger-table td{ border:1px solid #999 !important; text-align:center !important; font-weight:700; padding:11px 8px !important; line-height:1.6; font-size:17px !important; }
  .ledger-table .ledger-details{ color:#1414A0; text-align:right !important; padding-right:10px !important; }
  .ledger-table .ledger-date{ color:#1414A0; }
  .ledger-table .ledger-balance{ color:#A3002B; font-weight:900; }
  .ledger-table .ledger-debit{ color:#A3002B; }
  .ledger-table .ledger-credit{ color:#1E8A3C; }
  .ledger-table .ledger-totals-row td{ background:#EDEDED; font-weight:900; }
  .ledger-table .ledger-totals-row .ledger-debit{ color:#A3002B; }
  .ledger-table .ledger-totals-row .ledger-credit{ color:#1E8A3C; }
  .ledger-table .ledger-final-row td{
    background:#F5B7B1; color:#1414A0; font-weight:900; font-size:19px !important; text-align:center !important; padding:13px 8px !important;
  }
  .loading{ text-align:center; padding:40px; color:#8b8368; }
  @media print{
    @page{ margin: 5mm; }
    body > *:not(#printArea){ display:none !important; }
    html, body{ height:auto !important; margin:0 !important; padding:0 !important; }
    #printArea{ display:block !important; position:static !important; margin:0; padding:6px; background:#fff; color:#000 !important; }
    #printArea *{ color:#000 !important; }
    #printArea .invoice{ padding:10px; border:none; }
    #printArea h4{ font-weight:900 !important; font-size:18px !important; margin:0 0 3px !important; }
    #printArea .inv-sub{ font-size:12px !important; margin-bottom:10px !important; }
    #printArea table{ font-size:12px !important; border-collapse:collapse !important; }
    #printArea td, #printArea th{ color:#000 !important; font-weight:700 !important; border-bottom-color:#999 !important; padding:5px 6px !important; line-height:1.4 !important; }
    #printArea .total-row td{ font-weight:900 !important; font-size:13px !important; border-top-color:#000 !important; padding-top:7px !important; }

    #printArea .ledger-table th{ background:#D9D9D9 !important; color:#1414A0 !important; font-size:19px !important; padding:12px 8px !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    #printArea .ledger-table td{ color:inherit !important; font-size:19px !important; padding:12px 8px !important; line-height:1.6 !important; }
    #printArea .ledger-table .ledger-details, #printArea .ledger-table .ledger-date{ color:#1414A0 !important; }
    #printArea .ledger-table .ledger-debit{ color:#A3002B !important; }
    #printArea .ledger-table .ledger-credit{ color:#1E8A3C !important; }
    #printArea .ledger-table .ledger-balance{ color:#A3002B !important; }
    #printArea .ledger-table .ledger-totals-row td{ background:#EDEDED !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    #printArea .ledger-table .ledger-totals-row .ledger-debit{ color:#A3002B !important; }
    #printArea .ledger-table .ledger-totals-row .ledger-credit{ color:#1E8A3C !important; }
    #printArea .ledger-table .ledger-final-row td{ background:#F5B7B1 !important; color:#1414A0 !important; font-size:21px !important; padding:14px 8px !important; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
  }
</style>
</head>
<body>
<div class="app">
  <header class="cover">
    <div class="brand top-user">
      <div style="display:flex;align-items:center;gap:14px;">
        <div class="seal">ج</div>
        <div>
          <h1>دفتر الجمعيات والحسابات</h1>
          <p class="sub">إدارة الجمعيات وحسابات الأفراد بسهولة</p>
        </div>
      </div>
      <a class="logout-link" href="logout.php">خروج (<?php echo htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>)</a>
    </div>
  </header>
  <div class="body-layout">
    <nav class="tabs-sidebar" id="tabsBar"></nav>
    <main>
      <div id="viewMain"><div class="loading">جاري التحميل...</div></div>
    </main>
  </div>
</div>
<div id="modalRoot"></div>
<div id="printArea" style="display:none;"></div>

<script>
const API = 'api.php';
const CUR = { EGP: 'ج.م', USD: '$' };
let state = { gam3eyas: [], individuals: [], sections: [] };
let activeSectionId = null;
let selectedGamId = null;
let selectedIndId = null;

function fmt(n){
  n = Number(n)||0;
  return n.toLocaleString('ar-EG-u-nu-latn', {maximumFractionDigits:2});
}
function esc(s){
  return String(s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}
function formatDate(d){
  return `${d.getDate()}/${d.getMonth()+1}/${d.getFullYear()}`;
}

async function api(endpoint, options={}){
  const res = await fetch(`${API}?endpoint=${endpoint}${options.qs||''}`, {
    method: options.method || 'GET',
    headers: options.body ? {'Content-Type':'application/json'} : {},
    body: options.body ? JSON.stringify(options.body) : undefined
  });
  if(!res.ok){
    const err = await res.json().catch(()=>({error:'حدث خطأ'}));
    throw new Error(err.error || 'حدث خطأ');
  }
  return res.json();
}

async function loadState(){
  try{
    const [g, i, sec] = await Promise.all([
      api('gam3eyas'), api('individuals'), api('sections')
    ]);
    state.gam3eyas = g;
    state.individuals = i;
    state.sections = sec;
    if(activeSectionId && !sec.find(s=>s.id===activeSectionId)) activeSectionId = null;
    if(!activeSectionId && sec.length) activeSectionId = sec[0].id;
  }catch(e){
    document.getElementById('viewMain').innerHTML = `<div class="empty">تعذر الاتصال بالخادم<br><span style="font-size:12px;">${esc(e.message)}</span></div>`;
    return;
  }
  renderTabsBar();
  render();
}

function currentSection(){ return state.sections.find(s=>s.id===activeSectionId); }

function selectTab(id){
  activeSectionId = id;
  selectedGamId = null;
  selectedIndId = null;
  renderTabsBar();
  render();
}

function renderTabsBar(){
  const bar = document.getElementById('tabsBar');
  let html = state.sections.map(s => `
    <button class="${s.id===activeSectionId?'active':''}" onclick="selectTab(${s.id})">${esc(s.name)}</button>
  `).join('');
  html += `<button class="gear" onclick="openTabsManager()" title="إدارة التبويبات">⚙</button>`;
  bar.innerHTML = html;
}

function openGamDetail(id){ selectedGamId = id; render(); }
function closeGamDetail(){ selectedGamId = null; render(); }
function openIndDetail(id){ selectedIndId = id; render(); }
function closeIndDetail(){ selectedIndId = null; render(); }

function render(){
  const el = document.getElementById('viewMain');
  if(!state.sections.length){
    el.innerHTML = `<div class="empty"><div class="icon">🗂️</div>مفيش تبويبات لسه<br>ضيف أول تبويب من زرار ⚙ فوق</div>`;
    return;
  }
  const sec = currentSection();
  if(!sec){ el.innerHTML = ''; return; }
  if(sec.type==='gam3eya') renderGam(sec); else renderInd(sec);
}


function renderGam(sec){
  const el = document.getElementById('viewMain');

  if(selectedGamId){
    const g = state.gam3eyas.find(x=>x.id===selectedGamId);
    if(!g){ selectedGamId=null; renderGam(sec); return; }
    const paidCount = g.schedule.filter(s=>Number(s.paid)===1).length;
    const total = g.schedule.reduce((a,s)=>a+Number(s.amount),0);
    el.innerHTML = `
      <button class="back-btn" onclick="closeGamDetail()">‹ رجوع لـ${esc(sec.name)}</button>
      <div class="card">
        <div class="card-head">
          <div>
            <div class="card-title">${esc(g.name)}</div>
            <div class="card-meta">تبدأ ${formatDate(new Date(g.start_date))} · ${g.months} شهر</div>
          </div>
          <span class="badge">${CUR[g.currency]}</span>
        </div>
        <div class="stats-row">
          <div class="stat"><div class="label">القسط الشهري</div><div class="value">${fmt(g.monthly_amount)} ${CUR[g.currency]}</div></div>
          <div class="stat"><div class="label">الإجمالي</div><div class="value">${fmt(total)} ${CUR[g.currency]}</div></div>
          <div class="stat"><div class="label">المدفوع</div><div class="value">${paidCount}/${g.months}</div></div>
          ${sec.has_turns == 1 ? `<div class="stat turn-stat" onclick="openMyTurnForm(${g.id})">
            <div class="label">أدوارك</div>
            <div class="value">${g.my_turns.length ? g.my_turns.join(', ') : 'غير محدد'} ✎</div>
          </div>` : ''}
        </div>
        <table class="sched">
          <tr><th>#</th><th>التاريخ</th><th>المبلغ</th><th>الحالة</th></tr>
          ${g.schedule.map(s=>{
            const isTurn = sec.has_turns == 1 && g.my_turns.includes(s.month_idx);
            const rowClass = `${Number(s.paid)===1?'paid-row':''} ${isTurn?'my-turn-row':''}`.trim();
            return `<tr class="${rowClass}">
            <td>${s.month_idx}</td>
            <td>${formatDate(new Date(s.due_date))}</td>
            <td class="amount">${fmt(s.amount)} ${CUR[g.currency]}</td>
            <td><div class="pay-cell">
              ${Number(s.paid)===1
                ? `<span class="paid-badge">✓ تم السداد</span><button class="undo-btn" onclick="toggleGamPaid(${s.id})">تراجع</button>`
                : `<button class="pay-btn" onclick="toggleGamPaid(${s.id})">دفع</button>`
              }
              ${isTurn ? `<span class="turn-badge">★ دورك</span>` : ``}
            </div></td>
          </tr>`;
          }).join('')}
        </table>
        <div class="card-actions">
          <button class="ghost" onclick="openGamInvoice(${g.id})">فاتورة PDF</button>
          <button class="danger-outline" onclick="deleteGam(${g.id})">حذف</button>
        </div>
      </div>`;
    return;
  }

  const items = state.gam3eyas.filter(g=>Number(g.section_id)===sec.id);
  let html = `<div class="toolbar"><h2>${esc(sec.name)}</h2><button class="primary" onclick="openGamForm()">+ إضافة ${esc(sec.name)}</button></div>`;
  if(items.length===0){
    html += `<div class="empty"><div class="icon">📒</div>ابدأ بإضافة أول ${esc(sec.name)} هنا<br>حدد تاريخ البداية وعدد الشهور والمبلغ</div>`;
  } else {
    html += `<div class="list">`;
    items.forEach(g=>{
      const paidCount = g.schedule.filter(s=>Number(s.paid)===1).length;
      html += `<div class="list-item" onclick="openGamDetail(${g.id})">
        <div class="list-item-main">
          <div class="card-title">${esc(g.name)}</div>
          <div class="card-meta">${g.months} شهر · مدفوع ${paidCount}/${g.months}</div>
        </div>
        <div class="list-item-side">
          <span class="badge">${CUR[g.currency]}</span>
          <button class="list-btn edit" onclick="event.stopPropagation();openGamRename(${g.id})">تعديل</button>
          <button class="list-btn delete" onclick="event.stopPropagation();deleteGam(${g.id})">حذف</button>
          <span class="chevron">‹</span>
        </div>
      </div>`;
    });
    html += `</div>`;
  }
  el.innerHTML = html;
}

function renderInd(sec){
  const el = document.getElementById('viewMain');

  if(selectedIndId){
    const p = state.individuals.find(x=>x.id===selectedIndId);
    if(!p){ selectedIndId=null; renderInd(sec); return; }
    const total = p.entries.reduce((a,e)=> a + (e.type==='debit'? Number(e.amount) : -Number(e.amount)), 0);
    el.innerHTML = `
      <button class="back-btn" onclick="closeIndDetail()">‹ رجوع لـ${esc(sec.name)}</button>
      <div class="card">
        <div class="card-head">
          <div>
            <div class="card-title">${esc(p.name)}</div>
            <div class="card-meta">${esc(p.phone||'بدون رقم هاتف')}</div>
          </div>
          <span class="badge">${CUR[p.currency]}</span>
        </div>
        <div class="stats-row">
          <div class="stat"><div class="label">الإجمالي المستحق</div><div class="value">${fmt(total)} ${CUR[p.currency]}</div></div>
          <div class="stat"><div class="label">عدد الحركات</div><div class="value">${p.entries.length}</div></div>
        </div>
        <div class="members-list">
          ${p.entries.map(e=>`<div class="member-row">
            <div><div class="name">${esc(e.note||'حركة')}</div><div class="phone">${formatDate(new Date(e.entry_date))}</div></div>
            <div style="display:flex;align-items:center;gap:10px;">
              <div class="amount" style="color:${e.type==='debit'?'#1E4A34':'var(--danger)'}">${e.type==='debit'?'':'-'}${fmt(e.amount)} ${CUR[p.currency]}</div>
              <button class="icon-btn" onclick="openEntryForm(${p.id},${e.id})" aria-label="تعديل"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg></button>
              <button class="icon-btn del" onclick="deleteEntry(${e.id},${p.id})" aria-label="حذف">✕</button>
            </div>
          </div>`).join('') || '<p style="font-size:13px;color:#6b6248;">لا توجد حركات مسجلة</p>'}
        </div>
        <div class="card-actions">
          <button class="ghost" onclick="openEntryForm(${p.id})">+ إضافة حركة</button>
          <button class="ghost" onclick="openIndInvoice(${p.id})">فاتورة PDF</button>
          <button class="danger-outline" onclick="deleteInd(${p.id})">حذف الفرد</button>
        </div>
      </div>`;
    return;
  }

  const items = state.individuals.filter(p=>Number(p.section_id)===sec.id);
  let html = `<div class="toolbar"><h2>${esc(sec.name)}</h2><button class="primary" onclick="openIndForm()">+ إضافة ${esc(sec.name)}</button></div>`;
  if(items.length===0){
    html += `<div class="empty"><div class="icon">👤</div>لا يوجد أحد بعد في ${esc(sec.name)}<br>أضف عنصر وسجل حسابه</div>`;
  } else {
    html += `<div class="list">`;
    items.forEach(p=>{
      const total = p.entries.reduce((a,e)=> a + (e.type==='debit'? Number(e.amount) : -Number(e.amount)), 0);
      html += `<div class="list-item" onclick="openIndDetail(${p.id})">
        <div class="list-item-main">
          <div class="card-title">${esc(p.name)}</div>
          <div class="card-meta">${esc(p.phone||'بدون رقم هاتف')} · الإجمالي ${fmt(total)} ${CUR[p.currency]}</div>
        </div>
        <div class="list-item-side">
          <span class="badge">${CUR[p.currency]}</span>
          <button class="list-btn edit" onclick="event.stopPropagation();openIndEdit(${p.id})">تعديل</button>
          <button class="list-btn delete" onclick="event.stopPropagation();deleteInd(${p.id})">حذف</button>
          <span class="chevron">‹</span>
        </div>
      </div>`;
    });
    html += `</div>`;
  }
  el.innerHTML = html;
}

function closeModal(){ document.getElementById('modalRoot').innerHTML=''; }

function openTabsManager(){
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>إدارة التبويبات</h3>
      <div class="list" style="margin-bottom:16px;">
        ${state.sections.map(s => `
          <div class="list-item" style="cursor:default;">
            <div class="list-item-main">
              <div class="card-title">${esc(s.name)}</div>
              <div class="card-meta">${s.type==='gam3eya' ? 'نوع: جمعيات' : 'نوع: حسابات أفراد'}</div>
            </div>
            <div class="list-item-side">
              <button class="list-btn edit" onclick="openSectionRename(${s.id})">تعديل</button>
              <button class="list-btn delete" onclick="deleteSection(${s.id})">حذف</button>
            </div>
          </div>
        `).join('') || '<p style="font-size:13px;color:#6b6248;">لا توجد تبويبات بعد</p>'}
      </div>
      <button class="primary" style="width:100%;" onclick="closeModal();openSectionForm();">+ إضافة تبويب جديد</button>
    </div>
  </div>`;
}

function openSectionForm(){
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>إضافة تبويب جديد</h3>
      <div class="field"><label>اسم التبويب</label><input id="secName" placeholder="مثال: شقة، جمعية العيلة، أقساط، عملاء المحل..."></div>
      <div class="field">
        <label>نوع التبويب</label>
        <select id="secType" onchange="toggleTurnsFieldVisibility()">
          <option value="gam3eya">جمعيات (بتواريخ وأقساط شهرية)</option>
          <option value="individual">حسابات أفراد (بحركات وأرصدة)</option>
        </select>
      </div>
      <div class="field" id="secTurnsField">
        <label style="display:flex;align-items:center;gap:8px;font-weight:700;">
          <input type="checkbox" id="secHasTurns">
          تفعيل خاصية "الأدوار" (للجمعيات الدورية اللي بياخد فيها كل شخص دوره)
        </label>
      </div>
      <div class="error-msg" id="secErr"></div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()">إلغاء</button>
        <button class="primary" onclick="saveSectionForm()">حفظ التبويب</button>
      </div>
    </div>
  </div>`;
}

function toggleTurnsFieldVisibility(){
  const type = document.getElementById('secType').value;
  document.getElementById('secTurnsField').style.display = type === 'gam3eya' ? '' : 'none';
}

async function saveSectionForm(){
  const name = document.getElementById('secName').value.trim();
  const type = document.getElementById('secType').value;
  const hasTurns = type === 'gam3eya' && document.getElementById('secHasTurns').checked;
  const err = document.getElementById('secErr');
  if(!name){ err.textContent='اسم التبويب مطلوب'; err.style.display='block'; return; }
  try{
    const res = await api('sections', { method:'POST', body:{ name, type, hasTurns } });
    closeModal();
    activeSectionId = res.id;
    await loadState();
  }catch(e){ err.textContent = e.message; err.style.display='block'; }
}

function openSectionRename(id){
  const sec = state.sections.find(x=>x.id===id);
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>تعديل التبويب</h3>
      <div class="field"><label>اسم التبويب</label><input id="secRenameInput" value="${esc(sec.name)}"></div>
      ${sec.type === 'gam3eya' ? `
      <div class="field">
        <label style="display:flex;align-items:center;gap:8px;font-weight:700;">
          <input type="checkbox" id="secRenameHasTurns" ${Number(sec.has_turns)===1?'checked':''}>
          تفعيل خاصية "الأدوار"
        </label>
      </div>` : ''}
      <div class="error-msg" id="secRenameErr"></div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()">إلغاء</button>
        <button class="primary" onclick="saveSectionRename(${id})">حفظ</button>
      </div>
    </div>
  </div>`;
}

async function saveSectionRename(id){
  const name = document.getElementById('secRenameInput').value.trim();
  const hasTurnsEl = document.getElementById('secRenameHasTurns');
  const err = document.getElementById('secRenameErr');
  if(!name){ err.textContent='الاسم مطلوب'; err.style.display='block'; return; }
  try{
    const body = { id, name };
    if(hasTurnsEl) body.hasTurns = hasTurnsEl.checked;
    await api('sections', { method:'PUT', body });
    closeModal();
    await loadState();
  }catch(e){ err.textContent = e.message; err.style.display='block'; }
}

async function deleteSection(id){
  const sec = state.sections.find(x=>x.id===id);
  const label = sec && sec.type==='gam3eya' ? 'كل الجمعيات اللي جواه' : 'كل الأفراد اللي جواه';
  if(!confirm(`هل تريد حذف هذا التبويب؟ هيتحذف معاه ${label}.`)) return;
  try{
    await api('sections', { method:'DELETE', qs:`&id=${id}` });
    closeModal();
    await loadState();
  }catch(e){ alert(e.message); }
}

function openGamForm(){
  const sec = currentSection();
  const label = esc(sec.name);
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>إضافة ${label} جديد</h3>
      <div class="field"><label>الاسم</label><input id="gName" placeholder="مثال: ${label} 1"></div>
      <div class="field-row">
        <div class="field"><label>تاريخ البداية</label><input id="gStart" type="date"></div>
        <div class="field"><label>عدد الشهور</label><input id="gMonths" type="number" min="1" value="12"></div>
      </div>
      <div class="field-row">
        <div class="field"><label>مبلغ القسط الشهري</label><input id="gAmount" type="number" min="0" step="0.01"></div>
        <div class="field"><label>العملة</label>
          <select id="gCurrency"><option value="EGP">جنيه مصري (ج.م)</option><option value="USD">دولار ($)</option></select>
        </div>
      </div>
      <div class="field" id="gMyTurnField" style="display:${sec.has_turns==1?'':'none'};"><label>أدوارك (أرقام الشهور مفصولة بفاصلة - اختياري)</label><input id="gMyTurn" placeholder="مثال: 3, 7, 10"></div>
      <div class="error-msg" id="gErr"></div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()">إلغاء</button>
        <button class="primary" onclick="saveGamForm()">حفظ</button>
      </div>
    </div>
  </div>`;
}

async function saveGamForm(){
  const name = document.getElementById('gName').value.trim();
  const startDate = document.getElementById('gStart').value;
  const months = parseInt(document.getElementById('gMonths').value);
  const monthlyAmount = parseFloat(document.getElementById('gAmount').value);
  const currency = document.getElementById('gCurrency').value;
  const myTurnRaw = document.getElementById('gMyTurn').value;
  const myTurnMonths = myTurnRaw
    ? myTurnRaw.split(',').map(x=>parseInt(x.trim())).filter(n=>!isNaN(n))
    : [];
  const err = document.getElementById('gErr');
  if(!name || !startDate || !months || months<1 || !monthlyAmount || monthlyAmount<=0){
    err.textContent = 'من فضلك أكمل جميع الحقول بشكل صحيح';
    err.style.display='block';
    return;
  }
  if(myTurnMonths.some(m => m < 1 || m > months)){
    err.textContent = `كل رقم شهر لازم يكون بين 1 و ${months}`;
    err.style.display='block';
    return;
  }
  try{
    await api('gam3eyas', { method:'POST', body:{ sectionId: activeSectionId, name, startDate, months, monthlyAmount, currency, myTurnMonths } });
    closeModal();
    await loadState();
  }catch(e){ err.textContent = e.message; err.style.display='block'; }
}

async function toggleGamPaid(scheduleId){
  await api('toggle_paid', { method:'POST', body:{ id: scheduleId } });
  await loadState();
}

async function setMyTurns(gam3eyaId, months){
  await api('set_my_turns', { method:'POST', body:{ gam3eyaId, months } });
  await loadState();
}

function openMyTurnForm(gam3eyaId){
  const g = state.gam3eyas.find(x=>x.id===gam3eyaId);
  const checkboxes = Array.from({length: g.months}, (_, i) => i + 1).map(m => `
    <label style="display:flex;align-items:center;gap:8px;padding:6px 0;border-bottom:1px solid var(--paper-2);font-size:14px;">
      <input type="checkbox" class="turn-check" value="${m}" ${g.my_turns.includes(m) ? 'checked' : ''}>
      شهر ${m}
    </label>
  `).join('');
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>تحديد أدوارك</h3>
      <p style="font-size:12px;color:#6b6248;margin-bottom:8px;">اختار كل الشهور اللي هي دورك في الجمعية دي (تقدر تختار أكتر من واحد)</p>
      <div style="max-height:280px;overflow-y:auto;">${checkboxes}</div>
      <div class="error-msg" id="turnErr"></div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()">إلغاء</button>
        <button class="primary" onclick="saveMyTurnForm(${gam3eyaId})">حفظ</button>
      </div>
    </div>
  </div>`;
}

async function saveMyTurnForm(gam3eyaId){
  const months = Array.from(document.querySelectorAll('.turn-check:checked')).map(el => parseInt(el.value));
  await setMyTurns(gam3eyaId, months);
  closeModal();
}

async function deleteGam(id){
  if(!confirm('هل تريد حذف هذه الجمعية؟')) return;
  await api('gam3eyas', { method:'DELETE', qs:`&id=${id}` });
  await loadState();
}

function openGamRename(id){
  const g = state.gam3eyas.find(x=>x.id===id);
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>تعديل اسم الجمعية</h3>
      <div class="field"><label>اسم الجمعية</label><input id="gRenameInput" value="${esc(g.name)}"></div>
      <div class="error-msg" id="gRenameErr"></div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()">إلغاء</button>
        <button class="primary" onclick="saveGamRename(${id})">حفظ</button>
      </div>
    </div>
  </div>`;
}

async function saveGamRename(id){
  const name = document.getElementById('gRenameInput').value.trim();
  const err = document.getElementById('gRenameErr');
  if(!name){ err.textContent='الاسم مطلوب'; err.style.display='block'; return; }
  try{
    await api('gam3eyas', { method:'PUT', body:{ id, name } });
    closeModal();
    await loadState();
  }catch(e){ err.textContent = e.message; err.style.display='block'; }
}

function openIndForm(){
  const sec = currentSection();
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>إضافة ${esc(sec.name)} جديد</h3>
      <div class="field"><label>الاسم</label><input id="pName" placeholder="اسم الشخص"></div>
      <div class="field-row">
        <div class="field"><label>رقم الهاتف (واتساب)</label><input id="pPhone" placeholder="مثال: 201001234567"></div>
        <div class="field"><label>العملة</label>
          <select id="pCurrency"><option value="EGP">جنيه مصري (ج.م)</option><option value="USD">دولار ($)</option></select>
        </div>
      </div>
      <div class="error-msg" id="pErr"></div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()">إلغاء</button>
        <button class="primary" onclick="saveIndForm()">حفظ</button>
      </div>
    </div>
  </div>`;
}

async function saveIndForm(){
  const name = document.getElementById('pName').value.trim();
  const phone = document.getElementById('pPhone').value.trim();
  const currency = document.getElementById('pCurrency').value;
  const err = document.getElementById('pErr');
  if(!name){
    err.textContent = 'من فضلك أدخل اسم الفرد';
    err.style.display='block';
    return;
  }
  try{
    await api('individuals', { method:'POST', body:{ sectionId: activeSectionId, name, phone, currency } });
    closeModal();
    await loadState();
  }catch(e){ err.textContent = e.message; err.style.display='block'; }
}

async function deleteInd(id){
  if(!confirm('هل تريد حذف هذا الفرد وكل حساباته؟')) return;
  await api('individuals', { method:'DELETE', qs:`&id=${id}` });
  await loadState();
}

function openIndEdit(id){
  const p = state.individuals.find(x=>x.id===id);
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>تعديل بيانات الفرد</h3>
      <div class="field"><label>الاسم</label><input id="pEditName" value="${esc(p.name)}"></div>
      <div class="field-row">
        <div class="field"><label>رقم الهاتف (واتساب)</label><input id="pEditPhone" value="${esc(p.phone||'')}"></div>
        <div class="field"><label>العملة</label>
          <select id="pEditCurrency">
            <option value="EGP" ${p.currency==='EGP'?'selected':''}>جنيه مصري (ج.م)</option>
            <option value="USD" ${p.currency==='USD'?'selected':''}>دولار ($)</option>
          </select>
        </div>
      </div>
      <div class="error-msg" id="pEditErr"></div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()">إلغاء</button>
        <button class="primary" onclick="saveIndEdit(${id})">حفظ</button>
      </div>
    </div>
  </div>`;
}

async function saveIndEdit(id){
  const name = document.getElementById('pEditName').value.trim();
  const phone = document.getElementById('pEditPhone').value.trim();
  const currency = document.getElementById('pEditCurrency').value;
  const err = document.getElementById('pEditErr');
  if(!name){ err.textContent='الاسم مطلوب'; err.style.display='block'; return; }
  try{
    await api('individuals', { method:'PUT', body:{ id, name, phone, currency } });
    closeModal();
    await loadState();
  }catch(e){ err.textContent = e.message; err.style.display='block'; }
}

function openEntryForm(pid, entryId){
  const editing = entryId !== undefined;
  const p = state.individuals.find(x=>x.id===pid);
  const e = editing ? p.entries.find(x=>x.id===entryId) : null;
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet">
      <h3>${editing?'تعديل الحركة':'إضافة حركة على الحساب'}</h3>
      <div class="field"><label>الوصف</label><input id="eNote" placeholder="مثال: دفعة شهر أغسطس" value="${editing?esc(e.note||''):''}"></div>
      <div class="field-row">
        <div class="field"><label>المبلغ</label><input id="eAmount" type="number" min="0" step="0.01" value="${editing?e.amount:''}"></div>
        <div class="field"><label>نوع الحركة</label>
          <select id="eType">
            <option value="debit" ${editing && e.type==='debit'?'selected':''}>مستحق (إضافة)</option>
            <option value="credit" ${editing && e.type==='credit'?'selected':''}>تم تحصيله (خصم)</option>
          </select>
        </div>
      </div>
      <div class="field"><label>التاريخ</label><input id="eDate" type="date" value="${editing?e.entry_date:new Date().toISOString().slice(0,10)}"></div>
      <div class="error-msg" id="eErr"></div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()">إلغاء</button>
        <button class="primary" onclick="saveEntry(${pid}${editing?','+entryId:''})">${editing?'حفظ التعديل':'حفظ الحركة'}</button>
      </div>
    </div>
  </div>`;
}

async function saveEntry(pid, entryId){
  const note = document.getElementById('eNote').value.trim();
  const amount = parseFloat(document.getElementById('eAmount').value);
  const type = document.getElementById('eType').value;
  const date = document.getElementById('eDate').value;
  const err = document.getElementById('eErr');
  if(!amount || amount<=0 || !date){
    err.textContent = 'من فضلك أدخل مبلغ صحيح وتاريخ';
    err.style.display='block';
    return;
  }
  try{
    if(entryId !== undefined){
      await api('entries', { method:'PUT', body:{ id: entryId, note, amount, type, date } });
    } else {
      await api('entries', { method:'POST', body:{ individualId: pid, note, amount, type, date } });
    }
    closeModal();
    await loadState();
  }catch(e){ err.textContent = e.message; err.style.display='block'; }
}

async function deleteEntry(entryId){
  await api('entries', { method:'DELETE', qs:`&id=${entryId}` });
  await loadState();
}

function openGamInvoice(gid){
  const g = state.gam3eyas.find(x=>x.id===gid);
  const total = g.schedule.reduce((a,s)=>a+Number(s.amount),0);
  const paid = g.schedule.filter(s=>Number(s.paid)===1).reduce((a,s)=>a+Number(s.amount),0);
  const rows = g.schedule.map(s=>`<tr><td>شهر ${s.month_idx} - ${formatDate(new Date(s.due_date))}</td><td>${fmt(s.amount)} ${CUR[g.currency]}</td><td>${Number(s.paid)===1?'مدفوع':'غير مدفوع'}</td></tr>`).join('');
  const invHtml = `
    <h4>فاتورة جمعية</h4>
    <p class="inv-sub">${esc(g.name)}</p>
    <table>
      <tr><th>البيان</th><th>المبلغ</th><th>الحالة</th></tr>
      ${rows}
      <tr class="total-row"><td colspan="2">الإجمالي</td><td>${fmt(total)} ${CUR[g.currency]}</td></tr>
      <tr class="total-row"><td colspan="2">المدفوع</td><td>${fmt(paid)} ${CUR[g.currency]}</td></tr>
    </table>
  `;
  showInvoiceModal(invHtml, '', g.name);
}

function openIndInvoice(pid){
  const p = state.individuals.find(x=>x.id===pid);
  const sorted = [...p.entries].sort((a,b)=> new Date(a.entry_date) - new Date(b.entry_date) || a.id - b.id);
  let balance = 0, sumDebit = 0, sumCredit = 0;
  const rows = sorted.map(e=>{
    const debit = e.type==='debit' ? Number(e.amount) : 0;
    const credit = e.type==='credit' ? Number(e.amount) : 0;
    balance += debit - credit;
    sumDebit += debit;
    sumCredit += credit;
    return `<tr>
      <td class="ledger-date">${formatDate(new Date(e.entry_date))}</td>
      <td class="ledger-details">${esc(e.note||'حركة')}</td>
      <td class="ledger-debit">${debit ? fmt(debit) : '0'}</td>
      <td class="ledger-credit">${credit ? fmt(credit) : '0'}</td>
      <td class="ledger-balance">${fmt(balance)}</td>
    </tr>`;
  }).join('');
  const sym = CUR[p.currency];
  const finalBalance = balance;
  const invHtml = `
    <h4>فاتورة حساب</h4>
    <p class="inv-sub">${esc(p.name)}</p>
    <table class="ledger-table">
      <tr><th>التاريخ</th><th>التفاصيل</th><th>عليه</th><th>له</th><th>الرصيد</th></tr>
      ${rows || '<tr><td colspan="5">لا توجد حركات</td></tr>'}
      <tr class="ledger-totals-row">
        <td colspan="2">إجمالي العمليات</td>
        <td class="ledger-debit">${fmt(sumDebit)}</td>
        <td class="ledger-credit">${fmt(sumCredit)}</td>
        <td></td>
      </tr>
      <tr class="ledger-final-row">
        <td colspan="4">الرصيد الإجمالي ${finalBalance>=0?'- عليه':'- له'}</td>
        <td>${sym} ${fmt(Math.abs(finalBalance))}</td>
      </tr>
    </table>
  `;
  showInvoiceModal(invHtml, p.phone, p.name, finalBalance, p.currency);
}

function showInvoiceModal(invHtml, phone, title, total, currency){
  document.getElementById('printArea').innerHTML = `<div class="invoice">${invHtml}</div>`;
  const waMsg = encodeURIComponent(`فاتورة ${title}${total!==undefined?` - الإجمالي: ${fmt(total)} ${CUR[currency]}`:''}\nمرفق تفاصيل الحساب.`);
  const cleanPhone = (phone||'').replace(/[^0-9]/g,'');
  const waLink = cleanPhone ? `https://wa.me/${cleanPhone}?text=${waMsg}` : `https://wa.me/?text=${waMsg}`;
  document.getElementById('modalRoot').innerHTML = `
  <div class="overlay" onclick="if(event.target===this) closeModal()">
    <div class="sheet invoice-wrap">
      <h3>معاينة الفاتورة</h3>
      <div class="invoice">${invHtml}</div>
      <p style="font-size:12px;color:#6b6248;margin-bottom:10px;">اضغط "طباعة / حفظ PDF" أولاً لحفظ الفاتورة، ثم أرسلها عبر واتساب وأرفقها يدويًا.</p>
      <div class="sheet-actions">
        <button class="ghost" onclick="window.print()">طباعة / حفظ PDF</button>
        <a href="${waLink}" target="_blank" style="flex:1;"><button class="primary" style="width:100%;">إرسال واتساب</button></a>
      </div>
      <div class="sheet-actions">
        <button class="ghost" onclick="closeModal()" style="width:100%;">إغلاق</button>
      </div>
    </div>
  </div>`;
}

loadState();
</script>
</body>
</html>

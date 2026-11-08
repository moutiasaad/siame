// Interactions : icônes, marche/arrêt, rafraîchissement en direct, graphiques.
lucide.createIcons();

const csrf = document.querySelector('meta[name="csrf"]')?.content;
const api = (path) => new URL(`api/${path}`, document.querySelector('link[href$="app.css"]').href.replace('assets/css/app.css', '')).href;
const fmt = (v, digits = 1) => (v === null ? '—' : v.toFixed(digits).replace('.', ','));

// --- Marche / arrêt --------------------------------------------------------
document.querySelectorAll('[data-toggle]').forEach((input) => {
  input.addEventListener('change', async () => {
    const state = input.checked ? 'on' : 'off';
    const body = new URLSearchParams({ id: input.dataset.toggle, state, csrf });
    const res = await fetch(api('toggle.php'), { method: 'POST', body });
    if (!res.ok) {
      input.checked = !input.checked;
      alert('La commande n’a pas pu être envoyée.');
      return;
    }
    const label = input.closest('.switch').querySelector('[data-field="state-label"]');
    label.textContent = input.checked ? 'Marche' : 'Arrêt';
  });
});

// --- Rafraîchissement toutes les 10 s ---------------------------------------
async function refresh() {
  const cards = document.querySelectorAll('[data-machine]');
  if (!cards.length) return;
  const res = await fetch(api('machines.php'));
  if (!res.ok) return;
  for (const m of await res.json()) {
    const card = document.querySelector(`[data-machine="${m.id}"]`);
    if (!card) continue;
    const online = card.querySelector('[data-field="online"]');
    online.className = `pill ${m.online ? 'ok' : 'off'}`;
    online.textContent = m.online ? 'En ligne' : 'Hors ligne';
    const t = card.querySelector('[data-field="temperature"]');
    t.className = `r-value t-${m.level}`;
    t.innerHTML = `${fmt(m.temperature)}<small> °C</small>`;
    card.querySelector('[data-field="humidity"]').innerHTML = `${fmt(m.humidity, 0)}<small> %</small>`;
    card.querySelector('[data-field="sync"]').textContent = m.sync;
  }
}
setInterval(refresh, 10000);

// --- Graphiques -------------------------------------------------------------
const blue = '#1d6fd8';
async function history(id, hours) {
  const res = await fetch(api(`history.php?id=${id}&hours=${hours}`));
  return res.ok ? res.json() : [];
}

document.querySelectorAll('canvas[data-spark]').forEach(async (canvas) => {
  const data = await history(canvas.dataset.spark, 6);
  new Chart(canvas, {
    type: 'line',
    data: { labels: data.map((d) => d.t), datasets: [{ data: data.map((d) => d.v), borderColor: blue, borderWidth: 2, pointRadius: 0, tension: 0.35, fill: true, backgroundColor: 'rgba(29,111,216,.08)' }] },
    options: { responsive: true, maintainAspectRatio: false, animation: false, plugins: { legend: { display: false }, tooltip: { enabled: false } }, scales: { x: { display: false }, y: { display: false } } },
  });
});

document.querySelectorAll('canvas[data-history]').forEach(async (canvas) => {
  const data = await history(canvas.dataset.history, 24);
  const max = Number(canvas.dataset.max);
  new Chart(canvas, {
    type: 'line',
    data: {
      labels: data.map((d) => d.t.slice(11)),
      datasets: [
        { label: 'Température (°C)', data: data.map((d) => d.v), borderColor: blue, borderWidth: 2.5, pointRadius: 0, tension: 0.3, fill: true, backgroundColor: 'rgba(29,111,216,.10)' },
        { label: `Seuil d’alarme (${max} °C)`, data: data.map(() => max), borderColor: '#dc2626', borderDash: [6, 6], borderWidth: 1.5, pointRadius: 0 },
      ],
    },
    options: {
      responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
      scales: { x: { ticks: { maxTicksLimit: 8 }, grid: { display: false } }, y: { suggestedMin: 20, suggestedMax: max + 5, grid: { color: '#eef2f7' } } },
    },
  });
});

<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * PALADIN PROFILE v5 — Public Character Armory View
 * LUMINOUS Engine · Repo-Wide Unique File: p/p_index.php
 *
 * Route:  /p/{slug}
 * Access: root .htaccess rewrites /p/SLUG → /p/p_index.php?slug=SLUG
 *
 * WoW Armory-inspired read-only character profile page.
 * Loads character data via JS fetch to /api/api_index.php
 * ═══════════════════════════════════════════════════════════════
 */
declare(strict_types=1);

// Sanitize slug from query string (set by root .htaccess rewrite)
$slug = preg_replace('/[^a-z0-9]/i', '', $_GET['slug'] ?? '');

if (empty($slug)) {
    header('Location: /');
    exit;
}

// Page title will be updated by JS once character loads
$pageTitle   = 'Paladin Profile — LUMINOUS Engine';
$pageDesc    = 'A public Paladin profile on the LUMINOUS Engine.';
$canonicalUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'pally-power.goldhatconsulting.com') . '/p/' . $slug;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title id="pageTitle"><?= htmlspecialchars($pageTitle) ?></title>
<meta name="description" id="pageDesc" content="<?= htmlspecialchars($pageDesc) ?>">
<link rel="canonical" href="<?= htmlspecialchars($canonicalUrl) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Cinzel:wght@400;500;600;700&family=EB+Garamond:ital,wght@0,400;0,500;1,400&family=Source+Sans+3:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/public/css/paladin.css">
<style>
/* ── Armory Layout ── */
.armory-wrap {
  max-width: 1100px;
  margin: 0 auto;
  padding: clamp(2rem,5vw,4rem) clamp(1rem,4vw,3rem);
  margin-top: 62px;
}
.armory-loading {
  text-align: center;
  padding: 6rem 2rem;
  font-family: var(--font-sans);
  color: var(--gold);
  font-size: 1.1rem;
  letter-spacing: .1em;
}
.armory-loading::after {
  content: '';
  display: block;
  width: 40px;
  height: 40px;
  border: 3px solid rgba(200,150,26,.2);
  border-top-color: var(--gold);
  border-radius: 50%;
  margin: 1.5rem auto 0;
  animation: spin 1s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
.armory-error { text-align: center; padding: 6rem 2rem; }
.armory-error h2 { font-family: var(--font-display); color: var(--crimson); margin-bottom: 1rem; }

/* ── Banner ── */
.armory-banner {
  position: relative;
  overflow: hidden;
  border-radius: var(--r);
  margin-bottom: 2.5rem;
  background: var(--iron);
  border: 1px solid rgba(200,150,26,.2);
}
.armory-banner-color { position: absolute; top: 0; left: 0; right: 0; height: 6px; }
.armory-banner-inner {
  padding: 2.5rem 2.5rem 2rem;
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 2rem;
  align-items: center;
}
.armory-order-sigil {
  width: 72px; height: 72px;
  border-radius: 50%;
  border: 3px solid;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-size: 1.5rem;
  font-weight: 900;
  color: white;
  flex-shrink: 0;
  text-shadow: 0 1px 4px rgba(0,0,0,.4);
}
.armory-name {
  font-family: var(--font-display);
  font-size: clamp(1.5rem,4vw,2.4rem);
  color: var(--gold-bright);
  text-shadow: 0 2px 20px rgba(232,184,48,.2);
  margin-bottom: .2rem;
  line-height: 1.1;
}
.armory-title { font-family: var(--font-serif); font-size: 1.1rem; color: rgba(248,243,232,.55); margin-bottom: .75rem; font-style: italic; }
.armory-badges { display: flex; flex-wrap: wrap; gap: .5rem; }
.armory-badge {
  font-family: var(--font-sans); font-size: .68rem; font-weight: 700;
  letter-spacing: .1em; text-transform: uppercase;
  padding: .2rem .7rem; border-radius: 3px; border: 1px solid;
}
.armory-badge-order { color: white; }
.armory-badge-align { border-color: rgba(200,150,26,.4); color: var(--gold); background: rgba(200,150,26,.08); }
.armory-level-display { text-align: right; flex-shrink: 0; }
.armory-level-num { font-family: var(--font-display); font-size: 3rem; color: var(--gold-bright); line-height: 1; display: block; }
.armory-level-label { font-family: var(--font-sans); font-size: .62rem; letter-spacing: .2em; text-transform: uppercase; color: rgba(200,196,188,.4); }

/* ── Grid ── */
.armory-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
.armory-grid.full { grid-template-columns: 1fr; }
.armory-panel {
  background: rgba(255,255,255,.45);
  border: 1px solid rgba(200,150,26,.15);
  border-radius: var(--r);
  padding: 1.5rem; overflow: hidden;
}
.armory-panel-dark { background: var(--iron); border-color: rgba(200,150,26,.2); }
.armory-panel h3 {
  font-family: var(--font-sans); font-size: .82rem; font-weight: 700;
  letter-spacing: .12em; text-transform: uppercase; color: var(--gold-deep);
  margin-bottom: 1rem; padding-bottom: .5rem;
  border-bottom: 1px solid rgba(200,150,26,.15);
}
.armory-panel-dark h3 { color: var(--gold); border-bottom-color: rgba(200,150,26,.25); }

/* ── Oath ── */
.armory-oath { font-family: var(--font-body); font-size: 1.1rem; font-style: italic; color: var(--iron); line-height: 1.85; }
.armory-oath-source { font-family: var(--font-sans); font-size: .75rem; color: var(--gold-deep); font-weight: 600; letter-spacing: .06em; text-transform: uppercase; margin-top: .5rem; }

/* ── Stat rows ── */
.armory-stat-row {
  display: flex; justify-content: space-between; align-items: center;
  padding: .45rem 0; border-bottom: 1px solid rgba(200,150,26,.06);
  font-family: var(--font-sans); font-size: .82rem;
}
.armory-stat-row:last-child { border-bottom: none; }
.armory-stat-key { color: rgba(30,28,24,.55); }
.armory-stat-val { font-weight: 600; color: var(--iron); }
.armory-stat-val.gold { color: var(--gold-deep); }

/* ── Attribute bars ── */
.attr-bar-row { margin-bottom: .75rem; }
.attr-bar-header { display: flex; justify-content: space-between; font-family: var(--font-sans); font-size: .75rem; margin-bottom: .3rem; }
.attr-bar-name { color: var(--iron); font-weight: 600; }
.attr-bar-val { font-family: var(--font-display); font-size: .85rem; color: var(--gold-deep); }
.attr-bar-track { height: 6px; background: rgba(200,150,26,.12); border-radius: 3px; overflow: hidden; }
.attr-bar-fill { height: 100%; background: linear-gradient(90deg, var(--gold-deep), var(--gold-bright)); border-radius: 3px; transition: width .6s ease; }

/* ── Talents ── */
.talent-row { display: flex; align-items: center; gap: 1rem; margin-bottom: .85rem; }
.talent-icon-lg { font-size: 1.4rem; flex-shrink: 0; }
.talent-info { flex: 1; }
.talent-tree-name { font-family: var(--font-serif); font-size: .88rem; font-weight: 700; color: var(--silver-light); display: block; margin-bottom: .3rem; }
.talent-pts-track { height: 5px; background: rgba(200,196,188,.1); border-radius: 3px; overflow: hidden; }
.talent-pts-fill { height: 100%; background: linear-gradient(90deg, rgba(200,150,26,.5), var(--gold-bright)); border-radius: 3px; }
.talent-pts-label { font-family: var(--font-mono); font-size: .65rem; color: var(--gold); margin-top: .2rem; display: block; }

/* ── Feats ── */
.armory-feat { display: flex; gap: .75rem; padding: .6rem 0; border-bottom: 1px solid rgba(200,150,26,.08); }
.armory-feat:last-child { border-bottom: none; }
.feat-lv-badge { font-family: var(--font-mono); font-size: .65rem; background: rgba(200,150,26,.15); color: var(--gold); padding: .15rem .4rem; border-radius: 3px; white-space: nowrap; align-self: flex-start; margin-top: .15rem; }
.feat-info-name { font-family: var(--font-serif); font-size: .88rem; font-weight: 700; color: var(--iron); display: block; margin-bottom: .15rem; }
.feat-info-desc { font-family: var(--font-sans); font-size: .75rem; color: #5a5040; line-height: 1.5; }

/* ── Journal ── */
.journal-entry { padding: .85rem 0; border-bottom: 1px solid rgba(200,150,26,.1); }
.journal-entry:last-child { border-bottom: none; }
.journal-level { font-family: var(--font-display); font-size: .78rem; color: var(--gold); margin-bottom: .3rem; display: flex; align-items: center; gap: .5rem; }
.journal-text { font-family: var(--font-body); font-size: .95rem; color: var(--silver-light); line-height: 1.75; font-style: italic; }
.journal-meta { font-family: var(--font-sans); font-size: .72rem; color: rgba(200,196,188,.4); margin-top: .3rem; }

/* ── Share bar ── */
.armory-share {
  display: flex; align-items: center; gap: 1rem;
  background: rgba(200,150,26,.06); border: 1px solid rgba(200,150,26,.2);
  border-radius: var(--r); padding: .85rem 1.25rem; margin-bottom: 2rem; flex-wrap: wrap;
}
.armory-share-url { font-family: var(--font-mono); font-size: .78rem; color: var(--gold-deep); flex: 1; min-width: 200px; }
.copy-btn { font-family: var(--font-sans); font-size: .72rem; font-weight: 700; padding: .35rem .85rem; border-radius: 4px; background: var(--gold); color: var(--ink); border: none; cursor: pointer; transition: background .2s; }
.copy-btn:hover { background: var(--gold-bright); }

@media(max-width:700px) {
  .armory-banner-inner { grid-template-columns: auto 1fr; }
  .armory-level-display { grid-column: 1/-1; text-align: left; }
  .armory-grid { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<header class="site-header"><div class="header-inner">
  <a class="logo-wrap" href="/"><div class="logo-text">Paladin Profile<span>LUMINOUS Engine v5 · Rose Ministries</span></div></a>
  <nav class="main-nav" id="mainNav">
    <a href="/" style="color:var(--gold-bright)">♪ Heart Song</a>
    <a href="/orders/">Orders</a>
    <a href="/">Builder</a>
    <a href="/">Gallery</a>
  </nav>
  <button class="mobile-toggle" id="mobileToggle" aria-label="Menu"><span></span><span></span><span></span></button>
</div></header>

<div class="armory-wrap">
  <div id="armoryContent">
    <div class="armory-loading">Loading Paladin...</div>
  </div>
</div>

<div class="closing-section">
  <h2>Per Silvam et Iter</h2>
  <p><em>"Through the forest and the journey."</em></p>
  <div style="width:80px;height:1px;background:rgba(200,150,26,.5);margin:2rem auto"></div>
  <p>This is a dream machine. You define the dream. We help you track it.</p>
  <div class="motto-final">GOOD GUYS, ON ME.</div>
</div>

<footer class="site-footer"><div class="footer-inner">
  <div class="footer-top">
    <div class="footer-brand"><div class="footer-brand-name">Gold Hat <span>Consulting</span></div><p>LUMINOUS Game Engine v5 · Paladin Profile · Rose Ministries ordained. GoldHat™ &amp; ArchDaemon™ property network.</p></div>
    <div class="footer-links"><h4>Properties</h4><ul><li><a href="https://goldhatconsulting.com">GoldHat Home</a></li><li><a href="https://therealpreacher.com">The Real Preacher</a></li><li><a href="https://beginaministry.com/ministershop/">Rose Ministries</a></li></ul></div>
    <div class="footer-links"><h4>Engine</h4><ul><li><a href="/">Character Builder</a></li><li><a href="/orders/">Orders Manual</a></li><li><a href="/">Public Gallery</a></li></ul></div>
  </div>
  <div class="footer-bottom"><p>&copy; 2026 David William Sylvester. All rights reserved.</p><div class="footer-legal">GoldHat™ 98925168 · ArchDaemon™ 98940257 · LUMINOUS Engine v5.0</div></div>
</div></footer>

<script>
// ═══════════════════════════════════════════════════════════════
// PALADIN PROFILE v5 — Armory Client (p/p_index.php)
// Slug injected server-side via PHP; API call done client-side.
// ═══════════════════════════════════════════════════════════════
const SLUG = <?= json_encode($slug) ?>;
const API_BASE = '/api/';

const ORDER_COLORS = {
  white:'#8b7840', yellow:'#c9a922', green:'#1a6b3c', blue:'#1a5276',
  pink:'#c44d8e', orange:'#c45e1a', red:'#a81c1c', purple:'#5b2c8e',
  brown:'#6b4226', black:'#3a3a6e'
};

function orderInitial(name) {
  if (!name) return '?';
  return name.replace(/Order of the /i,'').trim().charAt(0).toUpperCase();
}

function pct(val, max) { return Math.min(100, Math.round((val / max) * 100)); }

async function loadCharacter() {
  try {
    const res = await fetch(`${API_BASE}?action=character&slug=${encodeURIComponent(SLUG)}`);
    const data = await res.json();
    if (data.error || !data.character) {
      showError(data.message || 'Character not found or not public.');
      return;
    }
    renderArmory(data.character);
  } catch(e) {
    showError('Could not connect to the LUMINOUS Engine. Try again shortly.');
  }
}

function showError(msg) {
  document.getElementById('armoryContent').innerHTML = `
    <div class="armory-error">
      <h2>⚔ Not Found</h2>
      <p style="font-family:var(--font-body);font-style:italic;color:#5a5040;margin-bottom:2rem">${msg}</p>
      <a href="/" class="btn btn-next" style="text-decoration:none;padding:.75rem 2rem">← Return to Builder</a>
    </div>`;
}

function renderArmory(c) {
  document.title = `${c.name} — Paladin Profile · LUMINOUS Engine`;
  document.getElementById('pageDesc').content = `${c.name}'s public Paladin profile. ${c.oath_statement || ''}`;

  const orderColor = ORDER_COLORS[c.order_id] || '#c8961a';
  const orderName  = c.order_name || (c.order_id ? `Order of the ${c.order_id.charAt(0).toUpperCase()+c.order_id.slice(1)}` : 'Unsworn');
  const alignName  = c.alignment_name || c.alignment_id || '—';
  const callingName = c.calling_custom || c.calling_name || '—';
  const specName    = c.specialization_custom || c.specialization_name || '—';

  // Resolve build_data — API v5 nulls build_json and provides build_data object
  let buildData = {};
  if (c.build_data && typeof c.build_data === 'object') buildData = c.build_data;

  // Attributes
  const attrs = c.attribute_values || [];
  const attrHTML = attrs.length
    ? attrs.map(a => {
        const val = a.current_value || a.base_value || 8;
        return `<div class="attr-bar-row">
          <div class="attr-bar-header">
            <span class="attr-bar-name">${a.name}</span>
            <span class="attr-bar-val">${val}</span>
          </div>
          <div class="attr-bar-track"><div class="attr-bar-fill" style="width:${pct(val,20)}%"></div></div>
        </div>`;
      }).join('')
    : '<p style="font-family:var(--font-sans);font-size:.82rem;color:#7a6850">No attributes recorded.</p>';

  // Talents
  const talents = c.talent_values || [];
  const talentHTML = talents.length
    ? talents.map(t => `<div class="talent-row">
        <span class="talent-icon-lg">${t.icon || '⚔'}</span>
        <div class="talent-info">
          <span class="talent-tree-name">${t.name}</span>
          <div class="talent-pts-track"><div class="talent-pts-fill" style="width:${pct(t.points_allocated||0,15)}%"></div></div>
          <span class="talent-pts-label">${t.points_allocated || 0} / 15 pts</span>
        </div>
      </div>`).join('')
    : '<p style="font-family:var(--font-sans);font-size:.82rem;color:rgba(200,196,188,.4)">No talent allocations recorded.</p>';

  // Feats
  const feats = c.feat_progress || [];
  const featHTML = feats.length
    ? feats.map(f => `<div class="armory-feat">
        <span class="feat-lv-badge">Lv.${f.level_req}</span>
        <div><span class="feat-info-name">${f.name}</span><span class="feat-info-desc">${f.description}</span></div>
      </div>`).join('')
    : '<p style="font-family:var(--font-sans);font-size:.82rem;color:#7a6850">No feats unlocked yet.</p>';

  // Journal
  const journal = c.journal || [];
  const journalHTML = journal.length
    ? journal.map(j => `<div class="journal-entry">
        <div class="journal-level">
          <span>Level ${j.from_level} → ${j.to_level}</span>
          ${j.mentor_name ? `<span style="font-family:var(--font-sans);font-size:.7rem;color:rgba(200,196,188,.5)">· Mentored by ${j.mentor_name}</span>` : ''}
        </div>
        <div class="journal-text">${j.journal_entry}</div>
        ${j.evidence_notes ? `<div class="journal-meta">Evidence: ${j.evidence_notes}</div>` : ''}
      </div>`).join('')
    : '<p style="font-family:var(--font-body);font-size:.9rem;font-style:italic;color:rgba(200,196,188,.4)">No level-up journal entries yet.</p>';

  const shareUrl = window.location.href;

  document.getElementById('armoryContent').innerHTML = `
    <div class="armory-share">
      <span style="font-family:var(--font-sans);font-size:.72rem;font-weight:700;color:var(--gold-deep);letter-spacing:.08em;text-transform:uppercase">⚔ Public Profile</span>
      <span class="armory-share-url">${shareUrl}</span>
      <button class="copy-btn" onclick="copyShare()">Copy Link</button>
      <a href="/" class="btn btn-back" style="font-size:.72rem;padding:.35rem .85rem;text-decoration:none">← Builder</a>
    </div>

    <div class="armory-banner">
      <div class="armory-banner-color" style="background:${orderColor}"></div>
      <div class="armory-banner-inner">
        <div class="armory-order-sigil" style="background:${orderColor};border-color:${orderColor}">${orderInitial(orderName)}</div>
        <div>
          <div class="armory-name">${c.name}</div>
          ${c.title ? `<div class="armory-title">${c.title}</div>` : ''}
          <div class="armory-badges">
            <span class="armory-badge armory-badge-order" style="background:${orderColor};border-color:${orderColor}">${orderName}</span>
            ${c.alignment_id ? `<span class="armory-badge armory-badge-align">${alignName}</span>` : ''}
            ${callingName !== '—' ? `<span class="armory-badge" style="border-color:var(--forest);color:var(--forest-light);background:rgba(26,61,30,.06)">${callingName}</span>` : ''}
          </div>
        </div>
        <div class="armory-level-display">
          <span class="armory-level-num">${c.current_level || 1}</span>
          <span class="armory-level-label">Level</span>
        </div>
      </div>
    </div>

    <div class="armory-grid">
      <div class="armory-panel">
        <h3>The Oath</h3>
        ${c.oath_statement
          ? `<div class="armory-oath">"${c.oath_statement}"</div>
             ${c.oath_source ? `<div class="armory-oath-source">Source: ${c.oath_source}</div>` : ''}`
          : `<p style="font-family:var(--font-body);font-style:italic;color:#7a6850">No oath statement recorded.</p>`}
        ${c.oath_name ? `<div style="margin-top:.75rem;font-family:var(--font-serif);font-size:.88rem;font-weight:700;color:var(--forest)">${c.oath_name}</div>` : ''}
      </div>
      <div class="armory-panel">
        <h3>Profile</h3>
        <div class="armory-stat-row"><span class="armory-stat-key">Order</span><span class="armory-stat-val" style="color:${orderColor}">${orderName}</span></div>
        <div class="armory-stat-row"><span class="armory-stat-key">Alignment</span><span class="armory-stat-val">${alignName}</span></div>
        <div class="armory-stat-row"><span class="armory-stat-key">Calling</span><span class="armory-stat-val">${callingName}</span></div>
        <div class="armory-stat-row"><span class="armory-stat-key">Specialization</span><span class="armory-stat-val">${specName}</span></div>
        <div class="armory-stat-row"><span class="armory-stat-key">Level</span><span class="armory-stat-val gold">${c.current_level || 1} / 50</span></div>
        <div class="armory-stat-row"><span class="armory-stat-key">Profile Created</span><span class="armory-stat-val">${new Date(c.created_at).toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'})}</span></div>
      </div>
    </div>

    <div class="armory-grid">
      <div class="armory-panel"><h3>Attributes</h3>${attrHTML}</div>
      <div class="armory-panel armory-panel-dark"><h3>Talent Trees</h3>${talentHTML}</div>
    </div>

    ${feats.length ? `<div class="armory-grid full" style="margin-bottom:1.5rem"><div class="armory-panel"><h3>Feats Earned</h3>${featHTML}</div></div>` : ''}
    ${journal.length ? `<div class="armory-grid full"><div class="armory-panel armory-panel-dark"><h3>Level-Up Journal</h3>${journalHTML}</div></div>` : ''}
  `;
}

function copyShare() {
  navigator.clipboard.writeText(window.location.href).then(() => {
    const btn = document.querySelector('.copy-btn');
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy Link', 2000);
  });
}

const tog=document.getElementById('mobileToggle'),nav=document.getElementById('mainNav');
if(tog&&nav){tog.addEventListener('click',()=>nav.classList.toggle('open'));}

loadCharacter();
</script>
</body>
</html>

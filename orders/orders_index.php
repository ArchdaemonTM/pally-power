<?php
/**
 * PALADIN PROFILE v5 — Orders Manual
 * LUMINOUS Engine · Repo-Wide Unique File: orders/orders_index.php
 * Route: /orders/ → served by root .htaccess rewrite
 */
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>The Color Orders — Paladin Profile · LUMINOUS Engine</title>
<meta name="description" content="The ten Color Orders of the Paladin Profile system. Every Paladin belongs to an Order. Your Order defines your philosophy, your callings, and your spectrum of power.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Cinzel:wght@400;500;600;700;900&family=EB+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Source+Sans+3:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/public/css/paladin.css">
<style>
/* ── Orders Page Specific ── */
.orders-hero {
  margin-top: 62px;
  background: var(--iron);
  background-image:
    radial-gradient(ellipse at 50% -10%, rgba(200,150,26,.22) 0%, transparent 60%),
    radial-gradient(ellipse at 0% 80%, rgba(139,20,20,.1) 0%, transparent 50%),
    radial-gradient(ellipse at 100% 20%, rgba(91,44,142,.15) 0%, transparent 50%);
  border-bottom: 3px solid var(--gold);
  padding: clamp(3rem,8vw,5.5rem) clamp(1.5rem,5vw,4rem);
  text-align: center;
}
.orders-hero-title {
  font-family: var(--font-display);
  font-size: clamp(1.8rem,4.5vw,3.2rem);
  color: var(--gold-bright);
  text-shadow: 0 2px 40px rgba(232,184,48,.25);
  margin-bottom: .5rem;
}
.orders-hero-sub {
  font-family: var(--font-serif);
  font-size: clamp(.95rem,2vw,1.3rem);
  color: rgba(248,243,232,.5);
  letter-spacing: .06em;
  margin-bottom: 2rem;
}
.orders-hero-lore {
  max-width: 780px;
  margin: 0 auto 2.5rem;
  font-family: var(--font-body);
  font-size: 1.05rem;
  color: rgba(248,243,232,.72);
  line-height: 1.9;
  font-style: italic;
}
.spectrum-bar-full {
  display: flex;
  height: 8px;
  border-radius: 4px;
  overflow: hidden;
  max-width: 700px;
  margin: 0 auto 2rem;
  box-shadow: 0 0 24px rgba(200,150,26,.2);
}
.spectrum-bar-full span {
  flex: 1;
}

/* ── Jump Nav ── */
.jump-nav {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
  justify-content: center;
  max-width: 900px;
  margin: 0 auto;
  padding: 0 1rem;
}
.jump-btn {
  font-family: var(--font-sans);
  font-size: .72rem;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  padding: .4rem 1rem;
  border-radius: 4px;
  border: 1px solid;
  cursor: pointer;
  background: transparent;
  transition: all .2s;
  text-decoration: none;
  display: inline-block;
}
.jump-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,.2);
}

/* ── Page layout ── */
.orders-wrap {
  max-width: 1100px;
  margin: 0 auto;
  padding: clamp(2rem,5vw,4rem) clamp(1rem,4vw,3rem);
}

/* ── System Intro Box ── */
.system-intro {
  background: var(--iron);
  border: 1px solid rgba(200,150,26,.3);
  border-radius: var(--r);
  padding: 2.5rem 3rem;
  margin-bottom: 4rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
}
.system-intro h2 {
  font-family: var(--font-display);
  font-size: 1.3rem;
  color: var(--gold-bright);
  margin-bottom: 1.25rem;
  grid-column: 1/-1;
  text-align: center;
}
.system-rule {
  border-left: 3px solid rgba(200,150,26,.4);
  padding-left: 1.25rem;
}
.system-rule h4 {
  font-family: var(--font-serif);
  font-size: .9rem;
  color: var(--gold);
  margin-bottom: .5rem;
}
.system-rule p {
  font-family: var(--font-sans);
  font-size: .82rem;
  color: rgba(200,196,188,.65);
  line-height: 1.65;
  margin: 0;
}

/* ── Order Entry ── */
.order-entry {
  scroll-margin-top: 80px;
  margin-bottom: 5rem;
  border: 1px solid rgba(200,150,26,.12);
  border-radius: var(--r);
  overflow: hidden;
  background: rgba(255,255,255,.45);
}
.order-entry-header {
  display: flex;
  align-items: stretch;
  gap: 0;
  position: relative;
  overflow: hidden;
}
.order-entry-swatch {
  width: 12px;
  flex-shrink: 0;
}
.order-entry-titles {
  flex: 1;
  padding: 2rem 2.5rem 1.75rem;
}
.order-entry-num {
  font-family: var(--font-mono);
  font-size: .68rem;
  color: rgba(200,150,26,.45);
  letter-spacing: .2em;
  margin-bottom: .3rem;
}
.order-entry-name {
  font-family: var(--font-display);
  font-size: clamp(1.4rem,3vw,2rem);
  margin-bottom: .2rem;
  line-height: 1.1;
}
.order-entry-covenant {
  font-family: var(--font-serif);
  font-size: 1rem;
  color: var(--iron);
  opacity: .7;
  margin-bottom: .5rem;
}
.order-entry-spectrum-badge {
  display: inline-block;
  font-family: var(--font-sans);
  font-size: .66rem;
  font-weight: 700;
  letter-spacing: .14em;
  text-transform: uppercase;
  padding: .18rem .6rem;
  border-radius: 3px;
  border: 1px solid;
  color: white;
}
.order-entry-body {
  padding: 0 2.5rem 2.5rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 2rem;
  align-items: start;
}
.order-philosophy {
  font-family: var(--font-body);
  font-size: 1.05rem;
  line-height: 1.85;
  color: var(--iron);
  grid-column: 1/-1;
  padding: 1.5rem 0 0;
  border-top: 1px solid rgba(200,150,26,.12);
}
.order-block h4 {
  font-family: var(--font-serif);
  font-size: .82rem;
  font-weight: 700;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: var(--gold-deep);
  margin-bottom: .75rem;
  padding-bottom: .4rem;
  border-bottom: 1px solid rgba(200,150,26,.15);
}
.order-calling-list {
  list-style: none;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: .25rem .75rem;
}
.order-calling-list li {
  font-family: var(--font-sans);
  font-size: .78rem;
  color: #3a3020;
  padding: .2rem 0;
  display: flex;
  align-items: center;
  gap: .4rem;
}
.order-calling-list li::before {
  content: '⚔';
  font-size: .6rem;
  opacity: .4;
  flex-shrink: 0;
}
.order-traits {
  display: flex;
  flex-wrap: wrap;
  gap: .4rem;
  margin-top: .25rem;
}
.order-trait {
  font-family: var(--font-sans);
  font-size: .72rem;
  font-weight: 600;
  padding: .18rem .55rem;
  border-radius: 3px;
  border: 1px solid rgba(200,150,26,.2);
  background: rgba(200,150,26,.06);
  color: var(--gold-deep);
}
.order-oath-example {
  font-family: var(--font-body);
  font-style: italic;
  font-size: .9rem;
  color: var(--iron);
  background: rgba(255,255,255,.6);
  border-left: 3px solid;
  padding: .75rem 1rem;
  border-radius: 0 4px 4px 0;
  line-height: 1.65;
}
.order-known-as {
  font-family: var(--font-sans);
  font-size: .82rem;
  color: #5a5040;
  line-height: 1.65;
}
.order-known-as strong {
  color: var(--iron);
}

/* ── Comparison Table ── */
.compare-section {
  margin: 4rem 0;
}
.compare-section h2 {
  font-family: var(--font-display);
  font-size: 1.5rem;
  color: var(--iron);
  margin-bottom: 2rem;
  text-align: center;
}
.compare-section h2 em {
  color: var(--gold-deep);
  font-style: normal;
}
.compare-table {
  width: 100%;
  border-collapse: collapse;
  font-family: var(--font-sans);
  font-size: .8rem;
  background: rgba(255,255,255,.5);
  border-radius: var(--r);
  overflow: hidden;
  border: 1px solid rgba(200,150,26,.15);
}
.compare-table th {
  background: var(--iron);
  color: var(--gold);
  font-family: var(--font-serif);
  font-size: .75rem;
  letter-spacing: .1em;
  padding: .75rem 1rem;
  text-align: left;
}
.compare-table td {
  padding: .65rem 1rem;
  border-bottom: 1px solid rgba(200,150,26,.08);
  color: #3a3020;
  vertical-align: middle;
}
.compare-table tr:hover td {
  background: rgba(200,150,26,.04);
}
.compare-table tr:last-child td {
  border-bottom: none;
}
.compare-swatch {
  display: inline-block;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  margin-right: .4rem;
  vertical-align: middle;
  border: 1px solid rgba(0,0,0,.1);
}
.order-name-cell {
  font-family: var(--font-serif);
  font-weight: 700;
  color: var(--iron);
}

/* ── CTA ── */
.orders-cta {
  background: var(--iron);
  border: 2px solid rgba(200,150,26,.35);
  border-radius: var(--r);
  padding: 3rem;
  text-align: center;
  margin: 4rem 0 2rem;
}
.orders-cta h2 {
  font-family: var(--font-display);
  font-size: clamp(1.3rem,3vw,2rem);
  color: var(--gold-bright);
  margin-bottom: 1rem;
}
.orders-cta p {
  font-family: var(--font-body);
  font-style: italic;
  color: rgba(248,243,232,.65);
  max-width: 620px;
  margin: 0 auto 2rem;
  line-height: 1.85;
}
.cta-buttons {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

/* ── Responsive ── */
@media(max-width:800px) {
  .system-intro { grid-template-columns: 1fr; padding: 1.75rem; }
  .order-entry-body { grid-template-columns: 1fr; }
  .order-philosophy { grid-column: auto; }
  .order-calling-list { grid-template-columns: 1fr; }
}
@media(max-width:600px) {
  .order-entry-titles { padding: 1.5rem; }
  .order-entry-body { padding: 0 1.5rem 1.75rem; }
}
</style>
</head>
<body>

<!-- Header -->
<header class="site-header"><div class="header-inner">
  <a class="logo-wrap" href="/"><div class="logo-text">Paladin Profile<span>LUMINOUS Engine v5 · Rose Ministries</span></div></a>
  <nav class="main-nav" id="mainNav">
    <a href="/#quiz" style="color:var(--gold-bright)">♪ Heart Song</a>
    <a href="/orders/" class="active">Orders</a>
    <a href="/#wizard">Builder</a>
    <a href="/#gallery">Gallery</a>
    <a href="/#suggest">Suggest</a>
    <a href="/#journal">Journal</a>
    <a href="/#import">Import/Export</a>
  </nav>
  <button class="mobile-toggle" id="mobileToggle" aria-label="Menu"><span></span><span></span><span></span></button>
</div></header>

<!-- Hero -->
<div class="orders-hero">
  <div class="orders-hero-title">The Color Orders</div>
  <div class="orders-hero-sub">The Ten Covenants of the LUMINOUS Engine</div>
  <div class="orders-hero-lore">"Every Paladin swears to something. The Order is not a box you are placed in — it is the covenant you choose. It defines the lens through which you pursue your oath, the callings available to your path, and the spectrum of your power. Choose deliberately. This is a lifelong declaration."</div>

  <!-- Full spectrum bar -->
  <div class="spectrum-bar-full">
    <span style="background:#f0ead6"></span>
    <span style="background:#c9a922"></span>
    <span style="background:#1a6b3c"></span>
    <span style="background:#1a5276"></span>
    <span style="background:#c44d8e"></span>
    <span style="background:#c45e1a"></span>
    <span style="background:#a81c1c"></span>
    <span style="background:#5b2c8e"></span>
    <span style="background:#6b4226"></span>
    <span style="background:#1a1a2e"></span>
  </div>

  <!-- Jump nav -->
  <div class="jump-nav">
    <a href="#order-white" class="jump-btn" style="border-color:#c8c0a8;color:#c8c0a8">White</a>
    <a href="#order-yellow" class="jump-btn" style="border-color:#c9a922;color:#c9a922">Yellow</a>
    <a href="#order-green" class="jump-btn" style="border-color:#1a6b3c;color:#3a9a5c">Green</a>
    <a href="#order-blue" class="jump-btn" style="border-color:#1a5276;color:#4a82a6">Blue</a>
    <a href="#order-pink" class="jump-btn" style="border-color:#c44d8e;color:#d47dae">Pink</a>
    <a href="#order-orange" class="jump-btn" style="border-color:#c45e1a;color:#e47e3a">Orange</a>
    <a href="#order-red" class="jump-btn" style="border-color:#a81c1c;color:#d84c4c">Red</a>
    <a href="#order-purple" class="jump-btn" style="border-color:#5b2c8e;color:#9b6cce">Purple</a>
    <a href="#order-brown" class="jump-btn" style="border-color:#6b4226;color:#9b7246">Brown</a>
    <a href="#order-black" class="jump-btn" style="border-color:#6060a0;color:#9090c0">Black</a>
  </div>
</div>

<div class="orders-wrap">

  <!-- System Rules -->
  <div class="system-intro">
    <h2>How Orders Work</h2>
    <div class="system-rule">
      <h4>One Order Per Character</h4>
      <p>At character creation you choose a single Color Order. This is your primary covenant. At Level 35, the Dual Oath feat allows you to acquire a secondary Order's talent tree — but your primary Order always defines your identity.</p>
    </div>
    <div class="system-rule">
      <h4>Orders Unlock Callings</h4>
      <p>Your Calling pool is drawn from your Order. Each Order has 16 available callings (8 core + 8 expanded). Your calling defines your specific role within the Order's broader philosophy.</p>
    </div>
    <div class="system-rule">
      <h4>Spectrum, Not Morality</h4>
      <p>The ten Orders represent different philosophies of service, not a scale from good to evil. A Black Order Paladin and a White Order Paladin can both be deeply moral people serving the same ultimate cause — they differ in method, not intent.</p>
    </div>
    <div class="system-rule">
      <h4>Orders Are Self-Declared</h4>
      <p>No one assigns you your Order. You declare it. The LUMINOUS Engine has no gatekeepers. If you believe your oath belongs to the Order of the Brown, you are of the Order of the Brown. The engine records your declaration; your life validates it.</p>
    </div>
  </div>

  <!-- ══════════════════════════════════ -->
  <!-- ORDER 1: WHITE -->
  <!-- ══════════════════════════════════ -->
  <div class="order-entry" id="order-white">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#f0ead6"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER I · 01/10</div>
        <div class="order-entry-name" style="color:#8b7840">Order of the White</div>
        <div class="order-entry-covenant">The Radiant Covenant</div>
        <span class="order-entry-spectrum-badge" style="background:#8b7840;border-color:#8b7840">Pure Spiritual</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The White Order is the oldest covenant in spirit, if not in name. These are the healers, the prophets, the spiritual counselors — those for whom the invisible world is as real as the concrete one. Their power flows not from strength of arms or depth of knowledge, but from the unwavering conviction that something sacred moves through all things, and that their job is to serve as its instrument.</p>
        <p>To swear the White Oath is to accept radical vulnerability as a form of power. The White Paladin does not defend themselves first — they defend others, and trust that their faith will sustain them. This is not naive. It is the most demanding of all the covenants, because it requires the practitioner to remain open in a world that punishes openness.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Prophet / Seer</li><li>Healer / Chaplain</li>
          <li>Spiritual Director</li><li>Intercessor</li>
          <li>Oracle</li><li>Liturgist</li>
          <li>Exorcist</li><li>Vigil Keeper</li>
          <li>Blessing Smith</li><li>Grief Walker</li>
          <li>Radiant Scribe</li><li>Covenant Keeper</li>
          <li>Sanctuary Warden</li><li>Dawn Herald</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">White Paladins are the ones people call when things are <strong>spiritually or emotionally broken</strong> beyond what medicine or law can repair. They hold deathbeds. They name the unnamed grief. They speak into silence and make it sacred. When the calendar says it's a Tuesday and the room feels like the end of the world, a White Paladin is already there.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Spiritual Intelligence</span>
          <span class="order-trait">Emotional Endurance</span>
          <span class="order-trait">Sacred Presence</span>
          <span class="order-trait">Intercessory Power</span>
          <span class="order-trait">Vulnerability as Strength</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#c8c0a8">"I will hold space for those in darkness — without flinching, without fixing, without abandoning them to their grief."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 2: YELLOW -->
  <div class="order-entry" id="order-yellow">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#c9a922"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER II · 07/10</div>
        <div class="order-entry-name" style="color:#8b6d10">Order of the Yellow</div>
        <div class="order-entry-covenant">The Solar Decree</div>
        <span class="order-entry-spectrum-badge" style="background:#c9a922;border-color:#c9a922;color:#0f0c08">Knowledge &amp; Innovation</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Yellow Order swears its oath to illumination — not in the mystical sense of the White Order, but in the concrete, demonstrable sense: turning ignorance into understanding through rigorous method. Engineers, inventors, architects of future infrastructure, software builders, scientists — these are the Solar Paladins. Their creed is simple: the world is broken in specific ways that can be identified, measured, and fixed.</p>
        <p>Where other Orders seek to inspire or protect, the Yellow Order seeks to <em>solve</em>. They are the ones still in the lab at 2 AM not because they are commanded to be, but because the problem is not yet solved and an unsolved problem is an offense against their oath. The Solar Decree demands that knowledge be put to work — not hoarded, not weaponized, but deployed in service of human flourishing.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Software Engineer</li><li>Scientist / Researcher</li>
          <li>Inventor / Patent Holder</li><li>Architect</li>
          <li>Systems Designer</li><li>Algorithm Knight</li>
          <li>Open Source Paladin</li><li>Data Shepherd</li>
          <li>Prototype Warden</li><li>Grid Keeper</li>
          <li>Accessibility Knight</li><li>Debug Paladin</li>
          <li>Future Architect</li><li>Technology Ethicist</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Yellow Paladins are the ones who <strong>build the things others couldn't imagine</strong> were possible. They will spend three years on a problem that everyone else gave up on in three weeks. They do not accept "it can't be done" — they accept only "it hasn't been done yet." When civilization advances, it is usually because a Yellow Paladin refused to stop.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Systematic Thinking</span>
          <span class="order-trait">Technical Mastery</span>
          <span class="order-trait">Problem Obsession</span>
          <span class="order-trait">Knowledge Sharing</span>
          <span class="order-trait">Iterative Refinement</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#c9a922">"I will build things that work — that actually work, for actual people, in the actual world — and I will share what I learn so others can build further."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 3: GREEN -->
  <div class="order-entry" id="order-green">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#1a6b3c"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER III · 06/10</div>
        <div class="order-entry-name" style="color:#1a6b3c">Order of the Green</div>
        <div class="order-entry-covenant">The Verdant Path</div>
        <span class="order-entry-spectrum-badge" style="background:#1a6b3c;border-color:#1a6b3c">Growth &amp; Renewal</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Green Order sees the world as a garden — not a battlefield. Where Red Paladins fight and Yellow Paladins build, Green Paladins <em>grow</em>. Their philosophy holds that most of what is broken can be healed, and most of what is damaged can regenerate, given the right conditions and the right tending. They are healers of systems — biological, ecological, economic, social — and they understand that healing is not a single dramatic intervention but a sustained, patient process.</p>
        <p>The Verdant Path is the most long-term of all Orders. A Green Paladin plants trees under whose shade they know they will never sit, and they consider this the highest form of service. They are teachers, therapists, entrepreneurs, ecological restorers — anyone whose primary gift is helping something or someone reach their full potential through patient cultivation.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Therapist / Counselor</li><li>Entrepreneur / Incubator</li>
          <li>Educator / Mentor</li><li>Ecologist</li>
          <li>Compost Knight</li><li>Watershed Guardian</li>
          <li>Pollinator</li><li>Nursery Warden</li>
          <li>Succession Planner</li><li>Root Doctor</li>
          <li>Harvest Master</li><li>Rewilding Knight</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Green Paladins are the ones who <strong>see potential where others see ruin</strong>. They will work with the most damaged person, the most depleted ecosystem, the most failed organization — and find the seed of what it could become. They are incapable of writing things off as unsalvageable, which makes them the last resort and the most indispensable resource in any community.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Radical Patience</span>
          <span class="order-trait">Systems Healing</span>
          <span class="order-trait">Long-Term Vision</span>
          <span class="order-trait">Generative Thinking</span>
          <span class="order-trait">Restorative Practice</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#1a6b3c">"I will tend what is broken until it is whole, and I will not count the seasons it takes."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 4: BLUE -->
  <div class="order-entry" id="order-blue">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#1a5276"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER IV · 05/10</div>
        <div class="order-entry-name" style="color:#1a5276">Order of the Blue</div>
        <div class="order-entry-covenant">The Azure Shield</div>
        <span class="order-entry-spectrum-badge" style="background:#1a5276;border-color:#1a5276">Law &amp; Service</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Blue Order swears its oath to structure — not to power, not to tradition, but to the living institutions that make civilization possible. Law enforcement, military, emergency services, civil servants, constitutional attorneys, compliance officers — these are the Azure Paladins. They believe, against considerable evidence to the contrary, that human beings can build systems of cooperation that are worth defending, and they dedicate their lives to that defense.</p>
        <p>The Azure Shield is not naive about institutional failure. Blue Paladins have seen the worst of what systems can do. But rather than abandoning institutions, they go <em>into</em> them — to fix them from within, to hold the line when systems are tested, to be the person who says "this is not how we do things here" when everyone else has forgotten the covenant. Their oath is to the principle, not the policy.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Law Enforcement Officer</li><li>Military Officer</li>
          <li>Emergency Manager</li><li>Civil Servant</li>
          <li>Protocol Officer</li><li>Compliance Warden</li>
          <li>Dispatch Commander</li><li>Evidence Keeper</li>
          <li>Watch Commander</li><li>Constitutional Guardian</li>
          <li>Emergency Coordinator</li><li>Civil Engineer Paladin</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Blue Paladins are the ones who <strong>hold the line</strong>. When everything is falling apart, they do not flee — they move toward the problem, because that is what the oath demands. They are the first responders in every sense: first to the crisis, first to the difficult conversation, first to say "we cannot let this stand."</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Institutional Loyalty</span>
          <span class="order-trait">Crisis Stability</span>
          <span class="order-trait">Procedural Discipline</span>
          <span class="order-trait">Civic Courage</span>
          <span class="order-trait">Constitutional Integrity</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#1a5276">"I will serve the structure that protects the innocent — and I will fix it when it fails them, rather than abandon it."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 5: PINK -->
  <div class="order-entry" id="order-pink">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#c44d8e"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER V · 10/10</div>
        <div class="order-entry-name" style="color:#a43d7e">Order of the Pink</div>
        <div class="order-entry-covenant">The Rose Communion</div>
        <span class="order-entry-spectrum-badge" style="background:#c44d8e;border-color:#c44d8e">Compassion &amp; Art</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Pink Order holds what many consider the most undervalued power in the LUMINOUS Engine: the power of beauty, empathy, and creative expression. Artists, performers, counselors, diplomats, musicians, poets — these are the Rose Communion. Their oath is not to fight evil but to create conditions in which evil cannot take root. Where other Orders defeat darkness, the Pink Order displaces it with light so beautiful that the darkness cannot bear to remain.</p>
        <p>The Rose Communion is frequently dismissed as the "soft" Order, and this is the mistake that always destroys the people who make it. Pink Paladins carry the emotional weight of entire communities. They process what others cannot articulate. They transform grief into art, conflict into dialogue, disconnection into belonging. This is not soft work. This is the hardest work. The ones who underestimate it tend to fall apart first when real adversity arrives.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Visual Artist</li><li>Musician / Composer</li>
          <li>Counselor / Therapist</li><li>Diplomat</li>
          <li>Lullaby Knight</li><li>Beauty Warden</li>
          <li>Festival Master</li><li>Empathy Shield</li>
          <li>Color Keeper</li><li>Memory Weaver</li>
          <li>Harmony Knight</li><li>Grace Warden</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Pink Paladins are the ones who <strong>remember that humans need beauty to survive</strong>. They maintain the ceremonies, preserve the traditions, write the songs that generations carry forward. They know that a community without art is already dying — and they will not let that happen on their watch.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Emotional Intelligence</span>
          <span class="order-trait">Creative Vision</span>
          <span class="order-trait">Diplomatic Instinct</span>
          <span class="order-trait">Beauty Preservation</span>
          <span class="order-trait">Communal Healing</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#c44d8e">"I will create beauty in places where it has been forgotten — and I will not apologize for insisting that beauty is necessary."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 6: ORANGE -->
  <div class="order-entry" id="order-orange">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#c45e1a"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER VI · 08/10</div>
        <div class="order-entry-name" style="color:#a44e10">Order of the Orange</div>
        <div class="order-entry-covenant">The Ember Pact</div>
        <span class="order-entry-spectrum-badge" style="background:#c45e1a;border-color:#c45e1a">Courage &amp; Action</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Orange Order swears a single, uncompromising oath: act. When others deliberate, the Ember Paladin moves. When others wait for permission, the Ember Paladin has already deployed. This is not impulsiveness — it is the disciplined capacity to convert decision into motion faster than anyone else, because the Orange Paladin has done the inner work of knowing exactly what they stand for and exactly what that demands of them in any given moment.</p>
        <p>First responders, activists, crisis interveners, catalysts — these are the people who run toward the thing everyone else is running from. The Ember Pact demands courage not as a feeling but as a practice: the daily discipline of doing the necessary thing regardless of fear, because the oath is louder than the fear.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>First Responder</li><li>Activist / Advocate</li>
          <li>Crisis Intervener</li><li>Breach Specialist</li>
          <li>Smoke Jumper</li><li>Morale Officer</li>
          <li>Rescue Knight</li><li>Flash Point</li>
          <li>Endurance Specialist</li><li>Catalyst Knight</li>
          <li>Signal Flare</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Orange Paladins are the ones who <strong>make the first move</strong>. Everyone else was thinking about doing something. The Orange Paladin already did it. They are the person who stops the fight, pulls the lever, makes the call, breaks the silence. The world advances because of people who could not tolerate inaction one second longer.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Decisive Action</span>
          <span class="order-trait">Physical Courage</span>
          <span class="order-trait">Crisis Instinct</span>
          <span class="order-trait">Sustained Effort</span>
          <span class="order-trait">Catalytic Presence</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#c45e1a">"I will not wait for the right moment. I will be the right moment."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 7: RED -->
  <div class="order-entry" id="order-red">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#a81c1c"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER VII · 09/10</div>
        <div class="order-entry-name" style="color:#a81c1c">Order of the Red</div>
        <div class="order-entry-covenant">The Crimson Mandate</div>
        <span class="order-entry-spectrum-badge" style="background:#a81c1c;border-color:#a81c1c">Pure Martial</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Red Order does not ask what you believe or why you fight. It asks only: can you stand between harm and the innocent, and will you? The Crimson Mandate is the most elemental of all covenants — the oath of the warrior. Warriors first. Philosophers distant second. The Red Paladin trains because discipline is the answer to chaos, and the world generates chaos in abundance.</p>
        <p>The Red Order is frequently misunderstood as glorifying violence. This gets it precisely backwards. The Red Paladin trains in violence specifically so that they may be capable of preventing it. The most skilled warrior in the room is usually the most peaceful — because they know what real violence costs, and they have no need to prove themselves through escalation. The Crimson Mandate is sealed in discipline, not aggression.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Martial Artist</li><li>Soldier / Veteran</li>
          <li>Combat Coach</li><li>Shield Master</li>
          <li>Armorer</li><li>Sparring Partner</li>
          <li>Tournament Champion</li><li>Siege Engineer</li>
          <li>Scout Warrior</li><li>Formation Leader</li>
          <li>Honor Guard</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Red Paladins are the ones who <strong>make security possible for everyone else</strong>. Their discipline holds space for others to be vulnerable. They are the reason the meeting can happen, the reason the community can gather, the reason the project can be built. Without the Red Order, none of the other Orders can function safely.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Physical Discipline</span>
          <span class="order-trait">Combat Mastery</span>
          <span class="order-trait">Protective Instinct</span>
          <span class="order-trait">Honor Code</span>
          <span class="order-trait">Tactical Precision</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#a81c1c">"I will master violence so completely that I need never use it — and I will always be ready if I must."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 8: PURPLE -->
  <div class="order-entry" id="order-purple">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#5b2c8e"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER VIII · 04/10</div>
        <div class="order-entry-name" style="color:#5b2c8e">Order of the Purple</div>
        <div class="order-entry-covenant">The Arcane Throne</div>
        <span class="order-entry-spectrum-badge" style="background:#5b2c8e;border-color:#5b2c8e">Mystical Authority</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Purple Order occupies a unique position in the LUMINOUS Engine: the intersection of the divine and the scholarly. Where the White Order communes with the sacred through faith, the Purple Order approaches it through <em>study</em>. Scholars, researchers, hermetic practitioners, theologians who approach texts as living systems — these are the Arcane Throne. Their conviction is that knowledge itself is sacred, and the pursuit of understanding is a form of worship.</p>
        <p>The Arcane Throne demands both rigor and reverence. The Purple Paladin is as likely to cite primary sources as scripture, and they see no contradiction in this. They believe that truth is one thing encountered from multiple angles, and that the more precise your understanding of how things actually work, the better equipped you are to serve the forces of goodness and order. These are the battlemages and sage-knights: the ones who know, and whose knowledge is itself a weapon.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Scholar / Academic</li><li>Theologian</li>
          <li>Researcher</li><li>Theorem Knight</li>
          <li>Pattern Breaker</li><li>Library Warden</li>
          <li>Hypothesis Paladin</li><li>Codex Master</li>
          <li>Mind Fortress</li><li>Translation Knight</li>
          <li>Axiom Guardian</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Purple Paladins are the ones who <strong>know things no one else has thought to study</strong>. They are the person you call when the question is so deep that normal intelligence doesn't reach it. They have read the primary sources. They have cross-referenced the commentary. They have thought about it for longer than you have been aware the question existed. And they will answer.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Deep Scholarship</span>
          <span class="order-trait">Pattern Recognition</span>
          <span class="order-trait">Sacred Curiosity</span>
          <span class="order-trait">Intellectual Courage</span>
          <span class="order-trait">Knowledge as Service</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#5b2c8e">"I will learn everything there is to know about the thing that matters most — and I will use that knowledge in service, not dominion."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 9: BROWN -->
  <div class="order-entry" id="order-brown">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#6b4226"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER IX · 03/10</div>
        <div class="order-entry-name" style="color:#6b4226">Order of the Brown</div>
        <div class="order-entry-covenant">The Earthen Compact</div>
        <span class="order-entry-spectrum-badge" style="background:#6b4226;border-color:#6b4226">Steward of Land</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Brown Order is the oldest in practice, if not in recognition. These are the farmers, the ranchers, the environmental stewards, the master tradespeople, the keepers of oral history and ancestral memory. The Earthen Compact is a covenant with the land itself — and by extension, with the communities that depend on the land for their physical and cultural survival. The Brown Paladin knows that civilizations are built on soil, and that if you do not tend the soil, the civilization will follow it into ruin.</p>
        <p>In an age that rewards abstraction, the Brown Order insists on the concrete. Food actually has to be grown. Water actually has to flow clean. Traditions actually have to be practiced or they are lost. The Earthen Compact demands that its adherents get their hands dirty — literally and metaphorically — in service of the living systems that no one else wants to maintain because they are too slow, too unglamorous, too essential.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Farmer / Rancher</li><li>Environmental Steward</li>
          <li>Master Tradesperson</li><li>Well Keeper</li>
          <li>Boundary Walker</li><li>Seed Vault Guardian</li>
          <li>Storm Watcher</li><li>Hearth Tender</li>
          <li>Trail Blazer</li><li>Timber Warden</li>
          <li>Heritage Keeper</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Brown Paladins are the ones who <strong>make everything else possible</strong> without receiving credit for it. While other Orders fight for glory, the Brown Order makes sure there is food on the table and the fence line is maintained and the old stories are written down before the last person who knows them dies. They are the substrate of civilization.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Land Stewardship</span>
          <span class="order-trait">Ancestral Knowledge</span>
          <span class="order-trait">Physical Craft</span>
          <span class="order-trait">Heritage Preservation</span>
          <span class="order-trait">Sustainable Practice</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#6b4226">"I will tend the land and the memory it holds — and I will pass both on better than I received them."</div>
      </div>
    </div>
  </div>

  <!-- ORDER 10: BLACK -->
  <div class="order-entry" id="order-black">
    <div class="order-entry-header">
      <div class="order-entry-swatch" style="background:#1a1a2e"></div>
      <div class="order-entry-titles">
        <div class="order-entry-num">ORDER X · 02/10</div>
        <div class="order-entry-name" style="color:#9090c0">Order of the Black</div>
        <div class="order-entry-covenant">The Obsidian Accord</div>
        <span class="order-entry-spectrum-badge" style="background:#1a1a2e;border-color:#6060a0">Shadow Conviction</span>
      </div>
    </div>
    <div class="order-entry-body">
      <div class="order-philosophy">
        <p>The Black Order is the most misunderstood covenant in the LUMINOUS Engine, and understanding the misunderstanding is the first step to understanding the Order itself. The Obsidian Accord does not glorify darkness — it consecrates the willingness to <em>enter</em> it on behalf of those who cannot. Intelligence operatives, strategic thinkers who understand moral complexity, those who dismantle corruption from the inside — these are the Obsidian Paladins. They walk in darkness so others don't have to.</p>
        <p>The Black Order swears what is perhaps the hardest oath of all: to do what is necessary even when no one will ever know, even when it will look wrong to those without context, even when history may judge them harshly. They are the people who understand that sometimes the shield must become the sword, and that accepting this truth — rather than hiding from it — is what separates effective goodness from performative goodness. The Accord demands results, not appearances.</p>
      </div>
      <div class="order-block">
        <h4>Core Callings</h4>
        <ul class="order-calling-list">
          <li>Intelligence Analyst</li><li>Strategic Advisor</li>
          <li>Cipher Knight</li><li>Wraith Hunter</li>
          <li>Archive Keeper</li><li>Ghost Protocol</li>
          <li>Veil Walker</li><li>Counter-Curse Specialist</li>
          <li>Obsidian Judge</li><li>Deep Cover Warden</li>
        </ul>
      </div>
      <div class="order-block">
        <h4>Known For</h4>
        <p class="order-known-as">Black Paladins are the ones who <strong>solved the problem before you knew it existed</strong>. They saw the threat when it was still invisible and neutralized it before it could become a crisis. No one will ever know. They did it anyway. The world is safer because of work that can never be acknowledged — and they have made their peace with that.</p>
        <h4 style="margin-top:1.25rem">Archetype Traits</h4>
        <div class="order-traits">
          <span class="order-trait">Strategic Intelligence</span>
          <span class="order-trait">Operational Security</span>
          <span class="order-trait">Moral Complexity</span>
          <span class="order-trait">Results Over Optics</span>
          <span class="order-trait">Quiet Effectiveness</span>
        </div>
        <h4 style="margin-top:1.25rem">Example Oath</h4>
        <div class="order-oath-example" style="border-left-color:#6060a0">"I will do what needs to be done, without credit, without recognition, without certainty that I am understood — because the oath is not contingent on applause."</div>
      </div>
    </div>
  </div>

  <!-- Comparison Table -->
  <div class="compare-section">
    <h2>Orders at a <em>Glance</em></h2>
    <table class="compare-table">
      <thead>
        <tr>
          <th>Order</th>
          <th>Covenant</th>
          <th>Spectrum</th>
          <th>Primary Domain</th>
          <th>Core Virtue</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#f0ead6;border-color:#8b7840"></span>White</td>
          <td>The Radiant Covenant</td>
          <td>Pure Spiritual</td>
          <td>Faith, Healing, Sacred Presence</td>
          <td>Vulnerability as Strength</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#c9a922"></span>Yellow</td>
          <td>The Solar Decree</td>
          <td>Knowledge &amp; Innovation</td>
          <td>Engineering, Science, Invention</td>
          <td>Relentless Problem-Solving</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#1a6b3c"></span>Green</td>
          <td>The Verdant Path</td>
          <td>Growth &amp; Renewal</td>
          <td>Healing, Teaching, Restoration</td>
          <td>Radical Patience</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#1a5276"></span>Blue</td>
          <td>The Azure Shield</td>
          <td>Law &amp; Service</td>
          <td>Justice, Protection, Institutions</td>
          <td>Civic Courage</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#c44d8e"></span>Pink</td>
          <td>The Rose Communion</td>
          <td>Compassion &amp; Art</td>
          <td>Art, Empathy, Creative Expression</td>
          <td>Beauty as Necessity</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#c45e1a"></span>Orange</td>
          <td>The Ember Pact</td>
          <td>Courage &amp; Action</td>
          <td>Crisis Response, Activism, Movement</td>
          <td>Decisive Action</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#a81c1c"></span>Red</td>
          <td>The Crimson Mandate</td>
          <td>Pure Martial</td>
          <td>Combat, Protection, Discipline</td>
          <td>Honor Through Mastery</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#5b2c8e"></span>Purple</td>
          <td>The Arcane Throne</td>
          <td>Mystical Authority</td>
          <td>Scholarship, Research, Sacred Study</td>
          <td>Knowledge as Service</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#6b4226"></span>Brown</td>
          <td>The Earthen Compact</td>
          <td>Steward of Land</td>
          <td>Agriculture, Craft, Heritage</td>
          <td>Generational Stewardship</td>
        </tr>
        <tr>
          <td class="order-name-cell"><span class="compare-swatch" style="background:#1a1a2e;border-color:#6060a0"></span>Black</td>
          <td>The Obsidian Accord</td>
          <td>Shadow Conviction</td>
          <td>Intelligence, Strategy, Covert Service</td>
          <td>Results Over Optics</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- CTA -->
  <div class="orders-cta">
    <h2>Ready to Declare Your Order?</h2>
    <p>"You don't find your Order. You recognize it — because you have already been living by its covenant, even before you had a name for it."</p>
    <div class="cta-buttons">
      <a href="/" class="btn btn-next" style="text-decoration:none;padding:.75rem 2rem">🎵 Take the Heart Song Quiz</a>
      <a href="/" class="btn" style="color:var(--gold);text-decoration:none;padding:.75rem 2rem">I Know My Order — Go to Builder →</a>
    </div>
  </div>

</div><!-- /orders-wrap -->

<!-- Closing -->
<div class="closing-section">
  <h2>Per Silvam et Iter</h2>
  <p><em>"Through the forest and the journey."</em></p>
  <div style="width:80px;height:1px;background:rgba(200,150,26,.5);margin:2rem auto"></div>
  <p>Ten Orders. One oath. Your real life is the campaign. Every level is earned.</p>
  <div class="motto-final">GOOD GUYS, ON ME.</div>
</div>

<!-- Footer -->
<footer class="site-footer"><div class="footer-inner">
  <div class="footer-top">
    <div class="footer-brand"><div class="footer-brand-name">Gold Hat <span>Consulting</span></div><p>LUMINOUS Game Engine v3 · Paladin Profile Generator. Rose Ministries ordained. GoldHat™ &amp; ArchDaemon™ property network.</p></div>
    <div class="footer-links"><h4>Properties</h4><ul><li><a href="https://goldhatconsulting.com">GoldHat Home</a></li><li><a href="https://therealpreacher.com">The Real Preacher</a></li><li><a href="https://beginaministry.com/ministershop/">Rose Ministries</a></li></ul></div>
    <div class="footer-links"><h4>Engine</h4><ul><li><a href="/">Character Builder</a></li><li><a href="/orders/">Orders Manual</a></li><li><a href="/">Public Gallery</a></li></ul></div>
  </div>
  <div class="footer-bottom"><p>&copy; 2026 David William Sylvester. All rights reserved.</p><div class="footer-legal">GoldHat™ 98925168 · ArchDaemon™ 98940257 · LUMINOUS Engine v5.0</div></div>
</div></footer>

<script>
// Mobile nav
const tog=document.getElementById('mobileToggle'),nav=document.getElementById('mainNav');
if(tog&&nav){tog.addEventListener('click',()=>nav.classList.toggle('open'));}

// Smooth scroll already handled by CSS scroll-behavior
// Active nav highlight
document.querySelectorAll('.main-nav a').forEach(a => {
  if(a.href === window.location.href) a.classList.add('active');
});
</script>
</body>
</html>

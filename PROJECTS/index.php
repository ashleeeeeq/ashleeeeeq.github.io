<?php
$projects = [
    [
        'slug' => 'venusia',
        'name' => 'Venusia',
        'num' => '001',
        'date' => '2026',
        'desc' => 'A session-based e-commerce store with a poké-inspired look — storefront, cart, checkout, user profiles and a full email-verification flow, all hand-rolled in plain PHP.',
        'tags' => ['PHP', 'MySQL', 'Sessions', 'E-commerce'],
        'color' => 'blue',
    ],
    [
        'slug' => 'codesktutorials',
        'name' => 'CoDesk',
        'num' => '002',
        'date' => '2025',
        'desc' => 'A free online learning hub for JavaScript, Java and C# — interactive lessons, assessments and quizzes with a clean course structure and dark mode.',
        'tags' => ['HTML', 'CSS', 'JS', 'Education'],
        'color' => 'yellow',
    ],
    [
        'slug' => 'pokemongo',
        'name' => 'Pokémon Store',
        'num' => '003',
        'date' => '2025',
        'desc' => 'A fully static, Pokémon Go–inspired storefront: animated login, registration, profile page, homepage and cart flows, built with vanilla HTML/CSS/JS.',
        'tags' => ['HTML', 'CSS', 'JS', 'UI Kit'],
        'color' => 'coral',
    ],
    [
        'slug' => 'FAIRALL',
        'name' => 'FAIRALL',
        'num' => '004',
        'date' => '2026',
        'desc' => 'A Laravel aid & scholarship management system — beneficiaries, donors, grants, funding, activities, attendance and reports, with role-based dashboards.',
        'tags' => ['PHP', 'Laravel', 'MySQL', 'Blade'],
        'color' => 'mint',
    ],
    [
        'slug' => 'CoffeeShop',
        'name' => 'Coffee Shop',
        'num' => '005',
        'date' => '2025',
        'desc' => 'A CodeIgniter 4 coffee shop toolkit — home, ordering, profiles and an admin area, structured as a slim MVC app.',
        'tags' => ['PHP', 'CodeIgniter 4', 'MVC'],
        'color' => 'purple',
    ],
];

$extras = [
    [
        'slug' => '../flashcards',
        'name' => 'flashcards',
        'desc' => 'Review flashcards for Systems Integration & Architecture (SA1).',
        'color' => 'ink',
    ],
    [
        'slug' => '../OLD-FILES',
        'name' => 'COLLEGE CODING FILES',
        'desc' => 'All my saved college programming code — TS1–TS4, kye, fakeblook and sample work.',
        'color' => 'ink',
    ],
];

function vault_scan($dir, $exclude) {
    $found = [];
    $items = @scandir($dir);
    if ($items === false) return $found;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        if (!is_dir($dir . DIRECTORY_SEPARATOR . $item)) continue;
        if (in_array($item, $exclude, true)) continue;
        $found[] = $item;
    }
    return $found;
}

$known = array_column($projects, 'slug');
$raw = vault_scan(__DIR__, $known);

function esc($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function tag_span($t) { return '<span>' . esc($t) . '</span>'; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Projects — Janna Ashley Quiban</title>
<meta name="description" content="An index of all of Janna Ashley Quiban's projects — web, mobile and everything in between.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Handjet:wght@700;800;900&family=Just+Me+Again+Down+Here&family=DM+Mono:wght@400;500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{
  --paper:#f8f4ea;
  --ink:#1c1c1a;
  --yellow:#ffcf49;
  --blue:#5b8ef5;
  --coral:#ff7a5c;
  --mint:#5fd0a0;
  --purple:#a889f0;
  --line:rgba(28,28,26,0.14);
}
*{box-sizing:border-box;}
html{scroll-behavior:smooth; scrollbar-width:thin; scrollbar-color:var(--ink) var(--paper);}
::-webkit-scrollbar{width:10px;}
::-webkit-scrollbar-track{background:var(--paper);}
::-webkit-scrollbar-thumb{background:var(--ink); border-radius:99px; border:2px solid var(--paper);}
body{
  margin:0;
  background:
    repeating-linear-gradient(to bottom, rgba(28,28,26,0) 0 31px, rgba(28,28,26,0.06) 31px 32px),
    var(--paper);
  color:var(--ink);
  font-family:'Inter', sans-serif; line-height:1.5; -webkit-font-smoothing:antialiased;
}
a{color:inherit; text-decoration:none;}
.hand{font-family:'Just Me Again Down Here', cursive;}
.display{font-family:'Handjet', sans-serif; font-weight:800;}
.wrap{max-width:1080px; margin:0 auto; padding:0 28px;}

.squiggle{display:block; width:90px; height:12px; margin:6px auto 0;}
.squiggle path{fill:none; stroke:var(--ink); stroke-width:1.6; stroke-linecap:round;}

nav.top{
  position:sticky; top:0; z-index:40;
  background:rgba(248,244,234,0.9); backdrop-filter:blur(6px);
  border-bottom:1px solid var(--line);
}
nav.top .wrap{display:flex; align-items:center; justify-content:space-between; height:62px;}
.brand{font-family:'Inter', sans-serif; font-weight:700; font-size:16px;}
.navlinks{display:flex; gap:6px;}
.navlinks a{
  font-family:'DM Mono', monospace; font-size:12.5px; font-weight:500;
  padding:9px 14px; border-radius:999px; text-transform:lowercase;
  transition:background .35s cubic-bezier(.4,0,.2,1);
}
.navlinks a:hover{background:var(--yellow);}
.navlinks a.active{background:var(--ink); color:var(--paper);}
.status-pill{display:flex; align-items:center; gap:7px; font-size:12.5px; font-family:'DM Mono', monospace;}
.status-pill .dot{width:7px; height:7px; border-radius:50%; background:var(--mint); box-shadow:0 0 0 3px rgba(95,208,160,0.25);}
@media(max-width:760px){.navlinks{display:none;}}

main section{padding:64px 0 0;}
.sec-label{text-align:center; font-size:26px;}

.hero{text-align:center; padding:64px 0 30px;}
.hero-label{font-size:26px;}
.hero-title{font-size:clamp(48px,10vw,96px); line-height:0.9; margin:10px 0 6px;}
.hero-sub{max-width:52ch; margin:6px auto 0; font-family:'Just Me Again Down Here', cursive; font-size:26px; color:rgba(28,28,26,0.8);}
.stickers-row{display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin:24px 0 0;}
.sticker{
  font-family:'DM Mono', monospace; font-size:12.5px; font-weight:600;
  padding:9px 16px; border-radius:999px; box-shadow:2px 3px 0 rgba(28,28,26,0.18);
  border:2px solid var(--ink);
}
.s1{background:var(--yellow); transform:rotate(-4deg);}
.s2{background:var(--mint); transform:rotate(3deg);}
.s3{background:#fff; transform:rotate(-2deg);}

.count-pill{
  display:inline-flex; align-items:center; gap:8px; margin-top:26px;
  font-family:'DM Mono', monospace; font-size:12.5px; font-weight:600;
  background:var(--ink); color:var(--paper);
  padding:10px 18px; border-radius:999px;
}

.grid{
  margin-top:34px;
  display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px;
}
.card{
  position:relative; display:flex; flex-direction:column;
  border:2px solid var(--ink); border-radius:14px; background:#fff;
  box-shadow:3px 5px 0 rgba(28,28,26,0.12); padding:52px 26px 24px; overflow:hidden;
  transition:transform .3s cubic-bezier(.4,0,.2,1), box-shadow .3s cubic-bezier(.4,0,.2,1);
}
.card:hover{transform:translateY(-4px); box-shadow:5px 9px 0 rgba(28,28,26,0.16);}
.card-tab{
  position:absolute; left:-2px; top:0;
  font-family:'DM Mono', monospace; font-size:11px; font-weight:700; letter-spacing:0.04em;
  text-transform:uppercase; color:#fff; padding:7px 16px 7px 18px;
  clip-path:polygon(0 0, 100% 0, calc(100% - 10px) 100%, 0 100%);
}
.card-blue{background:var(--blue);} .card-blue .card-name{color:#fff;} .card-blue .card-view{color:#fff;}
.card-yellow{background:var(--yellow);} .card-yellow .card-view{color:var(--ink);}
.card-coral{background:var(--coral);} .card-coral .card-name{color:#fff;} .card-coral .card-view{color:#fff;}
.card-mint{background:var(--mint);} .card-mint .card-name{color:#fff;} .card-mint .card-view{color:#fff;}
.card-purple{background:var(--purple);} .card-purple .card-name{color:#fff;} .card-purple .card-view{color:#fff;}
.card-name{font-family:'Handjet', sans-serif; font-weight:800; font-size:clamp(34px,4vw,44px); line-height:1; margin:6px 0 14px;}
.card-date{font-family:'DM Mono', monospace; font-size:11px; font-weight:600; opacity:0.75;}
.card-desc{font-size:14px; line-height:1.55; margin:0 0 20px; opacity:0.92;}
.card-tags{display:flex; gap:8px; flex-wrap:wrap; margin-top:auto;}
.card-tags span{
  font-family:'DM Mono', monospace; font-size:10.5px; font-weight:700; text-transform:uppercase;
  padding:6px 12px; border-radius:8px; background:rgba(28,28,26,0.85); color:#fff;
}
.card-blue .card-tags span{background:rgba(28,28,26,0.35);}
.card-yellow .card-tags span{background:rgba(28,28,26,0.25);}
.card-coral .card-tags span{background:rgba(28,28,26,0.35);}
.card-mint .card-tags span{background:rgba(28,28,26,0.3);}
.card-purple .card-tags span{background:rgba(28,28,26,0.35);}
.card-ink{background:var(--ink); color:var(--paper);}
.card-ink .card-name,.card-ink .card-view{color:var(--paper);}
.card-ink .card-tab{background:var(--yellow); color:var(--ink);}
.card-ink .card-tags span{background:rgba(248,244,234,0.2);}
.card-view{
  display:inline-flex; align-items:center; gap:6px; margin-top:20px; align-self:flex-start;
  font-family:'DM Mono', monospace; font-size:12px; font-weight:700; letter-spacing:0.03em; text-transform:uppercase;
  border-bottom:2px solid currentColor; padding-bottom:3px;
}

.extras .grid{grid-template-columns:repeat(auto-fit,minmax(240px,1fr));}
.card-ext{background:var(--ink); color:var(--paper); min-height:0;}
.card-ext .card-name,.card-ext .card-desc,.card-ext .card-view{color:var(--paper);}
.card-ext .card-desc{opacity:0.8;}

.note{
  max-width:640px; margin:44px auto 0; text-align:center;
  font-family:'DM Mono', monospace; font-size:11.5px; color:rgba(28,28,26,0.55); line-height:1.9;
}

footer{border-top:1px solid var(--line); padding:40px 0 30px; margin-top:70px;}
.foot-base{
  display:flex; justify-content:space-between; font-size:12.5px; color:rgba(28,28,26,0.55);
  font-family:'DM Mono', monospace; flex-wrap:wrap; gap:8px;
}
</style>
</head>
<body>

<nav class="top">
  <div class="wrap">
    <a class="brand" href="#top">Janna Ashley</a>
    <div class="navlinks">
      <a href="../portfolio/">portfolio</a>
      <a href="#projects">projects</a>
      <a href="#extras">vault</a>
      <a class="active" href="#">top</a>
    </div>
    <span class="status-pill"><span class="dot"></span> project index</span>
  </div>
</nav>

<main id="top">

  <header class="hero">
    <div class="wrap">
      <p class="hero-label hand">the vault</p>
      <svg class="squiggle" viewBox="0 0 90 12"><path d="M2 6c14-6 28-6 42 0s28 6 42 0"/></svg>
      <h1 class="display hero-title">ALL MY PROJECTS</h1>
      <p class="hero-sub">an index of everything I've built — web, mobile and everything in between.</p>
      <div class="stickers-row">
        <span class="sticker s1"><?php echo esc(count($projects)); ?> active projects</span>
        <span class="sticker s2">PHP + HTML twin</span>
        <span class="sticker s3">auto-scan enabled</span>
      </div>
      <span class="count-pill">PROJECTS/ &rarr; index.php</span>
    </div>
  </header>

  <section id="projects">
    <div class="wrap">
      <div class="grid">
<?php foreach ($projects as $p): ?>
        <a class="card card-<?php echo esc($p['color']); ?>" href="<?php echo esc($p['slug'] . '/'); ?>">
          <span class="card-tab">PROJ/<?php echo esc($p['num']); ?></span>
          <span class="card-name"><?php echo esc($p['name']); ?></span>
          <span class="card-date"><?php echo esc($p['date']); ?></span>
          <span class="card-desc"><?php echo esc($p['desc']); ?></span>
          <span class="card-tags"><?php foreach ($p['tags'] as $t) echo tag_span($t); ?></span>
          <span class="card-view">OPEN PROJECT ↗</span>
        </a>
<?php endforeach; ?>
<?php if (!empty($raw)): foreach ($raw as $slug): ?>
        <a class="card card-ink" href="<?php echo esc($slug . '/'); ?>">
          <span class="card-tab">SCANNED</span>
          <span class="card-name"><?php echo esc($slug); ?></span>
          <span class="card-desc">Folder found by the autoscan — not in the metadata map yet.</span>
          <span class="card-view">OPEN FOLDER ↗</span>
        </a>
<?php endforeach; endif; ?>
      </div>
    </div>
  </section>

  <section id="extras">
    <div class="wrap">
      <p class="sec-label hand">more in the vault</p>
      <svg class="squiggle" viewBox="0 0 90 12"><path d="M2 6c14-6 28-6 42 0s28 6 42 0"/></svg>
      <div class="grid extras">
<?php foreach ($extras as $x): ?>
        <a class="card card-ext" href="<?php echo esc($x['slug'] . '/'); ?>">
          <span class="card-name"><?php echo esc($x['name']); ?></span>
          <span class="card-desc"><?php echo esc($x['desc']); ?></span>
          <span class="card-view">OPEN ↗</span>
        </a>
<?php endforeach; ?>
      </div>
      <p class="note">index.php scans the PROJECTS/ folder on every load — drop in a new project folder and it shows up automatically.<br>index.html is the static twin deployed to GitHub Pages (GitHub Pages can't run PHP).</p>
    </div>
  </section>

</main>

<footer>
  <div class="wrap">
    <div class="foot-base">
      <span>Janna Ashley H. Quiban — project index</span>
      <span><a href="../portfolio/">← back to portfolio</a></span>
    </div>
  </div>
</footer>

</body>
</html>
'use strict';

/* ==========================================================
   CoDesk — shared site JS (navbar, theme, reveal, counters)
   ========================================================== */

(function () {
  const $ = (s, c) => (c || document).querySelector(s);
  const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));

  /* ---------- Navbar: shadow on scroll + mobile toggle ---------- */
  const navbar = $('.navbar');
  const toggleBtn = $('.nav-toggle');
  const mobileNav = $('.mobile-nav');

  const onScroll = () => {
    if (!navbar) return;
    navbar.classList.toggle('scrolled', window.scrollY > 12);
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  if (toggleBtn && mobileNav) {
    toggleBtn.addEventListener('click', () => mobileNav.classList.toggle('open'));
    mobileNav.querySelectorAll('a').forEach((a) =>
      a.addEventListener('click', () => mobileNav.classList.remove('open'))
    );
  }

  /* ---------- Active nav link from current hash/scroll ---------- */
  const activeLinks = $$('.nav-links a[data-nav]');
  if (activeLinks.length && $('[data-scrollspy]')) {
    const sections = $$('[data-scrollspy]');
    const spy = () => {
      const pos = window.scrollY + 120;
      let currentId = '';
      sections.forEach((s) => { if (s.offsetTop <= pos) currentId = s.id; });
      activeLinks.forEach((a) =>
        a.classList.toggle('active', a.getAttribute('href') === '#' + currentId)
      );
    };
    window.addEventListener('scroll', spy, { passive: true });
    spy();
  }

  /* ---------- Dark mode ---------- */
  const THEME_KEY = 'codesk-theme';
  const themeToggle = $('[data-theme-toggle]');
  const sunIcon = themeToggle && themeToggle.querySelector('.icon-sun');
  const moonIcon = themeToggle && themeToggle.querySelector('.icon-moon');

  const applyTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem(THEME_KEY, theme);
    if (sunIcon) sunIcon.style.display = theme === 'dark' ? 'none' : 'block';
    if (moonIcon) moonIcon.style.display = theme === 'dark' ? 'block' : 'none';
  };

  const storedTheme = localStorage.getItem(THEME_KEY);
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
  applyTheme(storedTheme || (prefersDark ? 'dark' : 'light'));

  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      applyTheme(next);
    });
  }

  /* ---------- Scroll reveal ---------- */
  const revealEls = $$('.reveal');
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(
      (entries) => entries.forEach((e) => {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
      }),
      { threshold: 0.12 }
    );
    revealEls.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add('in'));
  }

  /* ---------- Animated counters ---------- */
  const counters = $$('[data-count]');
  const animateCount = (el) => {
    const target = parseFloat(el.getAttribute('data-count'));
    const dur = 1200;
    const start = performance.now();
    const step = (now) => {
      const t = Math.min((now - start) / dur, 1);
      const eased = 1 - Math.pow(1 - t, 3);
      el.textContent = Math.round(target * eased);
      if (t < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  };
  if ('IntersectionObserver' in window && counters.length) {
    const cio = new IntersectionObserver(
      (entries) => entries.forEach((e) => {
        if (e.isIntersecting) { animateCount(e.target); cio.unobserve(e.target); }
      }),
      { threshold: 0.5 }
    );
    counters.forEach((el) => cio.observe(el));
  }

  /* ---------- Hero typing effect ---------- */
  const typeEl = $('[data-type]');
  if (typeEl) {
    const words = typeEl.getAttribute('data-type').split('|');
    let wi = 0, ci = 0, deleting = false;
    const tick = () => {
      const word = words[wi];
      typeEl.textContent = word.slice(0, ci);
      if (!deleting && ci < word.length) { ci++; setTimeout(tick, 85); }
      else if (!deleting && ci === word.length) { deleting = true; setTimeout(tick, 1600); }
      else if (deleting && ci > 0) { ci--; setTimeout(tick, 40); }
      else { deleting = false; wi = (wi + 1) % words.length; setTimeout(tick, 300); }
    };
    tick();
  }
})();

/* ==========================================================
   Confetti (lightweight, dependency-free)
   ========================================================== */
function burstConfetti() {
  const colors = ['#6366f1', '#8b5cf6', '#f59e0b', '#10b981', '#ec4899', '#f97316'];
  const count = 110;
  const root = document.documentElement;
  for (let i = 0; i < count; i++) {
    const piece = document.createElement('div');
    piece.className = 'confetti-piece';
    piece.style.left = Math.random() * 100 + 'vw';
    piece.style.top = -20 + 'px';
    piece.style.background = colors[(Math.random() * colors.length) | 0];
    piece.style.width = 6 + Math.random() * 8 + 'px';
    piece.style.height = 8 + Math.random() * 10 + 'px';
    piece.style.animationDuration = 1.6 + Math.random() * 1.6 + 's';
    piece.style.animationDelay = Math.random() * 0.4 + 's';
    piece.style.setProperty('--rot', Math.random() * 720 + 'deg');
    root.appendChild(piece);
    setTimeout(() => piece.remove(), 4000);
  }
}

/* ==========================================================
   Toast notification
   ========================================================== */
function showToast(msg, type) {
  let toast = document.querySelector('.toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.className = 'toast';
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.classList.remove('ok', 'no');
  toast.classList.add(type === 'ok' ? 'ok' : 'no', 'show');
  clearTimeout(toast._t);
  toast._t = setTimeout(() => toast.classList.remove('show'), 2400);
}

/* ==========================================================
   Copy-to-clipboard helper (used by code blocks + lesson.js)
   ========================================================== */
async function copyText(text, btn) {
  try {
    await navigator.clipboard.writeText(text);
  } catch (e) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    ta.remove();
  }
  if (btn) {
    btn.classList.add('copied');
    const label = btn.querySelector('span') || btn;
    const prev = label.textContent;
    if (btn.querySelector('span')) label.textContent = 'Copied!';
    setTimeout(() => { btn.classList.remove('copied'); label.textContent = prev; }, 1600);
  }
}
// Intro splash: types out two messages on separate screens, fading
// each out before the next appears. Runs first so it's out of the
// way before the nav/reveal logic below needs the page to be visible.
(function runSplash() {
  const splash1 = document.querySelector('.splash-1');
  const splash2 = document.querySelector('.splash-2');
  const msg1 = document.getElementById('splashMsg1');
  const msg2 = document.getElementById('splashMsg2');
  if (!splash1 && !splash2) return;

  document.body.classList.add('lock-scroll');
  document.body.classList.add('page-loading');

  const showPage = () => {
    document.body.classList.remove('lock-scroll');
    document.body.classList.remove('page-loading');
    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
  };

  const finish = () => {
    showPage();
    [splash1, splash2].forEach((s) => {
      if (!s) return;
      s.classList.add('is-hiding');
      setTimeout(() => {
        s.style.display = 'none';
        s.setAttribute('aria-hidden', 'true');
      }, 500);
    });
  };
  const safetyTimer = setTimeout(finish, 6000);

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const revealMessage = (msgEl) =>
    new Promise((resolve) => {
      const chars = msgEl.querySelectorAll('.ch');
      if (reduceMotion || !chars.length) {
        chars.forEach((c) => c.classList.add('in'));
        resolve();
        return;
      }
      chars.forEach((c, i) => setTimeout(() => c.classList.add('in'), i * 35));
      setTimeout(resolve, chars.length * 35 + 200);
    });

  const wait = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

  const fadeOut = (el) =>
    new Promise((resolve) => {
      el.classList.add('is-hiding');
      setTimeout(resolve, 500);
    });

  const fadeIn = (el) => {
    el.classList.remove('is-hidden');
    el.classList.remove('is-hiding');
  };

  (async () => {
    try {
      if (reduceMotion) {
        if (msg1) msg1.querySelectorAll('.ch').forEach((c) => c.classList.add('in'));
        await wait(200);
        if (splash1) { splash1.classList.add('is-hiding'); splash1.style.display = 'none'; }
        if (msg2) msg2.querySelectorAll('.ch').forEach((c) => c.classList.add('in'));
        if (splash2) fadeIn(splash2);
        await wait(200);
      } else {
        // Screen 1: "hey there!"
        await wait(150);
        if (msg1) await revealMessage(msg1);
        await wait(600);
        if (splash1) await fadeOut(splash1);
        if (splash1) splash1.style.display = 'none';

        // Screen 2: "you're in the right repo."
        if (splash2) fadeIn(splash2);
        await wait(200);
        if (msg2) await revealMessage(msg2);
        await wait(700);
        if (splash2) await fadeOut(splash2);
      }
    } catch (err) {
      // Ignore — the finally block below still hides the splash
      // and restores scrolling regardless of what went wrong.
    } finally {
      clearTimeout(safetyTimer);
      [splash1, splash2].forEach((s) => {
        if (!s) return;
        s.classList.add('is-hiding');
      });
      showPage();
      setTimeout(() => {
        [splash1, splash2].forEach((s) => {
          if (!s) return;
          s.style.display = 'none';
          s.setAttribute('aria-hidden', 'true');
        });
      }, 500);
    }
  })();
})();

// Black tracking box in the hero: follows the cursor while hovering
// over the name + sticker capsules (see .name-wrap / .scanner in
// styles.css). Stays out of the tagline below since that sits
// outside .name-wrap entirely.
(function initNameScanner() {
  const wrap = document.querySelector('.name-wrap');
  const box = wrap && wrap.querySelector('.scanner');
  if (!wrap || !box) return;

  let targetX = 0, targetY = 0, currentX = 0, currentY = 0;
  let raf = null;

  const lerp = (a, b, t) => a + (b - a) * t;

  const tick = () => {
    currentX = lerp(currentX, targetX, 0.12);
    currentY = lerp(currentY, targetY, 0.12);
    box.style.left = `${currentX}px`;
    box.style.top = `${currentY}px`;
    raf = requestAnimationFrame(tick);
  };

  const moveTo = (clientX, clientY) => {
    const rect = wrap.getBoundingClientRect();
    targetX = clientX - rect.left;
    targetY = clientY - rect.top;
    if (!raf) raf = requestAnimationFrame(tick);
  };

  wrap.addEventListener('mouseenter', (e) => moveTo(e.clientX, e.clientY));
  wrap.addEventListener('mousemove', (e) => moveTo(e.clientX, e.clientY));
  wrap.addEventListener('mouseleave', () => {
    if (raf) { cancelAnimationFrame(raf); raf = null; }
  });
})();

// Highlights the current section's link in the nav bar as you scroll.
const sections = document.querySelectorAll('main section[id]');
const navLinks = document.querySelectorAll('.navlinks a');

const setActiveLink = (id) => {
  navLinks.forEach((link) => {
    link.classList.toggle('active', link.getAttribute('href') === `#${id}`);
  });
};

if ('IntersectionObserver' in window && sections.length) {
  const navObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) setActiveLink(entry.target.id);
      });
    },
    { rootMargin: '-45% 0px -50% 0px', threshold: 0 }
  );
  sections.forEach((section) => navObserver.observe(section));
}

// Interactive certificates / badges tabs: switching between the two
// groups recolors each tab per its category (see styles.css).
(function initCertTabs() {
  const tabs = document.querySelectorAll('.cert-tab');
  const groups = document.querySelectorAll('.cert-group');
  if (!tabs.length || !groups.length) return;

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      const name = tab.dataset.group;
      tabs.forEach((t) => {
        const on = t === tab;
        t.classList.toggle('active', on);
        t.setAttribute('aria-pressed', String(on));
      });
      groups.forEach((g) => g.classList.toggle('hidden', g.dataset.group !== name));
    });
  });
})();

// Fades elements marked with .reveal up into place the first time
// they enter the viewport. Respects prefers-reduced-motion via CSS.
const revealTargets = document.querySelectorAll('.reveal');

if ('IntersectionObserver' in window && revealTargets.length) {
  const revealObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.15 }
  );
  revealTargets.forEach((el) => revealObserver.observe(el));
} else {
  revealTargets.forEach((el) => el.classList.add('in-view'));
}
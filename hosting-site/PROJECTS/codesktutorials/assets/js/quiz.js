'use strict';

/* ==========================================================
   CoDesk — quiz engine
   Expects window.QUIZ_DATA = [{ q, code, options, answer, explain }]
   ========================================================== */

(function () {
  const $ = (s) => document.querySelector(s);
  const $$ = (s) => Array.from(document.querySelectorAll(s));

  const data = window.QUIZ_DATA;
  if (!data || !data.length) return;

  const letters = ['A', 'B', 'C', 'D'];
  let questions = [];

  const el = {
    title: $('.quiz-card h1'),
    sub: $('.quiz-top p'),
    progress: $('.quiz-progress-fill'),
    num: $('.quiz-q-num'),
    question: $('.quiz-q'),
    options: $('.quiz-options'),
    explain: $('.quiz-explain'),
    prev: $('.btn-prev'),
    next: $('.btn-next'),
    body: $('.quiz-body'),
    result: $('.quiz-result'),
    card: $('.quiz-card')
  };

  let idx = 0;
  let score = 0;
  let answered = false;

  function shuffle(arr) {
    const a = arr.slice();
    for (let i = a.length - 1; i > 0; i--) {
      const j = (Math.random() * (i + 1)) | 0;
      [a[i], a[j]] = [a[j], a[i]];
    }
    return a;
  }

  function start() {
    questions = shuffle(data);
    idx = 0; score = 0; answered = false;
    el.title && (el.title.textContent = 'CoDesk Quiz');
    el.sub && (el.sub.textContent = '20 questions across JavaScript, Java & C#. Good luck!');
    el.result.style.display = 'none';
    el.body.style.display = 'block';
    el.next.style.display = 'inline-flex';
    el.prev.style.hidden = el.prev.style.hidden;
    el.prev.disabled = true;
    render();
  }

  function render() {
    const q = questions[idx];
    el.num.textContent = 'Question ' + (idx + 1) + ' of ' + questions.length;
    el.progress.style.width = ((idx) / questions.length) * 100 + '%';
    answered = false;

    el.question.innerHTML = '';
    if (q.code) {
      const code = document.createElement('div');
      code.className = 'quiz-code-line';
      code.innerHTML = '<code>' + q.code + '</code>';
      el.question.appendChild(code);
    }
    const text = document.createElement('span');
    text.innerHTML = q.q;
    el.question.appendChild(text);

    el.options.innerHTML = '';
    q.options.forEach((opt, i) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'quiz-opt';
      btn.innerHTML = '<span class="letter">' + letters[i] + '</span><span class="opt-text">' + opt + '</span>';
      btn.addEventListener('click', () => { if (!answered) choose(i, btn); });
      el.options.appendChild(btn);
    });

    el.explain.classList.remove('show', 'ok', 'no');
    el.explain.innerHTML = '';
    el.prev.disabled = idx === 0;
    el.next.disabled = true;
    el.next.textContent = idx === questions.length - 1 ? 'Finish' : 'Next';
  }

  function choose(i, btn) {
    const q = questions[idx];
    answered = true;
    const options = $$('.quiz-opt');
    options.forEach((o) => o.classList.add('locked'));

    if (i === q.answer) score++;
    q.options.forEach((opt, j) => {
      const elBtn = options[j];
      if (j === q.answer) elBtn.classList.add('correct');
      else if (j === i) elBtn.classList.add('wrong');
    });

    const ok = i === q.answer;
    el.explain.classList.add('show', ok ? 'ok' : 'no');
    el.explain.textContent = (ok ? 'Correct! ' + (q.explain || '') : 'Not quite. ' + (q.explain || ''));
    el.next.disabled = false;
    el.next.focus();
  }

  function next() {
    if (idx === questions.length - 1) return finish();
    idx++;
    render();
  }
  function prev() {
    if (idx > 0) { idx--; render(); }
  }

  function finish() {
    el.progress.style.width = '100%';
    el.body.style.display = 'none';
    el.result.style.display = 'block';
    el.card.setAttribute('data-finished', '1');
    el.title && (el.title.textContent = 'Quiz Complete');
    el.sub && (el.sub.textContent = '');

    const pct = Math.round((score / questions.length) * 100);

    let msg, sub;
    if (pct >= 90) { msg = 'Outstanding!'; sub = 'You are a CoDesk champion!'; }
    else if (pct >= 70) { msg = 'Great job!'; sub = 'Solid fundamentals — keep going!'; }
    else if (pct >= 50) { msg = 'Good effort!'; sub = 'Review the lessons and try again.'; }
    else { msg = 'Keep practicing!'; sub = 'Go back through the lessons and give it another shot.'; }

    const ring = $('.score-ring');
    ring.style.setProperty('--p', pct);

    const b = $('.score-inner b');
    b.innerHTML = score + '<span>out of ' + questions.length + '</span>';
    // re-parse for inline span style
    b.innerHTML = score + '<span>out of ' + questions.length + '</span>';

    $('.quiz-result h2').textContent = msg;
    $('.quiz-result p').textContent = sub;
    $('.btn-restart').addEventListener('click', start);

    if (pct >= 50 && typeof burstConfetti === 'function') burstConfetti();
  }

  el.next.addEventListener('click', next);
  el.prev.addEventListener('click', prev);
  start();
})();
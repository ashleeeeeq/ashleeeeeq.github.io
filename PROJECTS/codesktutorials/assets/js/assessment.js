'use strict';

/* ==========================================================
   CoDesk — assessment (fill-in-the-blank code) logic
   Each blank is an <input class="assess-input" data-answers="...">.
   data-answers holds acceptable answers, comma separated.
   ========================================================== */

(function () {
  const $ = (s, c) => (c || document).querySelector(s);
  const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));

  const card = $('.assess-card[data-course]');
  if (!card) return;

  const inputs = $$('.assess-input', card);
  const checkBtn = $('.btn-check', card);
  const feedback = $('.assess-feedback', card);
  const dataMsg = $('.assess-feedback .msg');
  const successMsg = card.getAttribute('data-success') || 'All correct — you nailed it!';
  const failMsg = card.getAttribute('data-fail') || 'Almost there! Check the hint and try again.';

  const startMsg = card.getAttribute('data-start') || 'Type in the missing code, then press Check.';

  if (feedback && dataMsg) dataMsg.textContent = startMsg;

  const sanitize = (v) => (v || '').trim().replace(/\s+/g, ' ');

  function checkAll() {
    let allCorrect = true;
    const correctTotal = inputs.length;

    inputs.forEach((input) => {
      const acceptable = (input.getAttribute('data-answers') || '')
        .split(',')
        .map(sanitize)
        .filter(Boolean);
      const ok = acceptable.includes(sanitize(input.value));
      input.classList.remove('correct', 'wrong');
      if (ok) {
        input.classList.add('correct');
        input.setAttribute('readonly', true);
      } else {
        input.classList.add('wrong');
        allCorrect = false;
      }
    });

    if (!feedback || !dataMsg) return;

    feedback.classList.add('show');
    if (allCorrect && correctTotal > 0) {
      feedback.classList.remove('no');
      feedback.classList.add('ok');
      dataMsg.textContent = successMsg;
      if (typeof burstConfetti === 'function') burstConfetti();
    } else {
      feedback.classList.remove('ok');
      feedback.classList.add('no');
      dataMsg.textContent = failMsg;
      if (typeof showToast === 'function') {
        const wrong = inputs.filter((i) => i.classList.contains('wrong')).length;
        showToast(wrong + ' blank' + (wrong > 1 ? 's' : '') + ' to fix', 'no');
      }
    }

    // reveal a hint if requested and still wrong
    if (!allCorrect) {
      const hint = $('.assess-hint', card);
      if (hint) hint.style.display = 'block';
    }
  }

  if (checkBtn) checkBtn.addEventListener('click', checkAll);

  inputs.forEach((input) => {
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') { e.preventDefault(); checkAll(); }
    });
    input.addEventListener('input', () => {
      if (input.classList.contains('wrong')) input.classList.remove('wrong');
      if (feedback) feedback.classList.remove('show');
      const hint = $('.assess-hint', card);
      if (hint) hint.style.display = 'none';
    });
  });
})();
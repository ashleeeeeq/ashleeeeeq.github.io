'use strict';

/* ==========================================================
   CoDesk — lesson page JS
   Runs on lesson pages and the landing page course lists.
   Handles: code-block chrome + syntax highlight, progress
   tracking (localStorage), sidebar & progress bars.
   ========================================================== */

(function () {
  const $ = (s, c) => (c || document).querySelector(s);
  const $$ = (s, c) => Array.from((c || document).querySelectorAll(s));

  const langName = { js: 'JavaScript', java: 'Java', csharp: 'C#' };
  const langId = { javascript: 'js', java: 'java', csharp: 'csharp' };

  const KEY = (course) => 'codesk-progress-' + course;

  const getProgress = (course) => {
    try { return JSON.parse(localStorage.getItem(KEY(course)) || '[]'); }
    catch (e) { return []; }
  };
  const saveProgress = (course, arr) => localStorage.setItem(KEY(course), JSON.stringify(arr));

  /* ---------- code block chrome + highlighting ---------- */
  const KW = [
    'class','public','private','protected','static','void','int','double','float','string',
    'boolean','bool','char','var','const','let','new','return','if','else','elseif','elif',
    'switch','case','break','default','for','foreach','while','do','function','extends',
    'import','using','namespace','this','super','true','false','null','try','catch','throw',
    'echo','print','typeof','instanceof'
  ];
  const FN = ['console','write','Write','WriteLine','System','main','java','abs','max','min','sqrt','pow','random','getElementById','getElement','innerHTML'];

  function highlight(text, lang) {
    const esc = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    const tokenRe = /(\/\/[^\n]*|\/\*[\s\S]*?\*\/|'[^'\n]*'|"[^"\n]*"|`[^`]*`|\b\d+(?:\.\d+)?\b|[A-Za-z_$][\w$]*|\+|-|\*|\/|%|&amp;&amp;|\|\||===|!==|==|!=|<=|>=|={1,2}|&lt;|&gt;|!)/g;
    return esc.replace(tokenRe, (m) => {
      if (/^(\/\/|\/\*)/.test(m)) return '<span class="tok-cmt">' + m + '</span>';
      if (/^['"`]/.test(m)) return '<span class="tok-str">' + m + '</span>';
      if (/^\d/.test(m)) return '<span class="tok-num">' + m + '</span>';
      if (/^[+\-*/%&|!=<>]/.test(m)) return '<span class="tok-op">' + m + '</span>';
      if (KW.includes(m)) return '<span class="tok-kw">' + m + '</span>';
      if (FN.some((f) => m.includes(f))) return '<span class="tok-fn">' + m + '</span>';
      return m;
    });
  }

  $$('.code-block[data-lang]').forEach((block) => {
    const lang = langName[block.getAttribute('data-lang')] || block.getAttribute('data-lang');
    const codeEl = block.querySelector('code') || block;
    const raw = codeEl.innerText || codeEl.textContent;

    const bar = document.createElement('div');
    bar.className = 'code-block__bar';
    bar.innerHTML = '<span class="lang">' + lang + '</span>';

    const copyBtn = document.createElement('button');
    copyBtn.className = 'copy-btn';
    copyBtn.type = 'button';
    copyBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg><span>Copy</span>';
    copyBtn.addEventListener('click', () => copyText(raw, copyBtn));
    bar.appendChild(copyBtn);

    block.prepend(bar);
    if (codeEl.tagName === 'CODE') {
      codeEl.innerHTML = highlight(raw, lang);
    } else {
      block.innerHTML = block.innerHTML.replace(/<pre[^>]*>/i, '') ;
    }
  });

  /* ---------- progress tracking ---------- */
  const layout = $('[data-course]');
  if (layout) {
    const course = layout.getAttribute('data-course');
    const currentLesson = parseInt(layout.getAttribute('data-lesson'), 10) || 1;
    const total = parseInt(layout.getAttribute('data-total'), 10) || 5;

    const arr = getProgress(course);
    if (!arr.includes(currentLesson)) { arr.push(currentLesson); saveProgress(course, arr); }

    // mark sidebar links
    $$('.side-link').forEach((a) => {
      const n = parseInt(a.getAttribute('data-lesson'), 10);
      if (!isNaN(n)) {
        if (n === currentLesson) a.classList.add('current');
        if (arr.includes(n) && n !== currentLesson) a.classList.add('done');
      }
    });

    // sidebar progress bar
    const fill = $('.side-progress .bar');
    if (fill) {
      const pct = (arr.filter((n) => n <= total).length / total) * 100;
      fill.style.width = pct + '%';
      const row = $('.side-progress .row');
      if (row) {
        const count = row.querySelector('.count');
        if (count) count.textContent = arr.filter((n) => n <= total).length + ' / ' + total;
      }
    }
    window.dispatchEvent(new CustomEvent('codesk-progress', { detail: { course } }));
  }

  /* ---------- landing page course widgets ---------- */
  $$('[data-course-widget]').forEach((widget) => {
    const course = widget.getAttribute('data-course-widget');
    const arr = getProgress(course);
    const total = parseInt(widget.getAttribute('data-total'), 10) || 5;

    widget.querySelectorAll('.lesson-list a').forEach((a) => {
      const n = parseInt(a.getAttribute('data-lesson'), 10);
      if (!isNaN(n) && arr.includes(n)) {
        a.classList.add('done');
        const ck = a.querySelector('.check');
        if (ck) ck.style.display = 'inline-block';
      }
      const ar = a.querySelector('.arrow');
      if (ar) ar.style.display = 'inline-block';
    });

    const bar = widget.querySelector('.course-progress .bar');
    if (bar) {
      const pct = (arr.filter((n) => n <= total).length / total) * 100;
      bar.style.width = pct + '%';
      const countEl = widget.querySelector('.course-progress .count');
      if (countEl) countEl.textContent = arr.filter((n) => n <= total).length + '/' + total;
    }
  });
})();
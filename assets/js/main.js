/**
 * Amorsum Theme — 交互脚本
 * WordPress 现代科技风主题
 */
(function () {
  'use strict';

  // ============================================
  // 1. 主题切换 (Dark / Light)
  // ============================================
  const themeToggle = document.getElementById('themeToggle');
  const html = document.documentElement;

  themeToggle?.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    html.setAttribute('data-theme', next);
    localStorage.setItem('amorsum-theme', next);

    // 尝试通过 AJAX 保存到用户 meta（仅登录用户）
    if (typeof amorsumAjax !== 'undefined' && amorsumAjax.ajaxurl) {
      fetch(amorsumAjax.ajaxurl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'action=amorsum_save_theme&theme=' + next + '&_wpnonce=' + (amorsumAjax.nonce || ''),
      }).catch(() => {});
    }
  });

  // ============================================
  // 2. 打字机效果 (Hero)
  // ============================================
  const typewriterEl = document.getElementById('heroTypewriter');
  const typewriterData = document.getElementById('heroTypewriterData');

  if (typewriterEl && typewriterData) {
    try {
      const texts = JSON.parse(typewriterData.textContent);
      if (texts.length > 1) {
        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;

        function type() {
          const currentText = texts[textIndex];

          if (isDeleting) {
            charIndex--;
            typewriterEl.textContent = currentText.substring(0, charIndex);
          } else {
            charIndex++;
            typewriterEl.textContent = currentText.substring(0, charIndex);
          }

          let speed = isDeleting ? 40 : 80;

          if (!isDeleting && charIndex === currentText.length) {
            speed = 2000;
            isDeleting = true;
          } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            textIndex = (textIndex + 1) % texts.length;
            speed = 400;
          }

          setTimeout(type, speed);
        }

        setTimeout(type, 1000);
      }
    } catch (e) {}
  }

  // ============================================
  // 3. 实时搜索
  // ============================================
  const searchBtn    = document.getElementById('searchBtn');
  const searchPanel  = document.getElementById('searchPanel');
  const searchInput  = document.getElementById('liveSearchInput');
  const searchResults = document.getElementById('searchResults');

  let searchTimer = null;
  let searchAbort = null;

  // 开关面板
  function openSearchPanel() {
    searchPanel?.classList.add('is-open');
    document.body.classList.add('search-is-open');
    setTimeout(() => searchInput?.focus(), 150);
  }
  function closeSearchPanel() {
    searchPanel?.classList.remove('is-open');
    document.body.classList.remove('search-is-open');
    if (searchInput) searchInput.value = '';
    clearResults();
  }
  function clearResults() {
    if (searchResults) searchResults.innerHTML = '';
    if (searchTimer) clearTimeout(searchTimer);
    if (searchAbort) { searchAbort.abort(); searchAbort = null; }
  }

  searchBtn?.addEventListener('click', () => {
    if (searchPanel?.classList.contains('is-open')) {
      closeSearchPanel();
    } else {
      openSearchPanel();
    }
  });

  // 输入关键词 → 防抖 300ms 后发起 AJAX 搜索
  searchInput?.addEventListener('input', function () {
    const q = this.value.trim();
    if (searchTimer) clearTimeout(searchTimer);
    if (searchAbort) { searchAbort.abort(); searchAbort = null; }

    if (q.length < 2) {
      searchResults.innerHTML = '';
      return;
    }

    searchResults.innerHTML = '<div class="search-results__loading"><span class="search-results__spinner"></span></div>';

    searchTimer = setTimeout(() => {
      searchAbort = new AbortController();
      const url = (typeof amorsumAjax !== 'undefined' && amorsumAjax.ajaxurl
        ? amorsumAjax.ajaxurl
        : '/wp-admin/admin-ajax.php')
        + '?action=amorsum_live_search&_wpnonce='
        + (typeof amorsumAjax !== 'undefined' ? amorsumAjax.nonce : '')
        + '&q=' + encodeURIComponent(q);

      fetch(url, { signal: searchAbort.signal })
        .then(r => r.json())
        .then(res => {
          if (!res.success || !res.data) return;
          renderResults(res.data.results, res.data.query);
        })
        .catch(err => {
          if (err.name !== 'AbortError') {
            searchResults.innerHTML = '<div class="search-results__empty">搜索出错了，请重试</div>';
          }
        });
    }, 300);
  });

  // 渲染结果列表
  function renderResults(results, query) {
    if (!searchResults) return;

    if (!results || results.length === 0) {
      searchResults.innerHTML = '<div class="search-results__empty"><i class="ph ph-smiley-sad"></i> 未找到相关内容</div>';
      return;
    }

    let html = '';
    results.forEach(r => {
      const suffix = r.in_title
        ? '<span class="search-results__date">' + escapeHtml(r.date) + '</span>'
        : '<span class="search-results__snippet">' + escapeHtml(r.excerpt) + '</span>';

      html += '<a href="' + escapeAttr(r.url) + '" class="search-results__item">'
        + '<span class="search-results__title">' + highlightKeyword(r.title, query) + '</span>'
        + suffix
        + '</a>';
    });

    searchResults.innerHTML = html;
  }

  // 关键词高亮
  function highlightKeyword(text, keyword) {
    const escaped = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    const regex = new RegExp('(' + escaped + ')', 'gi');
    return text.replace(regex, '<mark class="search-results__mark">$1</mark>');
  }

  function escapeHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
  }
  function escapeAttr(str) {
    return str.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  // ESC 关闭
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && searchPanel?.classList.contains('is-open')) {
      closeSearchPanel();
      searchBtn?.focus();
    }
  });

  // 点击面板外部关闭
  document.addEventListener('click', e => {
    if (searchPanel?.classList.contains('is-open')
        && !searchPanel.contains(e.target)
        && e.target !== searchBtn
        && !searchBtn?.contains(e.target)) {
      closeSearchPanel();
    }
  });

  // 点击结果项 → 关闭面板
  searchResults?.addEventListener('click', e => {
    if (e.target.closest('.search-results__item')) {
      closeSearchPanel();
    }
  });

  // ============================================
  // 4. 滚动动画 (Intersection Observer)
  // ============================================
  const animatedElements = document.querySelectorAll('.animate-fade-up');

  if (animatedElements.length > 0 && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1, rootMargin: '0px 0px -40px 0px' }
    );

    animatedElements.forEach((el) => observer.observe(el));
  } else {
    animatedElements.forEach((el) => el.classList.add('is-visible'));
  }

  // ============================================
  // 5. 阅读进度条
  // ============================================
  const progressBar = document.getElementById('progressBar');

  if (progressBar) {
    function updateProgress() {
      const scrollTop = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const progress = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      progressBar.style.setProperty('--progress', Math.min(progress, 100) + '%');
    }

    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
  }

  // ============================================
  // 6. Header 滚动状态
  // ============================================
  const header = document.querySelector('.header');

  if (header) {
    function updateHeader() {
      header.classList.toggle('scrolled', window.scrollY > 10);
    }

    window.addEventListener('scroll', updateHeader, { passive: true });
    updateHeader();
  }

  // ============================================
  // 7. 移动端菜单
  // ============================================
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  mobileMenuBtn?.addEventListener('click', () => {
    const isOpen = mobileMenu.classList.toggle('is-open');
    const icon = mobileMenuBtn.querySelector('i');
    if (icon) {
      icon.className = isOpen ? 'ph ph-x' : 'ph ph-list';
    }
  });

  mobileMenu?.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      mobileMenu.classList.remove('is-open');
      const icon = mobileMenuBtn?.querySelector('i');
      if (icon) icon.className = 'ph ph-list';
    });
  });

  // ============================================
  // 8. TOC 目录高亮
  // ============================================
  const tocLinks = document.querySelectorAll('.toc a');

  if (tocLinks.length > 0) {
    const headings = [];
    tocLinks.forEach((link) => {
      const href = link.getAttribute('href');
      if (href) {
        const id = href.replace('#', '');
        const heading = document.getElementById(id);
        if (heading) headings.push({ el: heading, link });
      }
    });

    function updateToc() {
      let current = null;
      const scrollTop = window.scrollY + 100;

      for (const { el, link } of headings) {
        if (el.offsetTop <= scrollTop) current = link;
      }

      tocLinks.forEach((link) => link.classList.remove('active'));
      if (current) current.classList.add('active');
    }

    window.addEventListener('scroll', updateToc, { passive: true });
    updateToc();
  }

  // ============================================
  // 9. 代码块复制按钮
  // ============================================
  document.querySelectorAll('.post__article pre, .page__content pre').forEach((pre) => {
    const code = pre.querySelector('code');
    if (!code) return;

    // 添加语言标签
    const langClass = Array.from(code.classList).find(c => c.startsWith('language-'));
    if (langClass) {
      pre.setAttribute('data-language', langClass.replace('language-', ''));
    }

    // 添加复制按钮
    const copyBtn = document.createElement('button');
    copyBtn.className = 'code-copy-btn';
    copyBtn.innerHTML = '<i class="ph ph-copy"></i>';
    copyBtn.setAttribute('aria-label', '复制代码');
    pre.style.position = 'relative';
    pre.appendChild(copyBtn);

    copyBtn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(code.textContent);
        copyBtn.innerHTML = '<i class="ph ph-check"></i>';
        copyBtn.classList.add('copied');
        setTimeout(() => {
          copyBtn.innerHTML = '<i class="ph ph-copy"></i>';
          copyBtn.classList.remove('copied');
        }, 2000);
      } catch (err) {
        // 降级：fallback
        const textarea = document.createElement('textarea');
        textarea.value = code.textContent;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        copyBtn.innerHTML = '<i class="ph ph-check"></i>';
        setTimeout(() => {
          copyBtn.innerHTML = '<i class="ph ph-copy"></i>';
        }, 2000);
      }
    });
  });

  // ============================================
  // 10. 图片懒加载
  // ============================================
  if ('loading' in HTMLImageElement.prototype) {
    document.querySelectorAll('.post__article img:not([loading]), .page__content img:not([loading])').forEach((img) => {
      img.setAttribute('loading', 'lazy');
    });
  }

  // ============================================
  // 11. 外部链接新窗口打开
  // ============================================
  document.querySelectorAll('.post__article a[href^="http"], .page__content a[href^="http"]').forEach((link) => {
    const host = window.location.host;
    try {
      const linkHost = new URL(link.href).host;
      if (linkHost !== host) {
        link.setAttribute('target', '_blank');
        link.setAttribute('rel', 'noopener noreferrer');
      }
    } catch (e) {}
  });

  // ============================================
  // 12. 响应式表格（包裹 scroll wrapper）
  // ============================================
  document.querySelectorAll('.post__article table, .page__content table').forEach((table) => {
    if (!table.parentElement.classList.contains('table-wrapper')) {
      const wrapper = document.createElement('div');
      wrapper.className = 'table-wrapper';
      wrapper.style.cssText = 'overflow-x:auto;margin:1.5em 0;-webkit-overflow-scrolling:touch;';
      table.parentNode.insertBefore(wrapper, table);
      wrapper.appendChild(table);
    }
  });

  // ============================================
  // 12. 背景视频 → Canvas 渲染
  // 不往 DOM 里放 <video>，浏览器就不会劫持/投屏/弹控件
  // ============================================
  const bgCanvas = document.getElementById('bgCanvas');

  if (bgCanvas) {
    const videoSrc = bgCanvas.getAttribute('data-src');
    if (!videoSrc) {
      document.body.classList.add('bg-video-fallback');
    } else {
      const ctx = bgCanvas.getContext('2d');
      let bgVideoEl = null;
      let rafId = null;
      let running = false;

      // Canvas 分辨率与视口严格对齐，避免画面变形
      function resize() {
        bgCanvas.width  = window.innerWidth;
        bgCanvas.height = window.innerHeight;
      }

      function draw() {
        if (!running || !bgVideoEl) return;
        try {
          const vw = bgVideoEl.videoWidth;
          const vh = bgVideoEl.videoHeight;
          if (vw && vh) {
            // object-fit: cover 逻辑 — 保持视频比例，填满画布，居中裁剪
            const cw = bgCanvas.width;
            const ch = bgCanvas.height;
            const scale = Math.max(cw / vw, ch / vh);
            const dw = vw * scale;
            const dh = vh * scale;
            const dx = (cw - dw) / 2;
            const dy = (ch - dh) / 2;
            ctx.drawImage(bgVideoEl, dx, dy, dw, dh);
          }
        } catch(e) {}
        rafId = requestAnimationFrame(draw);
      }

      function start() {
        running = true;
        draw();
      }

      function stop() {
        running = false;
        if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
      }

      function init() {
        // 创建离屏 video（永远不插入 DOM）
        bgVideoEl = document.createElement('video');
        bgVideoEl.muted     = true;
        bgVideoEl.loop      = true;
        bgVideoEl.playsInline = true;
        bgVideoEl.preload   = 'metadata';
        bgVideoEl.src       = videoSrc;

        bgVideoEl.addEventListener('loadeddata', () => {
          resize();
          bgVideoEl.play().then(() => {
            start();
            bgCanvas.style.opacity = '1';
          }).catch(() => {
            document.body.classList.add('bg-video-fallback');
          });
        });

        bgVideoEl.addEventListener('error', () => {
          document.body.classList.add('bg-video-fallback');
        });

        bgVideoEl.load();
      }

      // 初始状态
      bgCanvas.style.opacity = '0';
      bgCanvas.style.transition = 'opacity 1.5s ease';
      resize();

      // 等页面加载完再启动，不抢首屏带宽
      if (document.readyState === 'complete') {
        setTimeout(init, 500);
      } else {
        window.addEventListener('load', () => setTimeout(init, 500));
      }

      // 窗口大小变化时更新画布
      window.addEventListener('resize', resize);

      // 切到后台暂停绘制，省电
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) stop(); else start();
      });
    }
  }

  // ============================================
  // 13. 页面切换滑动过渡（根据页面层级判断方向）
  // ============================================
  const appMain = document.querySelector('.app__main');

  // 计算页面路径深度：首页=0, 归档=1, 分类=2, 关于=3, 文章/其他=路径段数+3
  function getPageDepth(pathname) {
    var p = pathname.replace(/^\/index\.php/, '').replace(/\/$/, '') || '/';
    var map = { '/': 0, '/archives': 1, '/categories': 2, '/about': 3 };
    if (map[p] !== undefined) return map[p];
    return p.split('/').filter(Boolean).length + 3;
  }

  // 进入动画：从内部链接跳转过来时播放
  if (appMain && sessionStorage.getItem('amorsum-transition') === 'slide') {
    var dir = sessionStorage.getItem('amorsum-direction') || 'forward';
    sessionStorage.removeItem('amorsum-transition');
    sessionStorage.removeItem('amorsum-direction');
    var enterClass = dir === 'back' ? 'app__main--entering-back' : 'app__main--entering';
    appMain.classList.add(enterClass);
    appMain.addEventListener('animationend', function () {
      appMain.classList.remove(enterClass);
    }, { once: true });
  }

  // 浏览器回退时清除残留
  window.addEventListener('pageshow', function (e) {
    if (e.persisted) {
      sessionStorage.removeItem('amorsum-transition');
      sessionStorage.removeItem('amorsum-direction');
      if (appMain) appMain.classList.remove('app__main--entering', 'app__main--entering-back', 'app__main--leaving', 'app__main--leaving-back');
    }
  });

  // 拦截内部链接点击
  document.addEventListener('click', function (e) {
    var link = e.target.closest('a');
    if (!link) return;

    var href = link.getAttribute('href');
    if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0) return;
    if (link.getAttribute('target') === '_blank') return;
    if (e.metaKey || e.ctrlKey || e.shiftKey) return;
    if (e.target.closest('#searchPanel')) return;

    var linkHost, targetPath;
    try {
      var url = new URL(href, window.location.origin);
      linkHost = url.host;
      targetPath = url.pathname;
    } catch(ex) { return; }
    if (linkHost !== window.location.host) return;
    if (href.indexOf('wp-admin') !== -1 || href.indexOf('wp-login') !== -1) return;

    e.preventDefault();

    // 判断方向：目标更深 → forward(向左滑)，目标更浅 → back(向右滑)
    var fromDepth = getPageDepth(window.location.pathname);
    var toDepth   = getPageDepth(targetPath);
    var isBack    = toDepth < fromDepth;

    if (appMain) {
      var leaveClass = isBack ? 'app__main--leaving-back' : 'app__main--leaving';
      appMain.classList.add(leaveClass);
      sessionStorage.setItem('amorsum-transition', 'slide');
      sessionStorage.setItem('amorsum-direction', isBack ? 'back' : 'forward');

      var go = function () { window.location = href; };
      appMain.addEventListener('animationend', go, { once: true });
      setTimeout(function () {
        if (sessionStorage.getItem('amorsum-transition') === 'slide') {
          go();
        }
      }, 300);
    } else {
      window.location = href;
    }
  });

  // 开发环境日志
  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('%c🚀 Amorsum Theme %cLoaded', 'color: #7c5cff; font-weight: bold;', 'color: #9a9ab0;');
  }
})();

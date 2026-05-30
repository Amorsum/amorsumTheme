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
  // 3. 搜索弹出层
  // ============================================
  const searchBtn = document.getElementById('searchBtn');
  const searchOverlay = document.getElementById('searchOverlay');
  const searchClose = document.getElementById('searchClose');

  searchBtn?.addEventListener('click', () => {
    searchOverlay?.classList.add('is-open');
    // 聚焦搜索框
    setTimeout(() => {
      const input = searchOverlay?.querySelector('input[type="search"]');
      input?.focus();
    }, 100);
  });

  searchClose?.addEventListener('click', () => {
    searchOverlay?.classList.remove('is-open');
  });

  // ESC 关闭
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && searchOverlay?.classList.contains('is-open')) {
      searchOverlay.classList.remove('is-open');
    }
  });

  // 点击遮罩关闭
  searchOverlay?.addEventListener('click', (e) => {
    if (e.target === searchOverlay) {
      searchOverlay.classList.remove('is-open');
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

  console.log('%c🚀 Amorsum Theme %cLoaded', 'color: #7c5cff; font-weight: bold;', 'color: #9a9ab0;');
})();

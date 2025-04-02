/**
 * PPA Modern Design System
 * JS functionality for modernized pages
 */

// 当DOM加载完成时执行
document.addEventListener('DOMContentLoaded', function() {
  // 初始化滚动监听
  initScrollObserver();
  
  // 导航栏滚动效果
  initNavbarScroll();
  
  // 初始化工具提示
  initTooltips();
  
  // 初始化返回顶部按钮
  initBackToTop();
  
  // 平滑滚动
  initSmoothScroll();
  
  // 添加计数器动画
  initCounters();
  
  // 加载谷歌字体
  loadGoogleFonts();
  
  // 初始化淡入动画
  initFadeInElements();
  
  // 初始化表单验证
  initFormValidation();
  
  // 初始化联系表单
  initContactForm();
});

/**
 * 滚动监听器初始化
 * 用于触发元素的淡入动画
 */
function initScrollObserver() {
  // 如果浏览器支持IntersectionObserver
  if ('IntersectionObserver' in window) {
    const fadeElements = document.querySelectorAll('.fade-in');
    
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          // 一旦元素出现就停止观察
          observer.unobserve(entry.target);
        }
      });
    }, {
      root: null, // 相对于视口
      rootMargin: '0px',
      threshold: 0.1 // 元素10%可见时触发
    });
    
    // 开始观察所有淡入元素
    fadeElements.forEach(element => {
      observer.observe(element);
    });
  } else {
    // 降级处理：直接显示所有元素
    document.querySelectorAll('.fade-in').forEach(element => {
      element.classList.add('active');
    });
  }
}

/**
 * 导航栏滚动效果
 */
function initNavbarScroll() {
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  }
}

/**
 * 初始化Bootstrap工具提示
 */
function initTooltips() {
  // 检查Bootstrap是否可用
  if (typeof bootstrap !== 'undefined') {
    // 初始化所有工具提示
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }
}

/**
 * 初始化返回顶部按钮
 */
function initBackToTop() {
  // 创建返回顶部按钮
  const backToTopBtn = document.createElement('button');
  backToTopBtn.id = 'back-to-top';
  backToTopBtn.className = 'btn btn-primary position-fixed shadow d-none';
  backToTopBtn.style.width = '56px';
  backToTopBtn.style.height = '56px';
  backToTopBtn.style.right = '20px';
  backToTopBtn.style.bottom = '20px';
  backToTopBtn.style.zIndex = '1000';
  backToTopBtn.style.borderRadius = '50%';
  backToTopBtn.style.padding = '0';
  backToTopBtn.style.display = 'flex';
  backToTopBtn.style.alignItems = 'center';
  backToTopBtn.style.justifyContent = 'center';
  backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
  document.body.appendChild(backToTopBtn);
  
  // 添加点击事件
  backToTopBtn.addEventListener('click', function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
  
  // 滚动时显示/隐藏按钮
  window.addEventListener('scroll', function() {
    if (window.scrollY > 300) {
      backToTopBtn.classList.remove('d-none');
    } else {
      backToTopBtn.classList.add('d-none');
    }
  });
}

/**
 * 初始化平滑滚动
 */
function initSmoothScroll() {
  const scrollLinks = document.querySelectorAll('a[href^="#"]:not([href="#"])');
  
  scrollLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      const targetId = this.getAttribute('href');
      const targetElement = document.querySelector(targetId);
      
      if (targetElement) {
        targetElement.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
}

/**
 * 初始化数字计数动画
 */
function initCounters() {
  const counters = document.querySelectorAll('.counter');
  
  // 如果有计数器元素
  if (counters.length > 0) {
    // 创建IntersectionObserver
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const counter = entry.target;
          const target = parseInt(counter.getAttribute('data-target'));
          const duration = parseInt(counter.getAttribute('data-duration') || 2000);
          let start = 0;
          const increment = target / (duration / 16);
          
          const updateCounter = () => {
            start += increment;
            if (start < target) {
              counter.innerText = Math.floor(start);
              requestAnimationFrame(updateCounter);
            } else {
              counter.innerText = target;
            }
          };
          
          updateCounter();
          observer.unobserve(counter);
        }
      });
    }, {
      threshold: 0.5
    });
    
    // 开始观察所有计数器
    counters.forEach(counter => {
      observer.observe(counter);
    });
  }
}

/**
 * 加载Google Fonts
 */
function loadGoogleFonts() {
  // 创建字体链接元素
  const fontLink = document.createElement('link');
  fontLink.href = 'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap';
  fontLink.rel = 'stylesheet';
  document.head.appendChild(fontLink);
}

/**
 * 视差滚动效果
 */
function initParallax() {
  const parallaxElements = document.querySelectorAll('.parallax');
  
  if (parallaxElements.length > 0) {
    window.addEventListener('scroll', function() {
      const scrollTop = window.scrollY;
      
      parallaxElements.forEach(element => {
        const speed = element.getAttribute('data-speed') || 0.5;
        element.style.transform = `translateY(${scrollTop * speed}px)`;
      });
    });
  }
}

/**
 * 实用函数: 防抖
 */
function debounce(func, wait, immediate) {
  let timeout;
  return function() {
    const context = this, args = arguments;
    clearTimeout(timeout);
    timeout = setTimeout(function() {
      timeout = null;
      if (!immediate) func.apply(context, args);
    }, wait);
    if (immediate && !timeout) func.apply(context, args);
  };
}

// 淡入动画处理
function initFadeInElements() {
  const fadeElements = document.querySelectorAll('.fade-in');
  
  if (fadeElements.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.animationPlayState = 'running';
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1
  });

  fadeElements.forEach(element => {
    element.style.animationPlayState = 'paused';
    observer.observe(element);
  });
}

// 表单验证
function initFormValidation() {
  const forms = document.querySelectorAll('form');
  
  forms.forEach(form => {
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
      // 添加验证样式
      field.addEventListener('blur', function() {
        validateField(this);
      });
      
      field.addEventListener('input', function() {
        if (this.classList.contains('is-invalid')) {
          validateField(this);
        }
      });
    });
    
    // 表单提交验证
    form.addEventListener('submit', function(e) {
      let isValid = true;
      
      requiredFields.forEach(field => {
        if (!validateField(field)) {
          isValid = false;
        }
      });
      
      if (!isValid) {
        e.preventDefault();
      }
    });
  });
}

// 字段验证
function validateField(field) {
  let isValid = true;
  
  if (field.value.trim() === '') {
    field.classList.add('is-invalid');
    isValid = false;
  } else {
    field.classList.remove('is-invalid');
    
    // Email 验证
    if (field.type === 'email' && !validateEmail(field.value)) {
      field.classList.add('is-invalid');
      isValid = false;
    }
    
    // 电话验证
    if (field.type === 'tel' && !validatePhone(field.value)) {
      field.classList.add('is-invalid');
      isValid = false;
    }
  }
  
  return isValid;
}

// Email 验证
function validateEmail(email) {
  const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(String(email).toLowerCase());
}

// 电话验证
function validatePhone(phone) {
  // 简单电话验证，可根据需要调整为特定国家的格式
  const re = /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/;
  return re.test(String(phone));
}

// 联系表单处理
function initContactForm() {
  const contactForm = document.getElementById('contactForm');
  
  if (!contactForm) return;

  contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // 显示提交中状态
    const submitBtn = contactForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
    submitBtn.disabled = true;
    
    // 模拟表单提交 (在实际应用中会发送到服务器)
    setTimeout(() => {
      // 恢复按钮状态
      submitBtn.innerHTML = originalBtnText;
      submitBtn.disabled = false;
      
      // 显示成功消息
      showFormMessage('Your message has been sent successfully! We will get back to you shortly.', 'success');
      
      // 重置表单
      contactForm.reset();
    }, 1500);
  });
}

// 显示表单消息
function showFormMessage(message, type = 'success') {
  // 检查是否已有消息框
  let alertBox = document.querySelector('.alert-message');
  
  if (!alertBox) {
    // 创建消息框
    alertBox = document.createElement('div');
    alertBox.className = `alert alert-${type} alert-dismissible fade show alert-message`;
    alertBox.setAttribute('role', 'alert');
    
    // 创建关闭按钮
    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'btn-close';
    closeButton.setAttribute('data-bs-dismiss', 'alert');
    closeButton.setAttribute('aria-label', 'Close');
    
    // 添加到消息框
    alertBox.appendChild(document.createTextNode(message));
    alertBox.appendChild(closeButton);
    
    // 查找表单
    const form = document.getElementById('contactForm');
    form.parentNode.insertBefore(alertBox, form);
    
    // 5秒后自动消失
    setTimeout(() => {
      alertBox.classList.remove('show');
      
      setTimeout(() => {
        alertBox.remove();
      }, 300);
    }, 5000);
  } else {
    // 更新现有消息
    alertBox.className = `alert alert-${type} alert-dismissible fade show alert-message`;
    alertBox.textContent = message;
  }
} 
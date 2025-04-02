/**
 * 主要JavaScript文件
 * 包含网站通用功能
 */

// 当DOM加载完成时执行
document.addEventListener('DOMContentLoaded', function() {
    // 初始化Bootstrap工具提示
    initTooltips();
    
    // 初始化回到顶部按钮
    initBackToTop();
    
    // 平滑滚动
    initSmoothScroll();
    
    // 用户菜单交互
    const userDropdown = document.getElementById('userDropdown');
    const dropdownMenu = userDropdown.querySelector('.dropdown-menu');

    // 点击用户头像显示/隐藏菜单
    userDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
    });

    // 点击页面其他地方关闭菜单
    document.addEventListener('click', function(e) {
        if (!userDropdown.contains(e.target)) {
            dropdownMenu.classList.remove('show');
        }
    });

    // 移动端侧边栏切换
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.user-sidebar');
    const content = document.querySelector('.user-content');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            content.classList.toggle('sidebar-open');
        });
    }
});

/**
 * 初始化Bootstrap工具提示
 */
function initTooltips() {
    // 检查Bootstrap是否可用
    if (typeof bootstrap !== 'undefined') {
        // 初始化所有工具提示
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * 初始化回到顶部按钮
 */
function initBackToTop() {
    // 创建回到顶部按钮
    var backToTopBtn = document.createElement('button');
    backToTopBtn.id = 'back-to-top';
    backToTopBtn.className = 'btn btn-primary position-fixed bottom-0 end-0 m-4 rounded-circle d-none';
    backToTopBtn.style.width = '50px';
    backToTopBtn.style.height = '50px';
    backToTopBtn.style.zIndex = '1000';
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
        if (window.pageYOffset > 300) {
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
    // 获取所有带有hash链接的锚点
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            // 获取目标元素
            const target = document.querySelector(this.getAttribute('href'));
            
            // 如果目标存在，实现平滑滚动
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * 实用函数: 检测元素是否在视口中
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * 实用函数: 防抖
 */
function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

// 处理车辆表单提交
document.addEventListener('DOMContentLoaded', function() {
    const addVehicleForm = document.getElementById('addVehicleForm');
    
    if (addVehicleForm) {
        addVehicleForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('api/vehicles.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // 关闭模态框
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addVehicleModal'));
                    modal.hide();
                    
                    // 刷新页面显示新车辆
                    window.location.reload();
                } else {
                    alert(result.message || 'An error occurred while adding the vehicle.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while adding the vehicle.');
            }
        });
    }
});

// 编辑车辆
function editVehicle(vehicleId) {
    // 获取车辆数据
    fetch(`api/vehicles.php?id=${vehicleId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const vehicle = data.vehicle;
                // 在这里实现编辑逻辑
                // 例如：打开编辑模态框并填充数据
            } else {
                alert(data.message || 'Failed to fetch vehicle data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching vehicle data.');
        });
} 
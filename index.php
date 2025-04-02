<?php
/**
 * 网站主页
 */

// 设置当前页面为首页
$current_page = 'home';
$page_title = ''; // 首页不需要额外的标题

// 引入配置文件
require_once 'config/config.php';

// 引入初始化文件 (包含所有类文件和用户会话)
require_once 'init.php';

// 引入公共函数
require_once 'includes/functions.php';

// 添加额外的CSS和JS文件
$additional_css = ['modern.css'];
$additional_js = ['modern.js'];

// 包含页面头部
include 'includes/header.php';
?>

<!-- 现代化英雄区 -->
<section class="hero-modern">
    <div class="hero-pattern-overlay"></div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-in">
                <h1 class="hero-title">Malaysia's <span>First</span> Additional Car Protection</h1>
                <p class="hero-subtitle">Experience premium vehicle protection beyond standard insurance with our innovative subscription plans.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo url('plans.php'); ?>" class="btn btn-primary btn-lg"><?php echo __('btn_subscribe'); ?></a>
                    <a href="<?php echo url('pages/how-it-works.php'); ?>" class="btn btn-outline-light btn-lg"><?php echo __('btn_learn_more'); ?></a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block fade-in" style="transition-delay: 0.2s;">
                <div class="text-center position-relative">
                    <img src="<?php echo asset('images/ppa-logo-white.png'); ?>" alt="PPA Logo" class="hero-logo img-fluid">
                    <div class="hero-logo-glow"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- 装饰性背景元素 -->
    <div class="hero-shape-1"></div>
    <div class="hero-shape-2"></div>
</section>

<style>
/* 英雄区域增强样式 */
.hero-modern {
    background-color: var(--bs-primary);
    background-image: linear-gradient(135deg, #4361ee, #3a0ca3);
    color: white;
    position: relative;
    overflow: hidden;
    padding: 100px 0;
    z-index: 1;
}

.hero-pattern-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('<?php echo asset('images/hero-pattern.svg'); ?>');
    background-size: cover;
    opacity: 0.15;
    z-index: -1;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.hero-title span {
    color: #ffd166;
    position: relative;
    display: inline-block;
}

.hero-title span:after {
    content: '';
    position: absolute;
    bottom: 8px;
    left: 0;
    width: 100%;
    height: 8px;
    background-color: rgba(255, 209, 102, 0.3);
    z-index: -1;
}

.hero-subtitle {
    font-size: 1.25rem;
    margin-bottom: 2rem;
    opacity: 0.9;
    max-width: 600px;
}

.hero-logo {
    max-width: 400px;
    filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.15));
    animation: float 6s ease-in-out infinite;
}

.hero-logo-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    height: 80%;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 70%);
    z-index: -1;
    animation: pulse 4s ease-in-out infinite;
}

.hero-shape-1 {
    position: absolute;
    top: -100px;
    right: -100px;
    width: 300px;
    height: 300px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.hero-shape-2 {
    position: absolute;
    bottom: -150px;
    left: -150px;
    width: 400px;
    height: 400px;
    background-color: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
}

@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}

@keyframes pulse {
    0% { opacity: 0.5; }
    50% { opacity: 0.8; }
    100% { opacity: 0.5; }
}

@media (max-width: 991px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-modern {
        padding: 80px 0;
    }
}
</style>

<!-- 核心数据统计 -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="bg-light rounded-4 shadow-sm p-4">
                    <h2 class="display-4 fw-bold text-primary counter" data-target="5000">0</h2>
                    <p class="mb-0 text-muted">Happy Customers</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="bg-light rounded-4 shadow-sm p-4">
                    <h2 class="display-4 fw-bold text-primary counter" data-target="98">0</h2>
                    <p class="mb-0 text-muted">Satisfaction Rate</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="bg-light rounded-4 shadow-sm p-4">
                    <h2 class="display-4 fw-bold text-primary counter" data-target="150">0</h2>
                    <p class="mb-0 text-muted">Partner Workshops</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bg-light rounded-4 shadow-sm p-4">
                    <h2 class="display-4 fw-bold text-primary counter" data-target="24">0</h2>
                    <p class="mb-0 text-muted">Hour Support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 现代化特色内容区域 -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5 fade-in">
            <h2 class="display-5 fw-bold mb-3"><?php echo __('home_benefits_title'); ?></h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Experience unparalleled peace of mind with our comprehensive vehicle protection plans designed specifically for Malaysian drivers.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 fade-in">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Comprehensive Coverage</h3>
                    <p>Protection beyond standard insurance policies, covering wear and tear, minor accidents, and more.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: 0.1s;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3 class="feature-title">Cost-Effective Plans</h3>
                    <p>Affordable monthly subscriptions that save you money on unexpected car repairs and maintenance.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: 0.2s;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <h3 class="feature-title">Quick Processing</h3>
                    <p>Fast and efficient claims processing with minimal paperwork and hassle-free approvals.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: 0.1s;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="feature-title">Quality Repairs</h3>
                    <p>Access to our network of certified workshops and skilled technicians across Malaysia.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: 0.2s;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Easy Management</h3>
                    <p>Manage your protection plan, submit claims, and track repair status online or via mobile.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 fade-in" style="transition-delay: 0.3s;">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p>Round-the-clock customer service to assist you with any queries or emergencies.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 如何运作区域 -->
<section class="py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 fade-in">
                <h2 class="display-5 fw-bold mb-4">How Our Protection Plan Works</h2>
                
                <div class="d-flex mb-4">
                    <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">1</div>
                    <div class="ms-3">
                        <h4>Choose Your Plan</h4>
                        <p class="text-muted mb-0">Select from our Basic or Premium protection plans based on your vehicle needs and budget.</p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">2</div>
                    <div class="ms-3">
                        <h4>Subscribe Monthly</h4>
                        <p class="text-muted mb-0">Pay a small monthly fee to keep your vehicle protected against unexpected damages and repairs.</p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">3</div>
                    <div class="ms-3">
                        <h4>Submit Claims Easily</h4>
                        <p class="text-muted mb-0">When you need repairs, simply submit a claim online with photos and basic information.</p>
                    </div>
                </div>
                
                <div class="d-flex mb-4">
                    <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">4</div>
                    <div class="ms-3">
                        <h4>Get Quality Repairs</h4>
                        <p class="text-muted mb-0">Visit any of our partner workshops for professional repairs covered by your protection plan.</p>
                    </div>
                </div>
                
                <a href="<?php echo url('pages/how-it-works.php'); ?>" class="btn btn-primary mt-3"><?php echo __('btn_learn_more'); ?></a>
            </div>
            
            <div class="col-lg-6 mt-5 mt-lg-0 fade-in">
                <div class="position-relative">
                    <div class="position-absolute top-0 start-0 translate-middle bg-primary rounded-circle" style="width: 150px; height: 150px; opacity: 0.1; z-index: -1;"></div>
                    <div class="position-absolute bottom-0 end-0 translate-middle bg-secondary rounded-circle" style="width: 200px; height: 200px; opacity: 0.1; z-index: -1;"></div>
                    <img src="https://assets.codepen.io/7989306/workshop-repair.jpg" alt="How it works" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 客户评价区域 -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="text-center mb-5 fade-in">
            <h2 class="display-5 fw-bold mb-3">What Our Customers Say</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">Join thousands of satisfied car owners across Malaysia</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 fade-in">
                <div class="testimonial-card h-100">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"When my car had unexpected issues with the air conditioning system, the protection plan saved me thousands in repair costs. The claim process was amazingly simple!"</p>
                    <div class="client-info">
                        <div class="client-image">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Ahmad" class="img-fluid">
                        </div>
                        <div>
                            <h5 class="client-name">Ahmad Bin Ismail</h5>
                            <p class="client-location">Kuala Lumpur</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 fade-in" style="transition-delay: 0.1s;">
                <div class="testimonial-card h-100">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"I was initially skeptical about the value, but after experiencing their quick response when my car's electrical system failed, I'm now a believer. Worth every ringgit!"</p>
                    <div class="client-info">
                        <div class="client-image">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Michelle" class="img-fluid">
                        </div>
                        <div>
                            <h5 class="client-name">Michelle Wong</h5>
                            <p class="client-location">Penang</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 fade-in" style="transition-delay: 0.2s;">
                <div class="testimonial-card h-100">
                    <div class="testimonial-rating">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="testimonial-text">"As a busy professional, I appreciate how they've simplified the whole process. Their partner workshops provided excellent service when my car needed transmission repairs."</p>
                    <div class="client-info">
                        <div class="client-image">
                            <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Raj" class="img-fluid">
                        </div>
                        <div>
                            <h5 class="client-name">Raj Kumar</h5>
                            <p class="client-location">Johor Bahru</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 合作伙伴区域 -->
<section class="py-5">
    <div class="container py-4">
        <div class="text-center mb-5 fade-in">
            <h2 class="display-6 fw-bold mb-3">Our Trusted Partners</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">We collaborate with leading workshops and service providers across Malaysia</p>
        </div>
        
        <div class="row justify-content-center align-items-center g-4 fade-in">
            <div class="col-4 col-md-2">
                <div class="bg-light p-4 rounded-4 text-center">
                    <img src="https://via.placeholder.com/150x75?text=Partner+1" alt="Partner 1" class="img-fluid">
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="bg-light p-4 rounded-4 text-center">
                    <img src="https://via.placeholder.com/150x75?text=Partner+2" alt="Partner 2" class="img-fluid">
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="bg-light p-4 rounded-4 text-center">
                    <img src="https://via.placeholder.com/150x75?text=Partner+3" alt="Partner 3" class="img-fluid">
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="bg-light p-4 rounded-4 text-center">
                    <img src="https://via.placeholder.com/150x75?text=Partner+4" alt="Partner 4" class="img-fluid">
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="bg-light p-4 rounded-4 text-center">
                    <img src="https://via.placeholder.com/150x75?text=Partner+5" alt="Partner 5" class="img-fluid">
                </div>
            </div>
            <div class="col-4 col-md-2">
                <div class="bg-light p-4 rounded-4 text-center">
                    <img src="https://via.placeholder.com/150x75?text=Partner+6" alt="Partner 6" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 行动号召区域 -->
<section class="divider-section">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-8 fade-in">
                <h2 class="display-5 fw-bold">Ready to protect your vehicle beyond standard insurance?</h2>
                <p class="lead mb-lg-0">Join thousands of satisfied customers who enjoy peace of mind with our protection plans</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0 fade-in" style="transition-delay: 0.1s;">
                <a href="<?php echo url('plans.php'); ?>" class="btn btn-light btn-lg"><?php echo __('btn_subscribe'); ?></a>
            </div>
        </div>
    </div>
</section>

<?php
// 包含页面页脚
include 'includes/footer.php';
?>

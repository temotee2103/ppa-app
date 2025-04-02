<?php
// About Us Page
require_once '../init.php';
$pageTitle = "About Us | Malaysia's 1st Additional Car Protection";
$current_page = 'about';

// 添加额外的CSS和JS文件
$additional_css = ['modern.css'];
$additional_js = ['modern.js'];

include_once("../includes/header.php");
?>

<!-- 现代化页面标题区 -->
<div class="page-header bg-primary position-relative overflow-hidden">
  <div class="page-header-pattern"></div>
  <div class="container py-5 position-relative">
    <div class="row align-items-center">
      <div class="col-lg-7 text-white mb-4 mb-lg-0">
        <h1 class="display-4 fw-bold mb-3 fade-in">About Us</h1>
        <p class="lead opacity-90 mb-4 fade-in" style="transition-delay: 0.1s;">Learn about our company and our mission to protect Malaysian drivers</p>
      </div>
      <div class="col-lg-5 d-none d-lg-block fade-in" style="transition-delay: 0.3s;">
      </div>
    </div>
  </div>
  <!-- 装饰元素 -->
  <div class="header-shape-1"></div>
  <div class="header-shape-2"></div>
</div>

<style>
/* 现代化页面标题区样式 */
.page-header {
  background-image: linear-gradient(135deg, #4361ee, #3a0ca3);
  z-index: 1;
}

.page-header-pattern {
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

.header-logo {
  max-height: 200px;
  filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.15));
}

.header-shape-1 {
  position: absolute;
  top: -50px;
  right: -50px;
  width: 200px;
  height: 200px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  z-index: -1;
}

.header-shape-2 {
  position: absolute;
  bottom: -80px;
  left: -80px;
  width: 250px;
  height: 250px;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
  z-index: -1;
}

/* 玻璃卡片效果 */
.glass-card {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  border-radius: 20px;
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  overflow: hidden;
  transition: all 0.3s ease;
}

.glass-card:hover {
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  transform: translateY(-5px);
}

/* 渐变背景区域 */
.gradient-section {
  background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(58, 12, 163, 0.08));
  position: relative;
  overflow: hidden;
}

.section-pattern-dots {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url('<?php echo asset('images/pattern-dots.svg'); ?>');
  opacity: 0.4;
  z-index: 0;
}

/* 团队成员卡片 */
.team-card {
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  height: 100%;
}

.team-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.team-card .member-img {
  position: relative;
  overflow: hidden;
}

.team-card .member-img img {
  transition: all 0.5s ease;
  width: 100%;
}

.team-card:hover .member-img img {
  transform: scale(1.05);
}

.team-card .member-info {
  padding: 25px;
}

.social-icons a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  background: rgba(67, 97, 238, 0.1);
  color: #4361ee;
  border-radius: 50%;
  transition: all 0.3s ease;
}

.social-icons a:hover {
  background: #4361ee;
  color: white;
}

/* 核心价值卡片 */
.value-card {
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  height: 100%;
  padding: 30px;
}

.value-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.value-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  color: #4361ee;
  font-size: 1.75rem;
}
</style>

<!-- 公司故事部分 -->
<div class="container py-5">
  <div class="row g-5">
    <div class="col-lg-8 fade-in">
      <h2 class="display-4 fw-bold mb-4">Our Story</h2>
      
      <div class="mt-4">
        <p class="mb-4">Malaysia's 1st Additional Car Protection was founded in 2020 with a simple mission: to provide comprehensive vehicle protection beyond standard insurance at an affordable price.</p>
        <p class="mb-4">Our founders, experienced professionals from the automotive and insurance industries, identified a significant gap in the market. While traditional car insurance provides coverage for accidents and theft, it often leaves car owners vulnerable to expensive repair costs for mechanical and electrical failures.</p>
        <p class="mb-4">After two years of research and planning, we launched our first protection plans designed specifically for Malaysian drivers. Today, we're proud to serve thousands of customers across the country, helping them avoid unexpected repair costs and enjoy worry-free driving.</p>
        <p>Our company continues to grow, but our core values remain the same: transparency, reliability, and exceptional customer service. We're committed to continuously improving our offerings and expanding our partner workshop network to better serve Malaysian drivers.</p>
      </div>
    </div>
    
    <div class="col-lg-4 fade-in" style="transition-delay: 0.2s;">
      <div class="d-flex flex-column justify-content-center align-items-center h-100" style="min-height: 300px;">
        <img src="<?php echo asset('images/ppa-logo-blue.png'); ?>" alt="PPA Logo" class="img-fluid" style="max-width: 400px;">
        
        <div class="position-absolute" style="z-index: -1;">
          <div class="bg-light rounded-circle position-absolute" style="width: 250px; height: 250px; top: -120px; right: -150px; opacity: 0.4;"></div>
          <div class="bg-light rounded-circle position-absolute" style="width: 300px; height: 300px; bottom: -150px; left: -150px; opacity: 0.2;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 核心价值部分 -->
<div class="gradient-section py-5">
  <div class="section-pattern-dots"></div>
  <div class="container py-4 position-relative">
    <div class="text-center mb-5 fade-in">
      <h2 class="display-5 fw-bold mb-3">Our Core Values</h2>
      <p class="lead text-muted mx-auto" style="max-width: 700px;">The principles that guide everything we do</p>
    </div>
    
    <div class="row g-4">
      <div class="col-md-4 fade-in">
        <div class="value-card text-center">
          <div class="value-icon">
            <i class="fas fa-handshake"></i>
          </div>
          <h3 class="mb-3">Trust & Integrity</h3>
          <p class="mb-0 text-muted">We believe in complete transparency and honesty in all our dealings. Our customers trust us because we deliver on our promises.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in" style="transition-delay: 0.1s;">
        <div class="value-card text-center">
          <div class="value-icon">
            <i class="fas fa-users"></i>
          </div>
          <h3 class="mb-3">Customer Focus</h3>
          <p class="mb-0 text-muted">Everything we do is designed with our customers in mind. We continuously seek feedback to improve our services and meet evolving needs.</p>
        </div>
      </div>
      <div class="col-md-4 fade-in" style="transition-delay: 0.2s;">
        <div class="value-card text-center">
          <div class="value-icon">
            <i class="fas fa-award"></i>
          </div>
          <h3 class="mb-3">Excellence</h3>
          <p class="mb-0 text-muted">We are committed to excellence in every aspect of our business, from our protection plans to our customer service and workshop partnerships.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 合作车间网络部分 -->
<div class="gradient-section py-5">
  <div class="section-pattern-dots"></div>
  <div class="container py-4 position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 fade-in">
        <h2 class="display-5 fw-bold mb-4">Our Workshop Partner Network</h2>
        <p class="lead mb-4">We've built a comprehensive network of certified automotive workshops across Malaysia to ensure our customers receive high-quality repairs and excellent service.</p>
        <p class="mb-4">Each partner workshop undergoes a rigorous vetting process and must meet our strict quality standards. We regularly audit our partners to maintain service excellence and customer satisfaction.</p>
        
        <div class="glass-card p-4 mb-4">
          <div class="row g-4">
            <div class="col-6">
              <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                  <i class="fas fa-check"></i>
                </div>
                <span class="ms-3">150+ partner workshops</span>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                  <i class="fas fa-check"></i>
                </div>
                <span class="ms-3">All major Malaysian cities</span>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                  <i class="fas fa-check"></i>
                </div>
                <span class="ms-3">Certified technicians</span>
              </div>
            </div>
            <div class="col-6">
              <div class="d-flex align-items-center">
                <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px;">
                  <i class="fas fa-check"></i>
                </div>
                <span class="ms-3">Quality guaranteed repairs</span>
              </div>
            </div>
          </div>
        </div>
        
        <a href="#" class="btn btn-primary btn-lg rounded-pill px-4">Find A Workshop Near You</a>
      </div>
      <div class="col-lg-6 fade-in" style="transition-delay: 0.2s;">
        <div class="glass-card overflow-hidden shadow-lg">
          <div id="workshopMap" style="height: 400px; background-color: #e9ecef;">
            <!-- In a real application, this would be an interactive map -->
            <img src="../assets/images/workshop-map.jpg" alt="Workshop Locations" class="img-fluid w-100 h-100 object-fit-cover">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 行动召唤部分 -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 fade-in">
      <div class="card border-0 bg-primary text-white shadow-lg position-relative overflow-hidden rounded-4">
        <div class="cta-pattern"></div>
        <div class="card-body p-5 text-center position-relative">
          <h2 class="fw-bold mb-3">Join Our Growing Family of Protected Drivers</h2>
          <p class="lead mb-4">Experience peace of mind with Malaysia's first comprehensive vehicle protection plan</p>
          <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <a href="../register.php?plan=premium" class="btn btn-light btn-lg shadow-sm rounded-pill px-4">Subscribe Now</a>
            <a href="../plans.php" class="btn btn-outline-light btn-lg rounded-pill px-4">Explore Plans</a>
          </div>
        </div>
        <div class="cta-shape-1"></div>
        <div class="cta-shape-2"></div>
      </div>
    </div>
  </div>
</div>

<style>
/* CTA 卡片增强样式 */
.cta-pattern {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-image: url('<?php echo asset('images/hero-pattern.svg'); ?>');
  background-size: cover;
  opacity: 0.05;
  z-index: 0;
}

.cta-shape-1 {
  position: absolute;
  top: -30px;
  right: -30px;
  width: 120px;
  height: 120px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  z-index: 0;
}

.cta-shape-2 {
  position: absolute;
  bottom: -40px;
  left: -40px;
  width: 150px;
  height: 150px;
  background-color: rgba(255, 255, 255, 0.05);
  border-radius: 50%;
  z-index: 0;
}

.glass-overlay {
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
  backdrop-filter: blur(5px);
}
</style>

<?php
include_once("../includes/footer.php");
?> 
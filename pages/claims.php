<?php
// Claims Page
require_once '../init.php';
$pageTitle = "Claims | Malaysia's 1st Additional Car Protection";
$current_page = 'claims';
$page_title = 'Claims Process';

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
        <h1 class="display-4 fw-bold mb-3 fade-in">Claims Process</h1>
        <p class="lead opacity-90 mb-4 fade-in" style="transition-delay: 0.1s;">Simple and fast claims processing for all your protection needs</p>
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

/* 磨砂玻璃卡片 */
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

/* 步骤卡片样式 */
.step-card {
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  height: 100%;
}

.step-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.step-number {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #4361ee, #3a0ca3);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0 auto 20px;
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
</style>

<!-- 3步骤申请流程 -->
<div class="container py-5">
  <div class="text-center mb-5 fade-in">
    <h2 class="display-5 fw-bold mb-3">Simple 3-Step Claims Process</h2>
    <p class="lead text-muted mx-auto" style="max-width: 700px;">We've simplified the claims process to get your vehicle back on the road quickly</p>
  </div>
  
  <div class="row g-4">
    <div class="col-md-4 fade-in">
      <div class="step-card p-4 text-center">
        <div class="step-number">1</div>
        <h3 class="mb-3">Submit Claim Online</h3>
        <p class="text-muted mb-0">Log in to your customer portal and submit a claim with details and photos of the issue</p>
        <img src="../assets/images/submit-claim.jpg" alt="Submit Claim" class="img-fluid rounded-4 mt-4">
      </div>
    </div>
    
    <div class="col-md-4 fade-in" style="transition-delay: 0.1s;">
      <div class="step-card p-4 text-center">
        <div class="step-number">2</div>
        <h3 class="mb-3">Quick Approval</h3>
        <p class="text-muted mb-0">Receive claim approval within 24-48 hours with confirmation and next steps</p>
        <img src="../assets/images/approved.jpg" alt="Quick Approval" class="img-fluid rounded-4 mt-4">
      </div>
    </div>
    
    <div class="col-md-4 fade-in" style="transition-delay: 0.2s;">
      <div class="step-card p-4 text-center">
        <div class="step-number">3</div>
        <h3 class="mb-3">Get Repairs Done</h3>
        <p class="text-muted mb-0">Visit any of our partner workshops for professional repairs covered by your plan</p>
        <img src="../assets/images/repair-work.jpg" alt="Get Repairs" class="img-fluid rounded-4 mt-4">
      </div>
    </div>
  </div>
</div>

<!-- 客户登录提示 -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 fade-in">
      <div class="glass-card p-4 p-lg-5 border-0 text-center">
        <div class="icon-circle bg-primary text-white mx-auto mb-4">
          <i class="fas fa-user-shield fa-2x"></i>
        </div>
        <h2 class="fw-bold mb-3">Submit Claims in Your Customer Portal</h2>
        <p class="lead mb-4">For existing members, please log in to your customer account to submit and track claims. Our secure portal provides a streamlined process with faster approval times.</p>
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
          <a href="../login.php" class="btn btn-primary btn-lg shadow-sm rounded-pill px-5">
            <i class="fas fa-sign-in-alt me-2"></i>Log In to Submit a Claim
          </a>
          <a href="../register.php" class="btn btn-outline-primary btn-lg rounded-pill px-4">
            <i class="fas fa-user-plus me-2"></i>Register for an Account
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 详细流程 -->
<div class="gradient-section py-5">
  <div class="section-pattern-dots"></div>
  <div class="container py-4 position-relative">
    <div class="row mb-5">
      <div class="col-lg-6 fade-in">
        <h2 class="display-6 fw-bold mb-4">Before Filing a Claim</h2>
        <p class="lead mb-4">To ensure a smooth claims process, please have the following information ready:</p>
        
        <div class="glass-card p-4 mb-3">
          <div class="d-flex">
            <div class="flex-shrink-0 me-3">
              <div class="icon-circle bg-primary text-white">
                <i class="fas fa-car fa-lg"></i>
              </div>
            </div>
            <div>
              <h5>Vehicle Information</h5>
              <p class="mb-0 text-muted">Your vehicle registration number, make, model, and year</p>
            </div>
          </div>
        </div>
        
        <div class="glass-card p-4 mb-3">
          <div class="d-flex">
            <div class="flex-shrink-0 me-3">
              <div class="icon-circle bg-primary text-white">
                <i class="fas fa-calendar-alt fa-lg"></i>
              </div>
            </div>
            <div>
              <h5>Incident Details</h5>
              <p class="mb-0 text-muted">Date when the issue occurred and a detailed description of the problem</p>
            </div>
          </div>
        </div>
        
        <div class="glass-card p-4 mb-3">
          <div class="d-flex">
            <div class="flex-shrink-0 me-3">
              <div class="icon-circle bg-primary text-white">
                <i class="fas fa-camera fa-lg"></i>
              </div>
            </div>
            <div>
              <h5>Photos of Damage</h5>
              <p class="mb-0 text-muted">Clear photos showing the damaged or malfunctioning parts</p>
            </div>
          </div>
        </div>
        
        <div class="glass-card p-4">
          <div class="d-flex">
            <div class="flex-shrink-0 me-3">
              <div class="icon-circle bg-primary text-white">
                <i class="fas fa-file-invoice fa-lg"></i>
              </div>
            </div>
            <div>
              <h5>Member Details</h5>
              <p class="mb-0 text-muted">Your membership number and contact information</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6 mt-5 mt-lg-0 fade-in">
        <div class="glass-card p-4 mb-4">
          <h3 class="mb-3">Eligible Claims</h3>
          <p class="mb-3">Our protection plans primarily cover accident-related claims, including:</p>
          <ul class="list-unstyled">
            <li class="d-flex align-items-center mb-3">
              <div class="icon-circle icon-circle-sm bg-success text-white me-3">
                <i class="fas fa-check"></i>
              </div>
              <span>Traffic accident damages (including third-party collisions)</span>
            </li>
            <li class="d-flex align-items-center mb-3">
              <div class="icon-circle icon-circle-sm bg-success text-white me-3">
                <i class="fas fa-check"></i>
              </div>
              <span>One-time compensation for accident-related damages</span>
            </li>
            <li class="d-flex align-items-center mb-3">
              <div class="icon-circle icon-circle-sm bg-success text-white me-3">
                <i class="fas fa-check"></i>
              </div>
              <span>Replacement vehicle during repair period</span>
            </li>
            <li class="d-flex align-items-center mb-3">
              <div class="icon-circle icon-circle-sm bg-success text-white me-3">
                <i class="fas fa-check"></i>
              </div>
              <span>Towing services to nearest workshop</span>
            </li>
            <li class="d-flex align-items-center mb-3">
              <div class="icon-circle icon-circle-sm bg-success text-white me-3">
                <i class="fas fa-check"></i>
              </div>
              <span>Natural disaster damage (flood, fire, etc.)</span>
            </li>
            <li class="d-flex align-items-center">
              <div class="icon-circle icon-circle-sm bg-success text-white me-3">
                <i class="fas fa-check"></i>
              </div>
              <span>Additional coverage for Premium Plan members</span>
            </li>
          </ul>
        </div>
        
        <div class="glass-card p-4">
          <h3 class="mb-3">Non-Eligible Claims</h3>
          <p class="mb-3">The following issues are not covered by our protection plans:</p>
          <ul class="list-unstyled">
            <li class="d-flex align-items-center mb-3">
              <div class="icon-circle icon-circle-sm bg-danger text-white me-3">
                <i class="fas fa-times"></i>
              </div>
              <span>Intentional damage or sabotage</span>
            </li>
            <li class="d-flex align-items-center mb-3">
              <div class="icon-circle icon-circle-sm bg-danger text-white me-3">
                <i class="fas fa-times"></i>
              </div>
              <span>Damage resulting from illegal activities</span>
            </li>
            <li class="d-flex align-items-center mb-3">
              <div class="icon-circle icon-circle-sm bg-danger text-white me-3">
                <i class="fas fa-times"></i>
              </div>
              <span>Pre-existing damages before plan activation</span>
            </li>
            <li class="d-flex align-items-center">
              <div class="icon-circle icon-circle-sm bg-danger text-white me-3">
                <i class="fas fa-times"></i>
              </div>
              <span>Excessive wear and tear from neglect</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    
    <div class="text-center mt-5 fade-in">
      <a href="../register.php" class="btn btn-primary btn-lg rounded-pill px-5 py-3">
        <i class="fas fa-shield-alt me-2"></i>Sign Up for Protection
      </a>
    </div>
  </div>
</div>

<!-- 部分见证 -->
<?php include_once("../includes/success-stories.php"); ?>

<!-- 行动号召 -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 fade-in">
      <div class="card border-0 bg-primary text-white shadow-lg position-relative overflow-hidden rounded-4">
        <div class="cta-pattern"></div>
        <div class="card-body p-5 text-center position-relative">
          <h2 class="fw-bold mb-3">Ready to protect your vehicle beyond standard insurance?</h2>
          <p class="lead mb-4">Join thousands of satisfied customers who enjoy peace of mind with our protection plans</p>
          <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <a href="../register.php?plan=premium" class="btn btn-light btn-lg shadow-sm rounded-pill px-4">Subscribe Now</a>
            <a href="../plans.php" class="btn btn-outline-light btn-lg rounded-pill px-4">View Plans</a>
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

/* 表单控件样式 */
.form-control, .form-select {
  border-radius: 10px;
  padding: 12px 15px;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

.form-control:focus, .form-select:focus {
  box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.25);
  border-color: #4361ee;
}

/* 图标圆圈组件 */
.icon-circle {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

/* 小尺寸图标圆圈 */
.icon-circle-sm {
  width: 36px;
  height: 36px;
  font-size: 0.9rem;
}

/* 图标悬停效果 */
.glass-card:hover .icon-circle {
  transform: scale(1.1);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* 输入组样式增强 */
.input-group-text {
  border: none;
}

/* 按钮悬停效果 */
.btn-primary {
  transition: all 0.3s ease;
}

.btn-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(var(--bs-primary-rgb), 0.25);
}
</style>

<?php
include_once("../includes/footer.php");
?> 
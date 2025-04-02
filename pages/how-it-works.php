<?php
// How It Works Page
require_once '../init.php';

// 设置当前页面
$current_page = 'how_it_works';
$page_title = "How It Works";

// 添加额外的CSS和JS文件
$additional_css = ['modern.css'];
$additional_js = ['modern.js'];

include_once("../includes/header.php");
?>

<!-- 现代化页面标题区 -->
<div class="page-header gradient-blue position-relative overflow-hidden">
  <div class="page-header-pattern"></div>
  <div class="container py-5 position-relative">
    <div class="row align-items-center">
      <div class="col-lg-7 text-white mb-4 mb-lg-0">
        <h1 class="display-4 fw-bold mb-3 fade-in">How It Works</h1>
        <p class="lead opacity-90 mb-4 fade-in" style="transition-delay: 0.1s;">Learn how our car protection plan works to keep your vehicle protected</p>
        <a href="<?php echo url('./pages/plans.php'); ?>" class="btn btn-light btn-lg px-4 fade-in" style="transition-delay: 0.2s;">View Protection Plans</a>
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
</style>

<!-- 运作流程概述 -->
<div class="container py-5">
  <div class="text-center mb-5 fade-in">
    <h2 class="display-5 fw-bold mb-3 text-primary">Simple 4-Step Process</h2>
    <p class="lead text-muted mx-auto" style="max-width: 700px;">Our protection plan is designed to be straightforward and hassle-free, giving you peace of mind</p>
  </div>
  
  <div class="row g-4">
    <div class="col-md-6 col-lg-3 fade-in">
      <div class="process-card frosted-glass text-center h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden hover-float">
        <div class="card-bg-shape"></div>
        <div class="process-icon mb-4 hover-rotate-icon">
          <div class="bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
            <i class="fas fa-hand-pointer fa-2x"></i>
          </div>
        </div>
        <h4 class="mb-3 text-primary">Choose Your Plan</h4>
        <p class="text-muted mb-0">Select either the Basic or Premium protection plan based on your vehicle needs and budget.</p>
        <div class="process-number">1</div>
      </div>
    </div>
    
    <div class="col-md-6 col-lg-3 fade-in" style="transition-delay: 0.1s;">
      <div class="process-card frosted-glass text-center h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden hover-float">
        <div class="card-bg-shape"></div>
        <div class="process-icon mb-4 hover-rotate-icon">
          <div class="bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
            <i class="fas fa-credit-card fa-2x"></i>
          </div>
        </div>
        <h4 class="mb-3 text-primary">Subscribe Monthly</h4>
        <p class="text-muted mb-0">Pay a small monthly fee to keep your vehicle protected against unexpected damages and repairs.</p>
        <div class="process-number">2</div>
      </div>
    </div>
    
    <div class="col-md-6 col-lg-3 fade-in" style="transition-delay: 0.2s;">
      <div class="process-card frosted-glass text-center h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden hover-float">
        <div class="card-bg-shape"></div>
        <div class="process-icon mb-4 hover-rotate-icon">
          <div class="bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
            <i class="fas fa-clipboard-list fa-2x"></i>
          </div>
        </div>
        <h4 class="mb-3 text-primary">Submit Claims Easily</h4>
        <p class="text-muted mb-0">When you need repairs, simply submit a claim online with photos and basic information.</p>
        <div class="process-number">3</div>
      </div>
    </div>
    
    <div class="col-md-6 col-lg-3 fade-in" style="transition-delay: 0.3s;">
      <div class="process-card frosted-glass text-center h-100 border-0 shadow-sm rounded-4 p-4 position-relative overflow-hidden hover-float">
        <div class="card-bg-shape"></div>
        <div class="process-icon mb-4 hover-rotate-icon">
          <div class="bg-primary text-white rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 80px; height: 80px;">
            <i class="fas fa-tools fa-2x"></i>
          </div>
        </div>
        <h4 class="mb-3 text-primary">Get Quality Repairs</h4>
        <p class="text-muted mb-0">Visit any of our partner workshops for professional repairs covered by your protection plan.</p>
        <div class="process-number">4</div>
      </div>
    </div>
  </div>
</div>

<style>
/* 流程卡片增强样式 */
.process-card {
  transition: all 0.3s ease;
}

.process-card:hover {
  transform: translateY(-10px);
}

.card-bg-shape {
  position: absolute;
  bottom: 0;
  right: 0;
  width: 150px;
  height: 150px;
  background-color: rgba(var(--bs-primary-rgb), 0.03);
  border-radius: 50%;
  z-index: -1;
  transform: translate(30%, 30%);
}

.process-number {
  position: absolute;
  top: 15px;
  right: 15px;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background-color: rgba(var(--bs-primary-rgb), 0.1);
  color: var(--bs-primary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}
</style>

<!-- 详细过程解释 -->
<div class="container-fluid gradient-light-blue position-relative overflow-hidden py-5">
  <div class="section-pattern-dots"></div>
  <div class="container py-4">
    <div class="row align-items-center mb-5">
      <div class="col-lg-6 mb-4 mb-lg-0 fade-in">
        <h2 class="fw-bold mb-4 text-primary">1. Choose Your Protection Plan</h2>
        <p class="mb-4">We offer two comprehensive protection plans designed to fit different needs and budgets:</p>
        
        <div class="mb-4 frosted-glass p-3 rounded-4 hover-glow">
          <h5><i class="fas fa-check-circle text-primary me-2"></i> Basic Plan (RM 99/month)</h5>
          <p class="text-muted ms-4 ps-2 mb-0">Covers essential components including engine, transmission, and electronics. Perfect for newer vehicles or budget-conscious drivers.</p>
        </div>
        
        <div class="mb-4 frosted-glass p-3 rounded-4 hover-glow">
          <h5><i class="fas fa-check-circle text-primary me-2"></i> Premium Plan (RM 169/month)</h5>
          <p class="text-muted ms-4 ps-2 mb-0">Our most comprehensive coverage with unlimited claims, higher claim limits, roadside assistance, and courtesy car. Ideal for complete peace of mind.</p>
        </div>
        
        <a href="<?php echo url('../plans.php'); ?>" class="btn btn-primary rounded-pill">
          <i class="fas fa-th-list me-2"></i> View Plan Details
        </a>
      </div>
      <div class="col-lg-6 fade-in">
        <div class="position-relative d-flex justify-content-center">
          <div class="position-absolute top-0 start-0 translate-middle bg-primary rounded-circle" style="width: 150px; height: 150px; opacity: 0.1; z-index: -1;"></div>
          <img src="../assets/images/choose-plan.png" alt="Choose Your Plan" class="img-fluid rounded-4 shadow-lg hover-scale" style="max-width: 85%;">
        </div>
      </div>
    </div>
    
    <div class="row align-items-center mb-5 flex-lg-row-reverse">
      <div class="col-lg-6 mb-4 mb-lg-0 fade-in">
        <h2 class="fw-bold mb-4 text-primary">2. Subscribe and Activate</h2>
        <p class="mb-4">Once you've selected your plan, the subscription process is simple:</p>
        
        <div class="frosted-glass p-4 rounded-4 mb-4">
          <ul class="list-unstyled">
            <li class="d-flex mb-3">
              <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; font-size: 14px;">1</div>
              <span class="ms-3">Complete a short application with your personal and vehicle details</span>
            </li>
            <li class="d-flex mb-3">
              <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; font-size: 14px;">2</div>
              <span class="ms-3">Set up your monthly payment method (credit card, debit card, or online banking)</span>
            </li>
            <li class="d-flex mb-3">
              <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; font-size: 14px;">3</div>
              <span class="ms-3">Receive your digital membership card and welcome kit</span>
            </li>
            <li class="d-flex">
              <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 30px; height: 30px; font-size: 14px;">4</div>
              <span class="ms-3">Your protection becomes active after a 30-day waiting period</span>
            </li>
          </ul>
        </div>
        
        <div class="alert alert-primary rounded-4 mt-4 frosted-glass border-0">
          <i class="fas fa-info-circle me-2"></i> The 30-day waiting period helps us maintain fair pricing by preventing sign-ups only after problems occur.
        </div>
      </div>
      <div class="col-lg-6 fade-in">
        <div class="position-relative">
          <div class="position-absolute bottom-0 end-0 translate-middle bg-primary rounded-circle" style="width: 150px; height: 150px; opacity: 0.1; z-index: -1;"></div>
          <img src="../assets/images/subscription.png" alt="Subscribe and Activate" class="img-fluid rounded-4 shadow-lg hover-scale">
        </div>
      </div>
    </div>
    
    <div class="row align-items-center mb-5">
      <div class="col-lg-6 mb-4 mb-lg-0 fade-in">
        <h2 class="fw-bold mb-4 text-primary">3. Submit Claims When Needed</h2>
        <p class="mb-4">When your vehicle needs repairs, our streamlined claims process makes it easy:</p>
        
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h5 class="card-title"><i class="fas fa-laptop text-primary me-2"></i> Online Claims Portal</h5>
            <p class="card-text">Log in to your member account and fill out a simple claim form with details about the issue.</p>
          </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <h5 class="card-title"><i class="fas fa-camera text-primary me-2"></i> Photo Documentation</h5>
            <p class="card-text">Upload photos of the affected parts and any relevant documentation.</p>
          </div>
        </div>
        
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h5 class="card-title"><i class="fas fa-check-double text-primary me-2"></i> Fast Approval</h5>
            <p class="card-text">Receive claim approval typically within 24-48 hours, with clear next steps for repairs.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6 fade-in">
        <div class="position-relative d-flex justify-content-center">
          <img src="../assets/images/claims-process.png" alt="Submit Claims" class="img-fluid rounded-4 shadow-lg hover-scale" style="max-width: 85%;">
        </div>
      </div>
    </div>
    
    <div class="row align-items-center flex-lg-row-reverse">
      <div class="col-lg-6 mb-4 mb-lg-0 fade-in">
        <h2 class="fw-bold mb-4 text-primary">4. Quality Repairs at Partner Workshops</h2>
        <p class="mb-4">Once your claim is approved, getting your car repaired is convenient and worry-free:</p>
        
        <div class="mb-4">
          <h5 class="mb-3">Extensive Workshop Network</h5>
          <p class="text-muted">Choose from our network of 150+ authorized workshops across Malaysia, including locations in:</p>
          <div class="row">
            <div class="col-6">
              <ul class="list-unstyled">
                <li><i class="fas fa-map-marker-alt text-primary me-2"></i> Kuala Lumpur</li>
                <li><i class="fas fa-map-marker-alt text-primary me-2"></i> Petaling Jaya</li>
                <li><i class="fas fa-map-marker-alt text-primary me-2"></i> Penang</li>
              </ul>
            </div>
            <div class="col-6">
              <ul class="list-unstyled">
                <li><i class="fas fa-map-marker-alt text-primary me-2"></i> Johor Bahru</li>
                <li><i class="fas fa-map-marker-alt text-primary me-2"></i> Ipoh</li>
                <li><i class="fas fa-map-marker-alt text-primary me-2"></i> Kuching</li>
              </ul>
            </div>
          </div>
        </div>
        
        <div class="mb-4">
          <h5 class="mb-3">Quality Guaranteed Repairs</h5>
          <p class="text-muted">All repairs come with a 6-month warranty for parts and labor, ensuring your vehicle gets the best care possible.</p>
        </div>
        
        <a href="#" class="btn btn-primary">Find a Workshop</a>
      </div>
      <div class="col-lg-6 fade-in">
        <div class="position-relative">
          <div class="position-absolute bottom-0 start-0 translate-middle bg-primary rounded-circle" style="width: 150px; height: 150px; opacity: 0.1; z-index: -1;"></div>
          <img src="../assets/images/workshop-repair.png" alt="Quality Repairs" class="img-fluid rounded-4 shadow-lg hover-scale">
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* 详细过程区域增强样式 */
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

.img-overlay {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 1;
}

.img-overlay .badge {
  font-size: 1rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
}
</style>

<!-- 会员仪表盘 -->
<div class="container py-5">
  <div class="text-center mb-5 fade-in">
    <h2 class="display-5 fw-bold mb-3 text-primary">Member Dashboard</h2>
    <p class="lead text-muted mx-auto" style="max-width: 700px;">Manage your protection plan easily through our intuitive online portal</p>
  </div>
  
  <div class="row g-4">
    <div class="col-md-4 fade-in">
      <div class="frosted-glass border-0 shadow h-100 hover-float">
        <div class="card-body text-center p-4">
          <div class="dashboard-icon mb-3 hover-rotate-icon">
            <i class="fas fa-tachometer-alt text-primary fa-3x"></i>
          </div>
          <h4 class="text-primary">Account Overview</h4>
          <p>Monitor your plan details, coverage information, payment history, and upcoming payments all in one place.</p>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 fade-in" style="transition-delay: 0.1s;">
      <div class="frosted-glass border-0 shadow h-100 hover-float">
        <div class="card-body text-center p-4">
          <div class="dashboard-icon mb-3 hover-rotate-icon">
            <i class="fas fa-file-invoice text-primary fa-3x"></i>
          </div>
          <h4 class="text-primary">Claims Management</h4>
          <p>Submit new claims, upload photos and documentation, track claim status, and view claim history.</p>
        </div>
      </div>
    </div>
    
    <div class="col-md-4 fade-in" style="transition-delay: 0.2s;">
      <div class="frosted-glass border-0 shadow h-100 hover-float">
        <div class="card-body text-center p-4">
          <div class="dashboard-icon mb-3 hover-rotate-icon">
            <i class="fas fa-wrench text-primary fa-3x"></i>
          </div>
          <h4 class="text-primary">Service Records</h4>
          <p>Keep track of all your vehicle's service history, maintenance reminders, and repair documentation.</p>
        </div>
      </div>
    </div>
  </div>
  
  <div class="text-center mt-5 fade-in">
    <img src="../assets/images/dashboard-preview.jpg" alt="Dashboard Preview" class="img-fluid rounded-4 shadow-lg">
  </div>
</div>

<!-- 常见问题 -->
<div class="container-fluid position-relative overflow-hidden py-5" style="background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(58, 12, 163, 0.08));">
  <div class="section-pattern-dots"></div>
  <div class="container py-4">
    <div class="text-center mb-5 fade-in">
      <h2 class="display-5 fw-bold mb-3">Frequently Asked Questions</h2>
      <p class="lead text-muted mx-auto" style="max-width: 700px;">Common questions about how our protection plan works</p>
    </div>
    
    <div class="row justify-content-center">
      <div class="col-lg-10 fade-in">
        <div class="accordion custom-accordion" id="faqAccordion">
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                <i class="fas fa-question-circle text-primary me-2"></i> How quickly can I make a claim after subscribing?
              </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                After subscribing, there is a 30-day waiting period before you can submit your first claim. This policy helps us prevent fraud and keep our plans affordable for everyone.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                <i class="fas fa-question-circle text-primary me-2"></i> Do I need to use your partner workshops for repairs?
              </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                Yes, to ensure quality repairs and streamlined claims processing, repairs must be performed at one of our authorized partner workshops. We have over 150 partners across Malaysia, so there's likely one convenient to you.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                <i class="fas fa-question-circle text-primary me-2"></i> What if I need repairs while traveling?
              </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                If you need repairs while traveling within Malaysia, you can visit any of our partner workshops nationwide. For Premium Plan members, our roadside assistance service can help you locate the nearest partner or arrange towing if needed.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                <i class="fas fa-question-circle text-primary me-2"></i> How long does the claims process take?
              </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                Most claims are reviewed and approved within 24-48 hours after submission. Once approved, you can schedule repairs at your convenience with our partner workshop. Repair time depends on the specific issue and parts availability.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                <i class="fas fa-question-circle text-primary me-2"></i> Do I pay anything out of pocket for repairs?
              </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                For approved claims within your coverage limits, you typically won't pay anything out of pocket. We settle directly with the partner workshop. If repairs exceed your plan's coverage limit, you'll only pay the difference.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="text-center mt-5 fade-in">
      <a href="<?php echo url('pages/faq.php'); ?>" class="btn btn-outline-primary btn-lg px-4 rounded-pill">
        <i class="fas fa-arrow-right me-2"></i> View All FAQs
      </a>
    </div>
  </div>
</div>

<style>
/* FAQ 手风琴增强样式 */
.custom-accordion .accordion-button {
  padding: 20px 25px;
  font-weight: 600;
  font-size: 1.1rem;
  background-color: white;
  box-shadow: none;
}

.custom-accordion .accordion-button:not(.collapsed) {
  color: var(--bs-primary);
  background-color: white;
  box-shadow: none;
}

.custom-accordion .accordion-button:focus {
  box-shadow: none;
  border-color: rgba(0, 0, 0, 0.125);
}

.custom-accordion .accordion-body {
  padding: 5px 25px 25px;
  color: #555;
}

.custom-accordion .accordion-button::after {
  background-size: 16px;
  width: 16px;
  height: 16px;
}
</style>

<!-- 行动号召 -->
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10 fade-in">
      <div class="card border-0 gradient-blue text-white shadow-lg position-relative overflow-hidden rounded-4">
        <div class="cta-pattern"></div>
        <div class="card-body p-5 text-center position-relative">
          <h2 class="fw-bold mb-3">Ready to protect your vehicle beyond standard insurance?</h2>
          <p class="lead mb-4">Join thousands of satisfied customers who enjoy peace of mind with our protection plans</p>
          <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <a href="<?php echo url('../plans.php'); ?>" class="btn btn-light btn-lg shadow-sm rounded-pill px-4">
              <i class="fas fa-th-list me-2"></i> View Plans & Pricing
            </a>
            <a href="<?php echo url('../register.php'); ?>" class="btn btn-outline-light btn-lg rounded-pill px-4">
              <i class="fas fa-shield-alt me-2"></i> Subscribe Now
            </a>
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
</style>

<?php
include_once("../includes/footer.php");
?> 
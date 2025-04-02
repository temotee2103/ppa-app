<?php
// FAQ Page
require_once '../init.php';
$pageTitle = "FAQ | Malaysia's 1st Additional Car Protection";
$current_page = 'faq';

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
        <h1 class="display-4 fw-bold mb-3 fade-in">Frequently Asked Questions</h1>
        <p class="lead opacity-90 mb-4 fade-in" style="transition-delay: 0.1s;">Find answers to common questions about our protection plans</p>
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

/* FAQ 导航样式 */
.faq-nav .nav-link {
  border-radius: 30px;
  padding: 10px 20px;
  transition: all 0.3s ease;
  background-color: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(5px);
  color: #212529;
  font-weight: 500;
}

.faq-nav .nav-link:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.faq-nav .nav-link.active {
  background-image: linear-gradient(135deg, #4361ee, #3a0ca3);
  color: white;
  box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
}

/* 搜索标签样式 */
.badge-pill {
  padding: 8px 16px;
  font-weight: 500;
  border-radius: 30px;
  transition: all 0.3s ease;
  background-color: rgba(67, 97, 238, 0.1);
  color: #4361ee;
  border: 1px solid rgba(67, 97, 238, 0.2);
}

.badge-pill:hover {
  background-color: #4361ee;
  color: white;
  transform: translateY(-2px);
}

/* 手风琴样式 */
.custom-accordion .accordion-item {
  border: none;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  margin-bottom: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.custom-accordion .accordion-button {
  padding: 20px 25px;
  font-weight: 600;
  font-size: 1.1rem;
  background-color: white;
  box-shadow: none;
  border-radius: 16px;
}

.custom-accordion .accordion-button:not(.collapsed) {
  color: #4361ee;
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

/* 输入框和搜索 */
.search-box {
  border-radius: 30px;
  padding: 15px 25px;
  border: 1px solid rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.search-box:focus {
  box-shadow: 0 5px 30px rgba(67, 97, 238, 0.2);
  border-color: #4361ee;
}

.search-btn {
  border-radius: 0 30px 30px 0;
  padding: 15px 25px;
}

/* 资源链接 */
.resource-link {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  border-radius: 10px;
  transition: all 0.3s ease;
  margin-bottom: 8px;
  color: #212529;
  text-decoration: none;
}

.resource-link:hover {
  background-color: rgba(67, 97, 238, 0.1);
  color: #4361ee;
  transform: translateX(5px);
}

.resource-link i {
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: linear-gradient(135deg, #4361ee, #3a0ca3);
  color: white;
  margin-right: 12px;
  font-size: 0.8rem;
}
</style>

<!-- 搜索部分 -->
<div class="container-fluid gradient-light-blue py-5">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-md-10">
        <div class="frosted-glass p-4 p-md-5 rounded-4 fade-in position-relative overflow-hidden">
          <div class="glass-shape glass-shape-1"></div>
          <div class="glass-shape glass-shape-2"></div>
          <h3 class="mb-4 text-primary">How can we help you?</h3>
          <div class="input-group mb-4">
            <input type="text" class="form-control search-box glass-input rounded-pill" id="faqSearch" placeholder="Search for answers...">
            <button class="btn btn-primary search-btn rounded-pill px-4" type="button">
              <i class="fas fa-search"></i>
            </button>
          </div>
          <div>
            <p class="mb-2">Popular searches:</p>
            <div class="d-flex flex-wrap gap-2">
              <a href="#claim" class="badge-pill text-decoration-none me-2 mb-2 hover-float">How to claim</a>
              <a href="#coverage" class="badge-pill text-decoration-none me-2 mb-2 hover-float">What's covered</a>
              <a href="#payments" class="badge-pill text-decoration-none me-2 mb-2 hover-float">Payment methods</a>
              <a href="#subscription" class="badge-pill text-decoration-none me-2 mb-2 hover-float">Cancel subscription</a>
              <a href="#workshops" class="badge-pill text-decoration-none me-2 mb-2 hover-float">Workshop locations</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FAQ 分类部分 -->
<div class="container pb-5">
  <div class="row mb-4">
    <div class="col-12 fade-in">
      <ul class="nav nav-pills faq-nav justify-content-center mb-5 flex-wrap" id="faqTab" role="tablist">
        <li class="nav-item me-3 mb-3">
          <button class="nav-link active hover-float" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
            <i class="fas fa-info-circle me-2"></i>General
          </button>
        </li>
        <li class="nav-item me-3 mb-3">
          <button class="nav-link hover-float" id="plans-tab" data-bs-toggle="tab" data-bs-target="#plans" type="button" role="tab" aria-controls="plans" aria-selected="false">
            <i class="fas fa-clipboard-list me-2"></i>Plans & Pricing
          </button>
        </li>
        <li class="nav-item me-3 mb-3">
          <button class="nav-link hover-float" id="coverage-tab" data-bs-toggle="tab" data-bs-target="#coverage" type="button" role="tab" aria-controls="coverage" aria-selected="false">
            <i class="fas fa-shield-alt me-2"></i>Coverage
          </button>
        </li>
        <li class="nav-item me-3 mb-3">
          <button class="nav-link hover-float" id="claim-tab" data-bs-toggle="tab" data-bs-target="#claim" type="button" role="tab" aria-controls="claim" aria-selected="false">
            <i class="fas fa-file-alt me-2"></i>Claims
          </button>
        </li>
        <li class="nav-item me-3 mb-3">
          <button class="nav-link hover-float" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="payments" aria-selected="false">
            <i class="fas fa-credit-card me-2"></i>Payments
          </button>
        </li>
        <li class="nav-item mb-3">
          <button class="nav-link hover-float" id="technical-tab" data-bs-toggle="tab" data-bs-target="#technical" type="button" role="tab" aria-controls="technical" aria-selected="false">
            <i class="fas fa-cogs me-2"></i>Technical
          </button>
        </li>
      </ul>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-3 mb-4 mb-lg-0 fade-in">
      <div class="frosted-glass mb-4 hover-float">
        <div class="card-body p-4">
          <h4 class="card-title mb-3 text-primary">Still Have Questions?</h4>
          <p class="card-text mb-4">Can't find what you're looking for? Contact our support team for assistance.</p>
          <a href="#contact-form" class="btn btn-primary w-100 rounded-pill">Contact Support</a>
        </div>
      </div>

      <div class="frosted-glass hover-float">
        <div class="card-body p-4">
          <h4 class="card-title mb-3 text-primary">Helpful Resources</h4>
          <div class="mt-3">
            <a href="how-it-works.php" class="resource-link hover-float d-flex align-items-center mb-3">
              <div class="me-2 rounded-circle bg-primary bg-opacity-10 p-2">
                <i class="fas fa-play-circle text-primary"></i>
              </div>
              <span>How It Works</span>
            </a>
            <a href="claims.php" class="resource-link hover-float d-flex align-items-center mb-3">
              <div class="me-2 rounded-circle bg-primary bg-opacity-10 p-2">
                <i class="fas fa-clipboard-check text-primary"></i>
              </div>
              <span>Claims Process</span>
            </a>
            <a href="#" class="resource-link hover-float d-flex align-items-center mb-3">
              <div class="me-2 rounded-circle bg-primary bg-opacity-10 p-2">
                <i class="fas fa-download text-primary"></i>
              </div>
              <span>Download Brochure</span>
            </a>
            <a href="subscription.php" class="resource-link hover-float d-flex align-items-center">
              <div class="me-2 rounded-circle bg-primary bg-opacity-10 p-2">
                <i class="fas fa-tags text-primary"></i>
              </div>
              <span>Subscription Plans</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-9 fade-in">
      <div class="tab-content" id="faqTabContent">
        <!-- General FAQs -->
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
          <div class="accordion custom-accordion" id="generalAccordion">
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="general1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse1" aria-expanded="true" aria-controls="generalCollapse1">
                  <i class="fas fa-question-circle text-primary me-2"></i> What is Malaysia's 1st Additional Car Protection?
                </button>
              </h2>
              <div id="generalCollapse1" class="accordion-collapse collapse show" aria-labelledby="general1" data-bs-parent="#generalAccordion">
                <div class="accordion-body bg-white">
                  <p>Malaysia's 1st Additional Car Protection is a subscription-based service that provides protection for your vehicle beyond standard insurance. We cover mechanical and electrical failures that are typically not covered by traditional car insurance, helping you avoid unexpected repair costs.</p>
                  <p>Our service operates on a simple monthly subscription model, with two plan options (Basic and Premium) to suit different needs and budgets.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="general2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse2" aria-expanded="false" aria-controls="generalCollapse2">
                  <i class="fas fa-question-circle text-primary me-2"></i> How is this different from regular car insurance?
                </button>
              </h2>
              <div id="generalCollapse2" class="accordion-collapse collapse" aria-labelledby="general2" data-bs-parent="#generalAccordion">
                <div class="accordion-body bg-white">
                  <p>Traditional car insurance typically covers accidents, theft, and third-party liability, but excludes mechanical and electrical failures due to wear and tear. Our protection plans specifically address these gaps by covering:</p>
                  <ul>
                    <li>Engine and transmission failures</li>
                    <li>Electrical system issues</li>
                    <li>Air conditioning repairs</li>
                    <li>Suspension system (Premium plan only)</li>
                    <li>And more</li>
                  </ul>
                  <p>Our service complements your existing insurance rather than replacing it, providing comprehensive protection for your vehicle.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="general3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse3" aria-expanded="false" aria-controls="generalCollapse3">
                  <i class="fas fa-question-circle text-primary me-2"></i> Is my car eligible for protection?
                </button>
              </h2>
              <div id="generalCollapse3" class="accordion-collapse collapse" aria-labelledby="general3" data-bs-parent="#generalAccordion">
                <div class="accordion-body bg-white">
                  <p>Most cars under 10 years old and with fewer than 150,000 kilometers are eligible for our protection plans. We cover most makes and models of cars, SUVs, and MPVs used for personal transportation.</p>
                  <p>Some limitations apply to:</p>
                  <ul>
                    <li>Luxury vehicles or high-performance sports cars (may require special assessment)</li>
                    <li>Commercial vehicles or those used for ride-sharing services</li>
                    <li>Vehicles with existing mechanical issues</li>
                    <li>Significantly modified vehicles</li>
                  </ul>
                  <p>If you're unsure about your vehicle's eligibility, please contact our customer service for a free assessment.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="general4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse4" aria-expanded="false" aria-controls="generalCollapse4">
                  <i class="fas fa-question-circle text-primary me-2"></i> Do I still need regular car insurance?
                </button>
              </h2>
              <div id="generalCollapse4" class="accordion-collapse collapse" aria-labelledby="general4" data-bs-parent="#generalAccordion">
                <div class="accordion-body bg-white">
                  <p>Yes, our protection plan does not replace mandatory motor insurance. In Malaysia, all vehicle owners are legally required to have at minimum a third-party motor insurance policy.</p>
                  <p>Our service is designed to complement your existing insurance by providing coverage for areas that traditional insurance typically excludes, such as mechanical and electrical failures.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="general5">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#generalCollapse5" aria-expanded="false" aria-controls="generalCollapse5">
                  <i class="fas fa-question-circle text-primary me-2"></i> Where are you located?
                </button>
              </h2>
              <div id="generalCollapse5" class="accordion-collapse collapse" aria-labelledby="general5" data-bs-parent="#generalAccordion">
                <div class="accordion-body bg-white">
                  <p>Our headquarters is located in Kuala Lumpur, Malaysia. We have a network of partner workshops throughout Malaysia, covering all major cities and many smaller towns.</p>
                  <p>Our main office address:</p>
                  <address>
                    Level 15, Menara XYZ<br>
                    Jalan Tun Razak<br>
                    50400 Kuala Lumpur<br>
                    Malaysia
                  </address>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Plans & Pricing Tab -->
        <div class="tab-pane fade" id="plans" role="tabpanel" aria-labelledby="plans-tab">
          <div class="accordion custom-accordion" id="plansAccordion">
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="plans1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#plansCollapse1" aria-expanded="true" aria-controls="plansCollapse1">
                  <i class="fas fa-question-circle text-primary me-2"></i> What protection plans do you offer?
                </button>
              </h2>
              <div id="plansCollapse1" class="accordion-collapse collapse show" aria-labelledby="plans1" data-bs-parent="#plansAccordion">
                <div class="accordion-body bg-white">
                  <p>We currently offer two protection plans to suit different needs and budgets:</p>
                  <ul>
                    <li><strong>Basic Plan (RM 99/month):</strong> Covers engine, transmission, and electronics with up to 2 claims per year, maximum RM 3,000 per claim.</li>
                    <li><strong>Premium Plan (RM 169/month):</strong> Comprehensive coverage including suspension, unlimited claims, roadside assistance, courtesy car, and maximum RM 5,000 per claim.</li>
                  </ul>
                  <p>For a detailed comparison of our plans, please visit our <a href="subscription.php">Subscription Plans</a> page.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="plans2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#plansCollapse2" aria-expanded="false" aria-controls="plansCollapse2">
                  <i class="fas fa-question-circle text-primary me-2"></i> Is there a minimum subscription period?
                </button>
              </h2>
              <div id="plansCollapse2" class="accordion-collapse collapse" aria-labelledby="plans2" data-bs-parent="#plansAccordion">
                <div class="accordion-body bg-white">
                  <p>Yes, our protection plans require a minimum commitment of 12 months. This allows us to provide competitive pricing and maintain the quality of our services.</p>
                  <p>After the initial 12-month period, your subscription continues on a month-to-month basis, and you can cancel at any time with 30 days' notice.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="plans3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#plansCollapse3" aria-expanded="false" aria-controls="plansCollapse3">
                  <i class="fas fa-question-circle text-primary me-2"></i> Can I upgrade or downgrade my plan?
                </button>
              </h2>
              <div id="plansCollapse3" class="accordion-collapse collapse" aria-labelledby="plans3" data-bs-parent="#plansAccordion">
                <div class="accordion-body bg-white">
                  <p>Yes, you can change your protection plan as follows:</p>
                  <ul>
                    <li><strong>Upgrades:</strong> You can upgrade from Basic to Premium at any time during your subscription period. The upgrade takes effect immediately, and your billing will be adjusted accordingly.</li>
                    <li><strong>Downgrades:</strong> You can downgrade from Premium to Basic at the end of your current billing cycle. Please note that downgrade requests must be submitted at least 30 days before the end of the billing cycle.</li>
                  </ul>
                  <p>To change your plan, simply log in to your member dashboard or contact our customer service.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="plans4">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#plansCollapse4" aria-expanded="false" aria-controls="plansCollapse4">
                  <i class="fas fa-question-circle text-primary me-2"></i> Are there any discounts available?
                </button>
              </h2>
              <div id="plansCollapse4" class="accordion-collapse collapse" aria-labelledby="plans4" data-bs-parent="#plansAccordion">
                <div class="accordion-body bg-white">
                  <p>Yes, we offer several discount options:</p>
                  <ul>
                    <li><strong>Annual Payment Discount:</strong> Save 10% when you pay for a full year upfront instead of monthly.</li>
                    <li><strong>Multi-Car Discount:</strong> Protect multiple vehicles under one account and receive a 15% discount on additional vehicles.</li>
                    <li><strong>Referral Program:</strong> Receive one month free when you refer a friend who subscribes.</li>
                    <li><strong>Promotional Offers:</strong> We regularly run special promotions. Check our website or subscribe to our newsletter to stay updated.</li>
                  </ul>
                  <p>For corporate clients with fleets of vehicles, we offer customized packages with special rates. Please contact our sales team for more information.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="plans5">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#plansCollapse5" aria-expanded="false" aria-controls="plansCollapse5">
                  <i class="fas fa-question-circle text-primary me-2"></i> How can I cancel my subscription?
                </button>
              </h2>
              <div id="plansCollapse5" class="accordion-collapse collapse" aria-labelledby="plans5" data-bs-parent="#plansAccordion">
                <div class="accordion-body bg-white">
                  <p>You can cancel your subscription after the initial 12-month commitment period. To cancel:</p>
                  <ol>
                    <li>Log in to your member dashboard</li>
                    <li>Go to "Subscription Management"</li>
                    <li>Click on "Cancel Subscription"</li>
                    <li>Follow the prompts to complete the cancellation process</li>
                  </ol>
                  <p>Alternatively, you can contact our customer service by phone or email to process your cancellation request.</p>
                  <p>Please note that cancellation requests must be submitted at least 30 days before the end of your billing cycle. If you cancel during the initial 12-month period, an early termination fee equivalent to 3 months of subscription may apply.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Coverage Tab -->
        <div class="tab-pane fade" id="coverage" role="tabpanel" aria-labelledby="coverage-tab">
          <div class="accordion custom-accordion" id="coverageAccordion">
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="coverage1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#coverageCollapse1" aria-expanded="true" aria-controls="coverageCollapse1">
                  <i class="fas fa-question-circle text-primary me-2"></i> What parts and systems are covered?
                </button>
              </h2>
              <div id="coverageCollapse1" class="accordion-collapse collapse show" aria-labelledby="coverage1" data-bs-parent="#coverageAccordion">
                <div class="accordion-body bg-white">
                  <p>Our protection plans cover a wide range of vehicle components and systems. Here's a breakdown:</p>
                  <p><strong>Basic Plan Coverage:</strong></p>
                  <ul>
                    <li><strong>Engine Components:</strong> Engine block, cylinder head, pistons, connecting rods, crankshaft, valves, timing belt/chain, oil pump, etc.</li>
                    <li><strong>Transmission:</strong> Gearbox, torque converter, clutch assembly, transmission control unit, etc.</li>
                    <li><strong>Electrical Systems:</strong> Alternator, starter motor, ECU, power window motors, central locking, etc.</li>
                  </ul>
                  <p><strong>Premium Plan Coverage:</strong> Everything in the Basic Plan, plus:</p>
                  <ul>
                    <li><strong>Suspension System:</strong> Shock absorbers, struts, control arms, ball joints, etc.</li>
                    <li><strong>Cooling System:</strong> Radiator, water pump, thermostat, cooling fans, etc.</li>
                    <li><strong>Fuel System:</strong> Fuel pump, injectors, pressure regulators, etc.</li>
                    <li><strong>Steering Components:</strong> Power steering pump, rack and pinion, steering box, etc.</li>
                    <li><strong>Braking System:</strong> Master cylinder, ABS components, calipers, etc.</li>
                    <li><strong>Air Conditioning:</strong> Compressor, condenser, evaporator, etc.</li>
                  </ul>
                  <p>For a complete list of covered components, please refer to the terms and conditions in your subscription agreement.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="coverage2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#coverageCollapse2" aria-expanded="false" aria-controls="coverageCollapse2">
                  <i class="fas fa-question-circle text-primary me-2"></i> What is not covered?
                </button>
              </h2>
              <div id="coverageCollapse2" class="accordion-collapse collapse" aria-labelledby="coverage2" data-bs-parent="#coverageAccordion">
                <div class="accordion-body bg-white">
                  <p>While our protection plans are comprehensive, certain items and situations are not covered:</p>
                  <ul>
                    <li><strong>Consumable Parts:</strong> Tires, batteries, light bulbs, wiper blades, brake pads/shoes, filters, fluids, etc.</li>
                    <li><strong>Regular Maintenance:</strong> Oil changes, tune-ups, and other routine servicing.</li>
                    <li><strong>Cosmetic Items:</strong> Paint, upholstery, trim, glass (unless related to a covered repair).</li>
                    <li><strong>Pre-existing Conditions:</strong> Issues that existed before your subscription or during the waiting period.</li>
                    <li><strong>Accident Damage:</strong> Repairs needed due to collisions, fire, theft, or vandalism (these should be covered by your regular car insurance).</li>
                    <li><strong>Misuse or Negligence:</strong> Damage caused by improper use, neglect, unauthorized modifications, or failure to maintain the vehicle according to manufacturer recommendations.</li>
                    <li><strong>Racing or Off-road Use:</strong> Any damage resulting from racing, off-roading, or using the vehicle for commercial purposes.</li>
                  </ul>
                  <p>For a full list of exclusions, please refer to the terms and conditions in your subscription agreement.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="coverage3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#coverageCollapse3" aria-expanded="false" aria-controls="coverageCollapse3">
                  <i class="fas fa-question-circle text-primary me-2"></i> Is there a waiting period before I can claim?
                </button>
              </h2>
              <div id="coverageCollapse3" class="accordion-collapse collapse" aria-labelledby="coverage3" data-bs-parent="#coverageAccordion">
                <div class="accordion-body bg-white">
                  <p>Yes, there is a 30-day waiting period from the start of your subscription before you can submit claims. This waiting period helps us maintain fair pricing and prevent fraudulent claims for pre-existing issues.</p>
                  <p>During this waiting period, we may request that your vehicle undergo an inspection to document its current condition. This is to ensure that any issues that arise after the waiting period are genuinely new problems and not pre-existing conditions.</p>
                  <p>Once the waiting period is over, you can submit claims for covered repairs at any time during your active subscription.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Claims Tab -->
        <div class="tab-pane fade" id="claim" role="tabpanel" aria-labelledby="claim-tab">
          <div class="accordion custom-accordion" id="claimAccordion">
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="claim1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#claimCollapse1" aria-expanded="true" aria-controls="claimCollapse1">
                  <i class="fas fa-question-circle text-primary me-2"></i> How do I make a claim?
                </button>
              </h2>
              <div id="claimCollapse1" class="accordion-collapse collapse show" aria-labelledby="claim1" data-bs-parent="#claimAccordion">
                <div class="accordion-body bg-white">
                  <p>To make a claim, please follow these steps:</p>
                  <ol>
                    <li>Contact our customer service to report the issue</li>
                    <li>Provide details about the problem and any relevant information</li>
                    <li>Wait for our approval</li>
                    <li>If approved, we'll arrange for a repair</li>
                  </ol>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="claim2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#claimCollapse2" aria-expanded="false" aria-controls="claimCollapse2">
                  <i class="fas fa-question-circle text-primary me-2"></i> What should I do if my claim is denied?
                </button>
              </h2>
              <div id="claimCollapse2" class="accordion-collapse collapse" aria-labelledby="claim2" data-bs-parent="#claimAccordion">
                <div class="accordion-body bg-white">
                  <p>If your claim is denied, you can:</p>
                  <ul>
                    <li>Contact our customer service for more information</li>
                    <li>Review the terms and conditions in your subscription agreement</li>
                    <li>Consider appealing the decision</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="claim3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#claimCollapse3" aria-expanded="false" aria-controls="claimCollapse3">
                  <i class="fas fa-question-circle text-primary me-2"></i> How long does it take to process a claim?
                </button>
              </h2>
              <div id="claimCollapse3" class="accordion-collapse collapse" aria-labelledby="claim3" data-bs-parent="#claimAccordion">
                <div class="accordion-body bg-white">
                  <p>The processing time for a claim can vary depending on the complexity of the issue. Typically, it takes 7-10 business days for a claim to be processed.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Payments Tab -->
        <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
          <div class="accordion custom-accordion" id="paymentsAccordion">
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="payments1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#paymentsCollapse1" aria-expanded="true" aria-controls="paymentsCollapse1">
                  <i class="fas fa-question-circle text-primary me-2"></i> What payment methods do you accept?
                </button>
              </h2>
              <div id="paymentsCollapse1" class="accordion-collapse collapse show" aria-labelledby="payments1" data-bs-parent="#paymentsAccordion">
                <div class="accordion-body bg-white">
                  <p>We accept various payment methods including:</p>
                  <ul>
                    <li>Credit/Debit Cards</li>
                    <li>Online Banking</li>
                    <li>Bank Transfers</li>
                    <li>Mobile Payments</li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="payments2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentsCollapse2" aria-expanded="false" aria-controls="paymentsCollapse2">
                  <i class="fas fa-question-circle text-primary me-2"></i> How can I update my payment details?
                </button>
              </h2>
              <div id="paymentsCollapse2" class="accordion-collapse collapse" aria-labelledby="payments2" data-bs-parent="#paymentsAccordion">
                <div class="accordion-body bg-white">
                  <p>You can update your payment details by logging in to your member dashboard and navigating to the "Subscription Management" section.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="payments3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#paymentsCollapse3" aria-expanded="false" aria-controls="paymentsCollapse3">
                  <i class="fas fa-question-circle text-primary me-2"></i> What happens if I miss a payment?
                </button>
              </h2>
              <div id="paymentsCollapse3" class="accordion-collapse collapse" aria-labelledby="payments3" data-bs-parent="#paymentsAccordion">
                <div class="accordion-body bg-white">
                  <p>If you miss a payment, your subscription will be suspended until the overdue amount is paid. Late payments may result in additional fees.</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Technical Tab -->
        <div class="tab-pane fade" id="technical" role="tabpanel" aria-labelledby="technical-tab">
          <div class="accordion custom-accordion" id="technicalAccordion">
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="technical1">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#technicalCollapse1" aria-expanded="true" aria-controls="technicalCollapse1">
                  <i class="fas fa-question-circle text-primary me-2"></i> How does the technology work?
                </button>
              </h2>
              <div id="technicalCollapse1" class="accordion-collapse collapse show" aria-labelledby="technical1" data-bs-parent="#technicalAccordion">
                <div class="accordion-body bg-white">
                  <p>The technology behind our protection plans is designed to monitor your vehicle's performance and detect potential issues before they become major problems. It uses sensors and data analysis to provide early warning of any abnormalities.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="technical2">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#technicalCollapse2" aria-expanded="false" aria-controls="technicalCollapse2">
                  <i class="fas fa-question-circle text-primary me-2"></i> What are the system requirements?
                </button>
              </h2>
              <div id="technicalCollapse2" class="accordion-collapse collapse" aria-labelledby="technical2" data-bs-parent="#technicalAccordion">
                <div class="accordion-body bg-white">
                  <p>Our technology is compatible with most modern vehicles. The system typically requires a direct connection to your vehicle's OBD port and access to a mobile device.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
              <h2 class="accordion-header" id="technical3">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#technicalCollapse3" aria-expanded="false" aria-controls="technicalCollapse3">
                  <i class="fas fa-question-circle text-primary me-2"></i> How often is the system updated?
                </button>
              </h2>
              <div id="technicalCollapse3" class="accordion-collapse collapse" aria-labelledby="technical3" data-bs-parent="#technicalAccordion">
                <div class="accordion-body bg-white">
                  <p>The system is updated regularly to ensure optimal performance and compatibility with the latest vehicle technologies.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 联系表单部分 -->
<div class="gradient-section py-5" id="contact-form">
  <div class="section-pattern-dots"></div>
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-lg-8 fade-in">
        <div class="glass-card">
          <div class="card-body p-4 p-lg-5">
            <h2 class="text-center mb-4">Still Have Questions?</h2>
            <p class="text-center mb-4">Our support team is here to help. Fill out the form below and we'll get back to you within 24 hours.</p>
            
            <form id="faqContactForm">
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="contactName" class="form-label">Full Name*</label>
                    <input type="text" class="form-control rounded-pill" id="contactName" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="contactEmail" class="form-label">Email Address*</label>
                    <input type="email" class="form-control rounded-pill" id="contactEmail" required>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label for="contactSubject" class="form-label">Subject*</label>
                    <select class="form-select rounded-pill" id="contactSubject" required>
                      <option value="" selected disabled>Select a subject</option>
                      <option value="general">General Question</option>
                      <option value="coverage">Coverage Question</option>
                      <option value="claims">Claims Assistance</option>
                      <option value="subscription">Subscription Question</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <label for="contactMessage" class="form-label">Your Question*</label>
                    <textarea class="form-control rounded-4" id="contactMessage" rows="5" required></textarea>
                  </div>
                </div>
                <div class="col-12 text-center">
                  <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 mt-3">Submit Question</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 行动号召部分 -->
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
</style>

<?php
include_once("../includes/footer.php");
?> 
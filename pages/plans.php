<?php
// 引入初始化文件
require_once '../init.php';

// 设置当前页面
$current_page = 'plans';
$page_title = 'Protection Plans';

// 添加额外的CSS和JS文件
$additional_css = ['modern.css'];
$additional_js = ['modern.js'];

// 包含页面头部
include_once '../includes/header.php';
?>

<!-- 现代化页面标题区 -->
<div class="page-header bg-primary position-relative overflow-hidden">
  <div class="page-header-pattern"></div>
  <div class="container py-5 position-relative">
    <div class="row align-items-center">
      <div class="col-lg-7 text-white mb-4 mb-lg-0">
        <h1 class="display-4 fw-bold mb-3 fade-in">Protection Plans</h1>
        <p class="lead opacity-90 mb-4 fade-in" style="transition-delay: 0.1s;">Choose the right protection that fits your needs</p>
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

/* 现代化表格设计 */
.pricing-table {
  border-radius: 16px;
  overflow: hidden;
  border: none;
  box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
}

.pricing-table thead th {
  padding: 20px;
  border: none;
}

.pricing-table thead th:first-child {
  border-radius: 16px 0 0 0;
  background-color: #f8f9fa;
}

.pricing-table thead th:last-child {
  border-radius: 0 16px 0 0;
  background-image: linear-gradient(135deg, #4361ee, #3a0ca3);
}

.pricing-table tbody td {
  padding: 15px 20px;
  border: none;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.pricing-table tbody tr:last-child td {
  border-bottom: none;
  padding-top: 25px;
  padding-bottom: 25px;
}

.pricing-table tbody tr:last-child td:first-child {
  border-radius: 0 0 0 16px;
}

.pricing-table tbody tr:last-child td:last-child {
  border-radius: 0 0 16px 0;
}

.pricing-table .bg-primary-subtle {
  background-color: rgba(67, 97, 238, 0.05) !important;
}

.pricing-table .badge {
  border-radius: 20px;
  padding: 5px 12px;
  font-weight: 500;
}

.btn-pricing {
  border-radius: 30px;
  padding: 10px 25px;
  transition: all 0.3s ease;
  font-weight: 600;
}

.btn-pricing:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* 车型卡片样式 */
.vehicle-card {
  border-radius: 16px;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  overflow: hidden;
}

.vehicle-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.vehicle-card .card-body {
  padding: 30px;
}

.vehicle-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

/* 计算器卡片样式 */
.calculator-card {
  border-radius: 20px;
  overflow: hidden;
  border: none;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
}

.calculator-card .card-body {
  padding: 30px;
}

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

<!-- 套餐比较 -->
<div class="container py-5">
  <div class="text-center mb-5 fade-in">
    <h2 class="display-5 fw-bold mb-3">Choose Your Protection Plan</h2>
    <p class="lead text-muted mx-auto" style="max-width: 700px;">We offer flexible protection options to meet different needs and budgets</p>
  </div>

  <!-- 桌面版表格视图 (d-none d-lg-block 在移动端隐藏) -->
  <div class="row justify-content-center d-none d-lg-flex">
    <div class="col-lg-10 fade-in">
      <div class="table-responsive">
        <table class="table pricing-table">
          <thead>
            <tr>
              <th class="text-center" style="width: 30%">Features</th>
              <th class="text-center" style="width: 35%">
                <h4 class="mb-0">Basic Plan</h4>
                <p class="text-primary fw-bold mb-0">RM 99/month</p>
              </th>
              <th class="text-center text-white" style="width: 35%">
                <h4 class="mb-0">Premium Plan</h4>
                <p class="fw-bold mb-0">RM 169/month</p>
                <div class="position-relative d-inline-block mt-1">
                  <span class="badge bg-warning">Most Popular</span>
                </div>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Engine Protection</td>
              <td class="text-center"><i class="fas fa-check-circle text-success"></i></td>
              <td class="text-center bg-primary-subtle"><i class="fas fa-check-circle text-success"></i></td>
            </tr>
            <tr>
              <td>Transmission Protection</td>
              <td class="text-center"><i class="fas fa-check-circle text-success"></i></td>
              <td class="text-center bg-primary-subtle"><i class="fas fa-check-circle text-success"></i></td>
            </tr>
            <tr>
              <td>Electronics Protection</td>
              <td class="text-center"><i class="fas fa-check-circle text-success"></i></td>
              <td class="text-center bg-primary-subtle"><i class="fas fa-check-circle text-success"></i></td>
            </tr>
            <tr>
              <td>Suspension Protection</td>
              <td class="text-center"><i class="fas fa-times-circle text-danger"></i></td>
              <td class="text-center bg-primary-subtle"><i class="fas fa-check-circle text-success"></i></td>
            </tr>
            <tr>
              <td>Claims Per Year</td>
              <td class="text-center">2 times</td>
              <td class="text-center bg-primary-subtle">Unlimited</td>
            </tr>
            <tr>
              <td>Maximum Claim Amount</td>
              <td class="text-center">RM 3,000</td>
              <td class="text-center bg-primary-subtle">RM 5,000</td>
            </tr>
            <tr>
              <td>24/7 Roadside Assistance</td>
              <td class="text-center"><i class="fas fa-times-circle text-danger"></i></td>
              <td class="text-center bg-primary-subtle"><i class="fas fa-check-circle text-success"></i></td>
            </tr>
            <tr>
              <td>Free Courtesy Car</td>
              <td class="text-center"><i class="fas fa-times-circle text-danger"></i></td>
              <td class="text-center bg-primary-subtle"><i class="fas fa-check-circle text-success"></i></td>
            </tr>
            <tr>
              <td>Annual Vehicle Inspection</td>
              <td class="text-center">1 time</td>
              <td class="text-center bg-primary-subtle">2 times</td>
            </tr>
            <tr>
              <td></td>
              <td class="text-center">
                <a href="register.php?plan=basic" class="btn btn-outline-primary btn-lg btn-pricing">Choose Basic</a>
              </td>
              <td class="text-center bg-primary-subtle">
                <a href="register.php?plan=premium" class="btn btn-primary btn-lg btn-pricing">Choose Premium</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
  <!-- 移动版卡片视图 (d-lg-none 在桌面端隐藏) -->
  <div class="row fade-in d-lg-none">
    <!-- 基础计划卡片 -->
    <div class="col-md-6 col-12 mb-4">
      <div class="card pricing-card h-100">
        <div class="card-header text-center py-4">
          <h4 class="mb-0">Basic Plan</h4>
          <p class="text-primary fw-bold mb-0 mt-2">RM 99/month</p>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Engine Protection
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Transmission Protection
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Electronics Protection
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Suspension Protection
              <i class="fas fa-times-circle text-danger"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Claims Per Year
              <span>2 times</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Maximum Claim Amount
              <span>RM 3,000</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              24/7 Roadside Assistance
              <i class="fas fa-times-circle text-danger"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Free Courtesy Car
              <i class="fas fa-times-circle text-danger"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Annual Vehicle Inspection
              <span>1 time</span>
            </li>
          </ul>
          <div class="d-grid">
            <a href="register.php?plan=basic" class="btn btn-outline-primary btn-lg btn-pricing">Choose Basic</a>
          </div>
        </div>
      </div>
    </div>
    
    <!-- 高级计划卡片 -->
    <div class="col-md-6 col-12 mb-4">
      <div class="card pricing-card premium-card h-100">
        <div class="popular-tag">Most Popular</div>
        <div class="card-header text-center py-4 bg-primary text-white">
          <h4 class="mb-0">Premium Plan</h4>
          <p class="fw-bold mb-0 mt-2">RM 169/month</p>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush mb-4">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Engine Protection
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Transmission Protection
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Electronics Protection
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Suspension Protection
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Claims Per Year
              <span>Unlimited</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Maximum Claim Amount
              <span>RM 5,000</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              24/7 Roadside Assistance
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Free Courtesy Car
              <i class="fas fa-check-circle text-success"></i>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              Annual Vehicle Inspection
              <span>2 times</span>
            </li>
          </ul>
          <div class="d-grid">
            <a href="register.php?plan=premium" class="btn btn-primary btn-lg btn-pricing">Choose Premium</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.pricing-card {
  border-radius: 16px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
  margin-bottom: 1rem;
}

.pricing-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.pricing-card .card-header {
  border-bottom: none;
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}

.pricing-card .list-group-item {
  border-left: none;
  border-right: none;
  padding: 0.75rem 1.25rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.pricing-card .btn-pricing {
  border-radius: 30px;
  padding: 12px 25px;
  transition: all 0.3s ease;
  font-weight: 600;
  font-size: 1rem;
}

.pricing-card .btn-pricing:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

/* 增强的Premium卡片样式 */
.premium-card {
  border: 2px solid #4361ee;
  box-shadow: 0 10px 30px rgba(67, 97, 238, 0.15);
  position: relative;
  z-index: 1;
  transform: translateY(-10px);
}

.premium-card:hover {
  box-shadow: 0 15px 40px rgba(67, 97, 238, 0.25);
  transform: translateY(-15px);
}

.premium-card .card-header {
  background: linear-gradient(135deg, #4361ee, #3a0ca3);
  padding-top: 2.5rem !important;
}

.premium-card .fa-check-circle {
  color: #4361ee !important;
  font-size: 1.1rem;
}

/* 顶部标签样式 */
.popular-tag {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  background: #ffc107;
  color: #000;
  text-align: center;
  font-weight: bold;
  font-size: 0.75rem;
  padding: 6px;
  z-index: 2;
  border-radius: 16px 16px 0 0;
}

/* 响应式调整 */
@media (max-width: 767.98px) {
  .pricing-card {
    margin-bottom: 2rem;
  }
}
</style>

<!-- 支持的车型 -->
<div class="container-fluid position-relative overflow-hidden py-5" style="background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(58, 12, 163, 0.08));">
  <div class="section-pattern-dots"></div>
  <div class="container py-4">
    <div class="text-center mb-5 fade-in">
      <h2 class="display-5 fw-bold mb-3">Supported Vehicle Types</h2>
      <p class="lead text-muted mx-auto" style="max-width: 700px;">Our protection plans cover a wide range of vehicles</p>
    </div>

    <div class="row g-4">
      <div class="col-md-3 col-6 fade-in">
        <div class="vehicle-card h-100 shadow-sm">
          <div class="card-body text-center">
            <div class="vehicle-icon">
              <i class="fas fa-car text-primary fa-2x"></i>
            </div>
            <h4 class="mb-3">Sedans</h4>
            <p class="text-muted mb-0">All popular sedan models from major manufacturers</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6 fade-in" style="transition-delay: 0.1s;">
        <div class="vehicle-card h-100 shadow-sm">
          <div class="card-body text-center">
            <div class="vehicle-icon">
              <i class="fas fa-truck-monster text-primary fa-2x"></i>
            </div>
            <h4 class="mb-3">SUVs</h4>
            <p class="text-muted mb-0">Compact, mid-size, and full-size SUVs</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6 fade-in" style="transition-delay: 0.2s;">
        <div class="vehicle-card h-100 shadow-sm">
          <div class="card-body text-center">
            <div class="vehicle-icon">
              <i class="fas fa-shuttle-van text-primary fa-2x"></i>
            </div>
            <h4 class="mb-3">MPVs</h4>
            <p class="text-muted mb-0">Family vehicles with seating for 6+ passengers</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-6 fade-in" style="transition-delay: 0.3s;">
        <div class="vehicle-card h-100 shadow-sm">
          <div class="card-body text-center">
            <div class="vehicle-icon">
              <i class="fas fa-car-side text-primary fa-2x"></i>
            </div>
            <h4 class="mb-3">Hatchbacks</h4>
            <p class="text-muted mb-0">Compact and subcompact hatchbacks</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
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

<!-- 节省计算器 -->
<div class="container py-5">
  <div class="row align-items-center g-4">
    <div class="col-lg-5 fade-in">
      <h2 class="display-6 fw-bold mb-4">Calculate Your Savings</h2>
      <p class="mb-4 lead">Use our interactive calculator to see how our protection plans can help you save on potential repair costs.</p>
      <ul class="list-unstyled mb-4">
        <li class="d-flex align-items-center mb-4">
          <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
            <i class="fas fa-shield-alt"></i>
          </div>
          <span class="ms-3 fs-5">Avoid unexpected high repair costs</span>
        </li>
        <li class="d-flex align-items-center mb-4">
          <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
            <i class="fas fa-chart-line"></i>
          </div>
          <span class="ms-3 fs-5">Maintain your vehicle's value</span>
        </li>
        <li class="d-flex align-items-center">
          <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px;">
            <i class="fas fa-wallet"></i>
          </div>
          <span class="ms-3 fs-5">Predictable monthly fee vs. unexpected expenses</span>
        </li>
      </ul>
    </div>
    <div class="col-lg-7 fade-in">
      <div class="calculator-card">
        <div class="card-body">
          <h4 class="card-title mb-4">Cost Savings Calculator</h4>
          <form id="savingsCalculator">
            <div class="mb-3">
              <label for="carModel" class="form-label">Car Model</label>
              <select class="form-select" id="carModel" required>
                <option value="">Please select...</option>
                <option value="honda_city">Honda City</option>
                <option value="toyota_vios">Toyota Vios</option>
                <option value="proton_x50">Proton X50</option>
                <option value="perodua_myvi">Perodua Myvi</option>
                <option value="honda_crv">Honda CR-V</option>
                <option value="toyota_camry">Toyota Camry</option>
                <option value="bmw_3">BMW 3 Series</option>
                <option value="mercedes_c">Mercedes C-Class</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label for="carAge" class="form-label">Vehicle Age</label>
              <select class="form-select" id="carAge" required>
                <option value="">Please select...</option>
                <option value="1">Less than 3 years</option>
                <option value="2">3-5 years</option>
                <option value="3">5-7 years</option>
                <option value="4">7-10 years</option>
                <option value="5">More than 10 years</option>
              </select>
            </div>
            
            <div class="mb-3">
              <label for="planType" class="form-label">Protection Plan</label>
              <select class="form-select" id="planType" required>
                <option value="">Please select...</option>
                <option value="basic">Basic Plan (RM 99/month)</option>
                <option value="premium">Premium Plan (RM 169/month)</option>
              </select>
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg">Calculate Savings</button>
            </div>
          </form>
          
          <div id="calculationResult" class="mt-4 d-none">
            <div class="alert alert-primary rounded-3 p-4">
              <h5 class="mb-3">Your Estimated Annual Savings</h5>
              <div class="row align-items-center">
                <div class="col-sm-6">
                  <p class="mb-1">Average repair costs without protection:</p>
                  <h4 class="text-danger mb-0" id="withoutProtection">RM 0</h4>
                </div>
                <div class="col-sm-6">
                  <p class="mb-1">Annual cost with our protection:</p>
                  <h4 class="text-success mb-0" id="withProtection">RM 0</h4>
                </div>
              </div>
              <hr>
              <div class="text-center">
                <h5 class="mb-0">Potential Savings: <span class="text-primary" id="potentialSavings">RM 0</span></h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FAQ 部分 -->
<div class="container-fluid position-relative overflow-hidden py-5" style="background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(58, 12, 163, 0.08));">
  <div class="section-pattern-dots"></div>
  <div class="container py-4">
    <div class="text-center mb-5 fade-in">
      <h2 class="display-5 fw-bold mb-3">Frequently Asked Questions</h2>
      <p class="lead text-muted mx-auto" style="max-width: 700px;">Common questions about our protection plans</p>
    </div>
    
    <div class="row justify-content-center">
      <div class="col-lg-10 fade-in">
        <div class="accordion custom-accordion" id="faqAccordion">
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                <i class="fas fa-question-circle text-primary me-2"></i> What is the difference between your protection plan and my regular insurance?
              </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                While regular insurance typically covers accidents and third-party damages, our protection plans focus on mechanical and electrical components that often aren't covered by standard insurance. We cover wear and tear, electronic failures, and other issues that develop during normal vehicle use.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                <i class="fas fa-question-circle text-primary me-2"></i> Is there a waiting period before I can make claims?
              </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                Yes, there is a 30-day waiting period from the date you subscribe before you can make your first claim. This helps us ensure the program remains viable by preventing sign-ups only after problems have already developed.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                <i class="fas fa-question-circle text-primary me-2"></i> How old can my vehicle be to qualify for coverage?
              </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                Our Basic Plan covers vehicles up to 12 years old, while our Premium Plan covers vehicles up to 10 years old. Vehicles must also pass an initial inspection to qualify for coverage.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                <i class="fas fa-question-circle text-primary me-2"></i> Can I cancel my subscription at any time?
              </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                Yes, you can cancel your subscription at any time. However, there is a minimum 3-month commitment period. If you cancel within this period, a cancellation fee equivalent to the remaining months will apply.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="text-center mt-5 fade-in">
      <a href="pages/faq.php" class="btn btn-outline-primary btn-lg px-4 rounded-pill">
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
      <div class="card border-0 bg-primary text-white shadow-lg position-relative overflow-hidden rounded-4">
        <div class="cta-pattern"></div>
        <div class="card-body p-5 text-center position-relative">
          <h2 class="fw-bold mb-3">Ready to protect your vehicle beyond standard insurance?</h2>
          <p class="lead mb-4">Join thousands of satisfied customers who enjoy peace of mind with our protection plans</p>
          <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
            <a href="register.php?plan=premium" class="btn btn-light btn-lg shadow-sm rounded-pill px-4">Subscribe Now</a>
            <a href="pages/how-it-works.php" class="btn btn-outline-light btn-lg rounded-pill px-4">Learn More</a>
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
// 包含页面页脚
include_once("../includes/footer.php");
?> 
?> 
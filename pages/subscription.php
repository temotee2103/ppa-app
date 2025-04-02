<?php
// Subscription Plans Page
require_once '../init.php';
$pageTitle = "Subscription | Malaysia's 1st Additional Car Protection";
$current_page = 'subscription';
// Include additional CSS for glass effect
$additional_css = ['modern.css'];
include_once("../includes/header.php");
?>

<!-- Page Header -->
<div class="page-header bg-primary text-white py-5 position-relative overflow-hidden">
    <div class="header-shape-1"></div>
    <div class="header-shape-2"></div>
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7 fade-in">
                <h1 class="display-4 fw-bold mb-3">Subscription Plans</h1>
                <p class="lead mb-0">Choose the protection plan that best fits your needs</p>
            </div>
            <div class="col-lg-5 text-center text-lg-end fade-in" style="transition-delay: 0.2s;">
                <div class="header-decorative-circles">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                    <div class="circle circle-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Plan Comparison -->
<div class="container py-5">
  <div class="text-center mb-5 fade-in">
    <h2 class="fw-bold">Choose Your Protection Plan</h2>
    <p>We offer flexible protection options to meet different needs and budgets</p>
  </div>

  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="table-responsive glass-card fade-in border-0 shadow-lg">
        <table class="table table-bordered pricing-table mb-0">
          <thead>
            <tr>
              <th class="text-center" style="width: 30%">Features</th>
              <th class="text-center" style="width: 35%">
                <h4 class="mb-0">Basic Plan</h4>
                <p class="text-primary fw-bold mb-0">RM 99/month</p>
              </th>
              <th class="text-center bg-primary text-white" style="width: 35%">
                <h4 class="mb-0">Premium Plan</h4>
                <p class="fw-bold mb-0">RM 169/month</p>
                <span class="badge bg-warning mt-1">Most Popular</span>
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
              <td class="border-bottom-0"></td>
              <td class="text-center border-bottom-0">
                <a href="register.php?plan=basic" class="btn btn-outline-primary btn-lg hover-float px-4">Choose Basic</a>
              </td>
              <td class="text-center border-bottom-0 bg-primary-subtle">
                <a href="register.php?plan=premium" class="btn btn-primary btn-lg hover-float px-4">Choose Premium</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Savings Calculator -->
<div class="container-fluid bg-light py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-5 mb-4 mb-lg-0 fade-in">
        <h2 class="fw-bold mb-3">Calculate Your Savings</h2>
        <p class="mb-4">Use our interactive calculator to see how additional car protection can help you save on potential repair costs.</p>
        <ul class="list-group list-group-flush mb-4">
          <li class="list-group-item bg-transparent d-flex align-items-center">
            <div class="icon-circle bg-primary-subtle text-primary me-3">
              <i class="fas fa-shield-alt"></i>
            </div>
            <span>Avoid high repair costs</span>
          </li>
          <li class="list-group-item bg-transparent d-flex align-items-center">
            <div class="icon-circle bg-primary-subtle text-primary me-3">
              <i class="fas fa-chart-line"></i>
            </div>
            <span>Maintain your car's value</span>
          </li>
          <li class="list-group-item bg-transparent d-flex align-items-center">
            <div class="icon-circle bg-primary-subtle text-primary me-3">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <span>Predictable monthly fee instead of unexpected expenses</span>
          </li>
        </ul>
      </div>
      <div class="col-lg-7 fade-in" style="transition-delay: 0.2s;">
        <div class="glass-card shadow-lg border-0">
          <div class="card-body p-4">
            <h4 class="card-title mb-4">Cost Savings Calculator</h4>
            <form id="savingsCalculator">
              <div class="mb-3">
                <label for="carModel" class="form-label">Car Model</label>
                <select class="form-select form-select-lg glass-input" id="carModel" required>
                  <option value="">Please select...</option>
                  <option value="sedan">Sedan</option>
                  <option value="suv">SUV</option>
                  <option value="mpv">MPV</option>
                  <option value="luxury">Luxury Car</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="carAge" class="form-label">Car Age</label>
                <select class="form-select form-select-lg glass-input" id="carAge" required>
                  <option value="">Please select...</option>
                  <option value="0-3">0-3 years</option>
                  <option value="4-7">4-7 years</option>
                  <option value="8-10">8-10 years</option>
                  <option value="10+">10+ years</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="planType" class="form-label">Protection Plan</label>
                <select class="form-select form-select-lg glass-input" id="planType" required>
                  <option value="">Please select...</option>
                  <option value="basic">Basic Plan</option>
                  <option value="premium">Premium Plan</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary btn-lg w-100 hover-float">Calculate Savings</button>
            </form>
            
            <div id="calculationResult" class="mt-4 p-3 rounded d-none glass-card-inner">
              <h5 class="text-center mb-4">Your Potential Savings</h5>
              <div class="row text-center g-4">
                <div class="col-4">
                  <h3 class="text-primary" id="monthlyFee">RM 0</h3>
                  <small class="text-muted">Monthly Fee</small>
                </div>
                <div class="col-4">
                  <h3 class="text-danger" id="avgRepairCost">RM 0</h3>
                  <small class="text-muted">Avg. Annual Repair</small>
                </div>
                <div class="col-4">
                  <h3 class="text-success" id="annualSavings">RM 0</h3>
                  <small class="text-muted">Annual Savings</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Frequently Asked Questions -->
<div class="container-fluid position-relative overflow-hidden py-5" style="background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(58, 12, 163, 0.08));">
  <div class="section-pattern-dots"></div>
  <div class="container py-4">
    <div class="text-center mb-5 fade-in">
      <h2 class="display-5 fw-bold mb-3">Frequently Asked Questions</h2>
      <p class="lead text-muted mx-auto" style="max-width: 700px;">Common questions about our subscription plans</p>
    </div>
    
    <div class="row justify-content-center">
      <div class="col-lg-10 fade-in">
        <div class="accordion custom-accordion" id="faqAccordion">
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                <i class="fas fa-question-circle text-primary me-2"></i> Is there a minimum commitment period for subscription plans?
              </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                Yes, our subscription plans have a minimum commitment period of 12 months. After signing up, you can choose to pay monthly or pay annually for additional discounts.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                <i class="fas fa-question-circle text-primary me-2"></i> How can I change my subscription plan?
              </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                You can upgrade your plan anytime in the member portal, and the upgrade will take effect immediately. For downgrades, you'll need to wait until the end of your current subscription period.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                <i class="fas fa-question-circle text-primary me-2"></i> Does the protection plan cover all car models?
              </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                Our protection plans cover most common sedans, SUVs, and MPVs that are under 10 years old. Some high-performance and luxury vehicles may require special assessment. Before registering, you can verify your car's eligibility through our customer service.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                <i class="fas fa-question-circle text-primary me-2"></i> How do I submit a claim?
              </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                The claims process is very straightforward. Log in to the member portal, fill out the claim form, and upload necessary documents (such as repair estimate, photos of your vehicle, etc.). Our team will review your application within 24 hours and guide you through the next steps.
              </div>
            </div>
          </div>
          
          <div class="accordion-item border-0 mb-3 rounded-4 shadow-sm overflow-hidden">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                <i class="fas fa-question-circle text-primary me-2"></i> Is there a waiting period?
              </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body bg-white">
                Yes, to prevent fraud, there is a 30-day waiting period after a new subscription before you can submit claims. This ensures we can provide fair and sustainable service to all customers.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="text-center mt-5 fade-in">
      <a href="faq.php" class="btn btn-outline-primary btn-lg px-4 rounded-pill">
        <i class="fas fa-arrow-right me-2"></i> View All FAQs
      </a>
    </div>
  </div>
</div>

<!-- Call to Action -->
<div class="page-header bg-primary text-white py-5 position-relative overflow-hidden">
    <div class="header-shape-1"></div>
    <div class="header-shape-2"></div>
    <div class="container position-relative text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8 fade-in">
                <h2 class="display-5 fw-bold mb-4">Subscribe Now for Additional Protection for Your Car</h2>
                <p class="lead mb-4">Join thousands of satisfied customers and enjoy worry-free driving</p>
                <a href="register.php" class="btn btn-light btn-lg px-5 py-3 fw-bold hover-float">Register Now</a>
            </div>
        </div>
    </div>
</div>

<style>
/* 头部装饰圆圈与形状 */
.header-decorative-circles {
    position: relative;
    width: 100%;
    height: 150px;
}

.circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
}

.circle-1 {
    width: 100px;
    height: 100px;
    top: 10px;
    right: 60px;
}

.circle-2 {
    width: 60px;
    height: 60px;
    top: 50px;
    right: 20px;
}

.circle-3 {
    width: 80px;
    height: 80px;
    top: 80px;
    right: 120px;
}

.header-shape-1 {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 300px;
    height: 300px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    z-index: 0;
}

.header-shape-2 {
    position: absolute;
    bottom: -80px;
    left: -80px;
    width: 350px;
    height: 350px;
    background-color: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    z-index: 0;
}

/* 图标圆圈样式 */
.icon-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1.5rem;
    font-size: 1.5rem;
}

/* 表格增强样式 */
.pricing-table {
    border-collapse: separate;
    border-spacing: 0;
    border-radius: 8px;
    overflow: hidden;
}

.pricing-table th, .pricing-table td {
    padding: 1.2rem;
    border-color: rgba(0, 0, 0, 0.05);
}

.pricing-table th {
    font-weight: 600;
    padding: 1.5rem;
}

/* 内部卡片样式 */
.glass-card-inner {
    background-color: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(5px);
    border-radius: 8px;
}

/* 手风琴样式覆盖 */
.accordion-button:not(.collapsed) {
    color: var(--bs-primary);
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(var(--bs-primary-rgb), 0.25);
}

/* 动画效果 */
.hover-float {
    transition: all 0.3s ease;
}

.hover-float:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* 添加点状背景图案样式 */
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

<!-- Savings Calculator Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const calculator = document.getElementById('savingsCalculator');
  const result = document.getElementById('calculationResult');
  const monthlyFee = document.getElementById('monthlyFee');
  const avgRepairCost = document.getElementById('avgRepairCost');
  const annualSavings = document.getElementById('annualSavings');
  
  calculator.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const carModel = document.getElementById('carModel').value;
    const carAge = document.getElementById('carAge').value;
    const planType = document.getElementById('planType').value;
    
    // Simple calculation logic (may need more complex algorithms in a real application)
    let fee = planType === 'basic' ? 99 : 169;
    let repair = 0;
    
    // Estimate potential repair costs based on car model and age
    if (carModel === 'sedan') {
      repair = carAge === '0-3' ? 1500 : carAge === '4-7' ? 3000 : carAge === '8-10' ? 4500 : 6000;
    } else if (carModel === 'suv') {
      repair = carAge === '0-3' ? 2000 : carAge === '4-7' ? 4000 : carAge === '8-10' ? 6000 : 8000;
    } else if (carModel === 'mpv') {
      repair = carAge === '0-3' ? 1800 : carAge === '4-7' ? 3600 : carAge === '8-10' ? 5400 : 7200;
    } else if (carModel === 'luxury') {
      repair = carAge === '0-3' ? 3000 : carAge === '4-7' ? 6000 : carAge === '8-10' ? 9000 : 12000;
    }
    
    // Repair costs covered by the protection plan
    const coverage = planType === 'basic' ? 0.6 : 0.8; // Basic covers 60%, Premium covers 80%
    const savings = Math.round(repair * coverage - fee * 12);
    
    // Display results
    monthlyFee.textContent = 'RM ' + fee;
    avgRepairCost.textContent = 'RM ' + repair;
    annualSavings.textContent = 'RM ' + savings;
    result.classList.remove('d-none');
  });
  
  // 添加淡入动画
  const fadeElements = document.querySelectorAll('.fade-in');
  
  const fadeInObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
        fadeInObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  
  fadeElements.forEach(el => fadeInObserver.observe(el));
});
</script>

<?php
include_once("../includes/footer.php");
?> 
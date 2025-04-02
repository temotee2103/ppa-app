<?php
// Contact Us Page
require_once '../init.php';
$pageTitle = "Contact Us | Malaysia's 1st Additional Car Protection";
$current_page = 'contact';

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
        <h1 class="display-4 fw-bold mb-3 fade-in">Contact Us</h1>
        <p class="lead opacity-90 mb-4 fade-in" style="transition-delay: 0.1s;">Get in touch with our team for any inquiries or assistance</p>
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

/* 联系信息图标 */
.contact-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(67, 97, 238, 0.1), rgba(58, 12, 163, 0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  color: #4361ee;
  font-size: 1.5rem;
  margin-right: 20px;
  flex-shrink: 0;
}

/* 地图卡片 */
.map-card {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  height: 100%;
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

/* 工作坊网络卡片 */
.workshop-card {
  border-radius: 20px;
  overflow: hidden;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  height: 100%;
}

.workshop-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.workshop-icon {
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

.form-check-input:checked {
  background-color: #4361ee;
  border-color: #4361ee;
}

.form-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}
</style>

<!-- 联系信息和表单部分 -->
<div class="container py-5">
  <div class="row g-5">
    <!-- 联系信息 -->
    <div class="col-lg-5 fade-in">
      <div class="glass-card h-100 position-relative overflow-hidden">
        <div class="card-blur-bg"></div>
        <div class="card-body p-4 p-lg-5 position-relative">
          <h2 class="card-title fw-bold mb-4 text-primary">Contact Information</h2>
          
          <div class="d-flex mb-4">
            <div class="contact-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
            <div>
              <h4 class="mb-2">Office Address</h4>
              <p class="mb-0 text-muted">
                Level 15, Menara XYZ<br>
                Jalan Tun Razak<br>
                50400 Kuala Lumpur<br>
                Malaysia
              </p>
            </div>
          </div>
          
          <div class="d-flex mb-4">
            <div class="contact-icon">
              <i class="fas fa-phone-alt"></i>
            </div>
            <div>
              <h4 class="mb-2">Phone</h4>
              <p class="mb-1 text-muted">Customer Service: <a href="tel:+60312345678" class="text-primary">+603 1234 5678</a></p>
              <p class="mb-0 text-muted">Claims Hotline: <a href="tel:+60312345679" class="text-primary">+603 1234 5679</a></p>
            </div>
          </div>
          
          <div class="d-flex mb-4">
            <div class="contact-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div>
              <h4 class="mb-2">Email</h4>
              <p class="mb-1 text-muted">General Inquiries: <a href="mailto:info@ppamy.com" class="text-primary">info@ppamy.com</a></p>
              <p class="mb-1 text-muted">Customer Support: <a href="mailto:support@ppamy.com" class="text-primary">support@ppamy.com</a></p>
              <p class="mb-0 text-muted">Claims Department: <a href="mailto:claims@ppamy.com" class="text-primary">claims@ppamy.com</a></p>
            </div>
          </div>
          
          <div class="d-flex">
            <div class="contact-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div>
              <h4 class="mb-2">Business Hours</h4>
              <p class="mb-1 text-muted">Monday - Friday: 9:00 AM - 6:00 PM</p>
              <p class="mb-0 text-muted">Saturday: 9:00 AM - 1:00 PM (Customer Service only)</p>
              <p class="mb-0 text-muted">Sunday & Public Holidays: Closed</p>
            </div>
          </div>
          
          <div class="mt-5">
            <h4 class="mb-3">Connect With Us</h4>
            <div class="d-flex gap-3">
              <a href="#" class="social-btn">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="#" class="social-btn">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="social-btn">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="social-btn">
                <i class="fab fa-linkedin-in"></i>
              </a>
            </div>
          </div>
          
          <div class="card-shape-1"></div>
          <div class="card-shape-2"></div>
        </div>
      </div>
    </div>
    
    <!-- 联系表单 -->
    <div class="col-lg-7 fade-in" style="transition-delay: 0.1s;">
      <div class="glass-card position-relative overflow-hidden">
        <div class="card-blur-bg"></div>
        <div class="card-body p-4 p-lg-5 position-relative">
          <h2 class="card-title fw-bold mb-4 text-primary">Send Us a Message</h2>
          
          <form id="contactForm">
            <div class="row g-4">
              <div class="col-md-6">
                <label for="firstName" class="form-label">First Name*</label>
                <input type="text" class="form-control form-control-lg rounded-pill glass-input" id="firstName" required>
              </div>
              <div class="col-md-6">
                <label for="lastName" class="form-label">Last Name*</label>
                <input type="text" class="form-control form-control-lg rounded-pill glass-input" id="lastName" required>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email Address*</label>
                <input type="email" class="form-control form-control-lg rounded-pill glass-input" id="email" required>
              </div>
              <div class="col-md-6">
                <label for="phone" class="form-label">Phone Number*</label>
                <input type="tel" class="form-control form-control-lg rounded-pill glass-input" id="phone" required>
              </div>
              <div class="col-12">
                <label for="subject" class="form-label">Subject*</label>
                <select class="form-select form-select-lg rounded-pill glass-input" id="subject" required>
                  <option value="" selected disabled>Select a subject</option>
                  <option value="general">General Inquiry</option>
                  <option value="membership">Membership Information</option>
                  <option value="claims">Claims Assistance</option>
                  <option value="technical">Technical Support</option>
                  <option value="feedback">Feedback & Suggestions</option>
                  <option value="partnership">Partnership Opportunities</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-12">
                <label for="message" class="form-label">Message*</label>
                <textarea class="form-control form-control-lg rounded-4 glass-input" id="message" rows="5" required></textarea>
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="privacyPolicy" required>
                  <label class="form-check-label" for="privacyPolicy">
                    I have read and agree to the <a href="#" class="text-primary">Privacy Policy</a> and consent to the processing of my personal data.
                  </label>
                </div>
              </div>
              <div class="col-12 text-center mt-3">
                <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3">
                  <i class="fas fa-paper-plane me-2"></i> Submit Message
                </button>
              </div>
            </div>
          </form>
          
          <div class="card-shape-1"></div>
          <div class="card-shape-2"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* 增加新的磨砂玻璃效果 */
.card-blur-bg {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.8), rgba(255, 255, 255, 0.5));
  backdrop-filter: blur(10px);
  z-index: -1;
}

.card-shape-1 {
  position: absolute;
  top: -30px;
  right: -30px;
  width: 150px;
  height: 150px;
  background: linear-gradient(135deg, rgba(26, 75, 132, 0.1), rgba(46, 125, 209, 0.1));
  border-radius: 50%;
  z-index: -1;
}

.card-shape-2 {
  position: absolute;
  bottom: -50px;
  left: -50px;
  width: 200px;
  height: 200px;
  background: linear-gradient(135deg, rgba(255, 115, 0, 0.05), rgba(255, 166, 77, 0.05));
  border-radius: 50%;
  z-index: -1;
}

.glass-input {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(5px);
  border: 1px solid rgba(26, 75, 132, 0.1);
  transition: all 0.3s ease;
}

.glass-input:focus {
  background: rgba(255, 255, 255, 0.9);
  box-shadow: 0 0 0 3px rgba(26, 75, 132, 0.15);
  border-color: rgba(26, 75, 132, 0.3);
}

.social-btn {
  width: 45px;
  height: 45px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(26, 75, 132, 0.1), rgba(46, 125, 209, 0.1));
  color: var(--primary);
  transition: all 0.3s ease;
  text-decoration: none;
}

.social-btn:hover {
  background: var(--primary-gradient);
  color: white;
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(26, 75, 132, 0.2);
}

/* 联系信息图标增强 */
.contact-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(26, 75, 132, 0.1), rgba(46, 125, 209, 0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--primary);
  font-size: 1.5rem;
  margin-right: 20px;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.d-flex:hover .contact-icon {
  transform: scale(1.1);
  background: linear-gradient(135deg, rgba(26, 75, 132, 0.2), rgba(46, 125, 209, 0.2));
}
</style>

<!-- 地图部分 -->
<div class="gradient-section py-5">
  <div class="section-pattern-dots"></div>
  <div class="container py-4 position-relative">
    <div class="row mb-5">
      <div class="col-12 text-center fade-in">
        <h2 class="display-5 fw-bold mb-3 text-primary">Find Us</h2>
        <p class="lead text-muted mx-auto" style="max-width: 700px;">Visit our main office or find a workshop partner near you</p>
      </div>
    </div>
    
    <div class="fade-in">
      <div class="map-card glass-effect">
        <!-- Replace with actual Google Maps embed code -->
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.7743918358853!2d101.71658231475519!3d3.1592365976920765!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc37d47e384ddf%3A0xbdc98376fbcb12a3!2sJalan%20Tun%20Razak%2C%20Kuala%20Lumpur%2C%20Malaysia!5e0!3m2!1sen!2s!4v1615889792921!5m2!1sen!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    </div>
  </div>
</div>

<style>
.map-card.glass-effect {
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
  height: 100%;
  border: var(--glass-border);
  position: relative;
}

.map-card.glass-effect::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 10px;
  background: var(--primary-gradient);
  z-index: 1;
}
</style>

<!-- 工作坊网络部分 -->
<div class="container py-5">
  <div class="row mb-5">
    <div class="col-12 text-center fade-in">
      <h2 class="display-5 fw-bold mb-3 text-primary">Our Workshop Network</h2>
      <p class="lead text-muted mx-auto" style="max-width: 700px;">With over 150 authorized workshop partners across Malaysia, help is always nearby</p>
    </div>
  </div>
  
  <div class="row g-4">
    <div class="col-md-6 col-lg-3 fade-in">
      <div class="workshop-card h-100 glass-card">
        <div class="card-blur-bg"></div>
        <div class="card-body text-center p-4 position-relative">
          <div class="workshop-icon">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <h4 class="text-primary mb-3">Klang Valley</h4>
          <p class="text-muted mb-4">Over 50 workshops in Kuala Lumpur, Petaling Jaya, Shah Alam, and surrounding areas.</p>
          <a href="#" class="btn btn-outline-primary rounded-pill px-4">Find Workshops</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 col-lg-3 fade-in" style="transition-delay: 0.1s;">
      <div class="workshop-card h-100 glass-card">
        <div class="card-blur-bg"></div>
        <div class="card-body text-center p-4 position-relative">
          <div class="workshop-icon">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <h4 class="text-primary mb-3">Northern Region</h4>
          <p class="text-muted mb-4">Workshops in Penang, Ipoh, Kedah, and other northern states.</p>
          <a href="#" class="btn btn-outline-primary rounded-pill px-4">Find Workshops</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 col-lg-3 fade-in" style="transition-delay: 0.2s;">
      <div class="workshop-card h-100 glass-card">
        <div class="card-blur-bg"></div>
        <div class="card-body text-center p-4 position-relative">
          <div class="workshop-icon">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <h4 class="text-primary mb-3">Southern Region</h4>
          <p class="text-muted mb-4">Workshops in Johor Bahru, Melaka, Negeri Sembilan, and surrounding areas.</p>
          <a href="#" class="btn btn-outline-primary rounded-pill px-4">Find Workshops</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-6 col-lg-3 fade-in" style="transition-delay: 0.3s;">
      <div class="workshop-card h-100 glass-card">
        <div class="card-blur-bg"></div>
        <div class="card-body text-center p-4 position-relative">
          <div class="workshop-icon">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <h4 class="text-primary mb-3">East Malaysia</h4>
          <p class="text-muted mb-4">Workshops in Kuching, Kota Kinabalu, and other major cities in Sabah and Sarawak.</p>
          <a href="#" class="btn btn-outline-primary rounded-pill px-4">Find Workshops</a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.workshop-card {
  transition: all 0.4s ease;
}

.workshop-card:hover {
  transform: translateY(-10px);
}

.workshop-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(26, 75, 132, 0.1), rgba(46, 125, 209, 0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
  color: var(--primary);
  font-size: 1.75rem;
  transition: all 0.3s ease;
}

.workshop-card:hover .workshop-icon {
  background: var(--primary-gradient);
  color: white;
  transform: scale(1.1);
}
</style>

<!-- 行动号召部分 -->
<div class="gradient-section py-5">
  <div class="section-pattern-dots"></div>
  <div class="container py-4 position-relative">
    <div class="row justify-content-center">
      <div class="col-lg-10 fade-in">
        <div class="card border-0 bg-primary text-white shadow-lg position-relative overflow-hidden rounded-4">
          <div class="cta-pattern"></div>
          <div class="card-body p-5 text-center position-relative">
            <h2 class="fw-bold mb-3">Ready to protect your vehicle beyond standard insurance?</h2>
            <p class="lead mb-4">Join thousands of satisfied customers who enjoy peace of mind with our protection plans</p>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
              <a href="../register.php?plan=premium" class="btn btn-light btn-lg shadow-sm rounded-pill px-4">
                <i class="fas fa-shield-alt me-2"></i> Subscribe Now
              </a>
              <a href="../plans.php" class="btn btn-outline-light btn-lg rounded-pill px-4">
                <i class="fas fa-th-list me-2"></i> View Plans
              </a>
            </div>
          </div>
          <div class="cta-shape-1"></div>
          <div class="cta-shape-2"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* CTA 卡片增强样式 */
.card.bg-primary {
  background: linear-gradient(135deg, #1A4B84, #2E7DD1) !important;
}

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
</style>

<?php
include_once("../includes/footer.php");
?> 
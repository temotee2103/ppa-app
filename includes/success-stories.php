<?php
/**
 * 成功案例组件
 * 用于展示客户使用PPA服务的成功案例
 */
?>
<div class="gradient-section py-5">
  <div class="section-pattern-dots"></div>
  <div class="container py-4 position-relative">
    <div class="text-center mb-5 fade-in">
      <h2 class="display-5 fw-bold mb-3">Successful Claims Stories</h2>
      <p class="lead text-muted mx-auto" style="max-width: 700px;">See how our protection plan has helped real customers</p>
    </div>
    
    <div class="row g-4">
      <div class="col-lg-4 fade-in">
        <div class="glass-card h-100 p-4">
          <div class="d-flex mb-4 align-items-center">
            <div class="user-avatar-container">
              <img src="<?php echo asset('images/users/user-ahmad.jpg'); ?>" alt="User" class="user-avatar">
            </div>
            <div class="ms-3">
              <h5 class="mb-0">Ahmad Ismail</h5>
              <p class="mb-0 text-muted">Proton X70 Owner</p>
            </div>
          </div>
          <p class="mb-3">"When my transmission started having issues, I was worried about the expensive repairs. I submitted a claim through the online portal and got approval within 24 hours. The total repair cost was RM 4,800, but I paid nothing!"</p>
          <div class="d-flex align-items-center">
            <div class="saved-amount-badge">
              <div class="saved-label">Saved</div>
              <div class="saved-value">RM 4,800</div>
            </div>
            <span class="text-muted small ms-3">Claim processed in 1 day</span>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 fade-in" style="transition-delay: 0.1s;">
        <div class="glass-card h-100 p-4">
          <div class="d-flex mb-4 align-items-center">
            <div class="user-avatar-container">
              <img src="<?php echo asset('images/users/user-michelle.jpg'); ?>" alt="User" class="user-avatar">
            </div>
            <div class="ms-3">
              <h5 class="mb-0">Michelle Wong</h5>
              <p class="mb-0 text-muted">Honda City Owner</p>
            </div>
          </div>
          <p class="mb-3">"My car's air conditioning system completely failed during the hottest month of the year. The PPA team approved my claim quickly and directed me to a nearby workshop. The entire process was smooth and I had my car back with a working AC in just 2 days."</p>
          <div class="d-flex align-items-center">
            <div class="saved-amount-badge">
              <div class="saved-label">Saved</div>
              <div class="saved-value">RM 2,300</div>
            </div>
            <span class="text-muted small ms-3">Claim processed in 2 days</span>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4 fade-in" style="transition-delay: 0.2s;">
        <div class="glass-card h-100 p-4">
          <div class="d-flex mb-4 align-items-center">
            <div class="user-avatar-container">
              <img src="<?php echo asset('images/users/user-raj.jpg'); ?>" alt="User" class="user-avatar">
            </div>
            <div class="ms-3">
              <h5 class="mb-0">Raj Kumar</h5>
              <p class="mb-0 text-muted">Toyota Camry Owner</p>
            </div>
          </div>
          <p class="mb-3">"When my car's electrical system started failing, I was expecting a complicated claims process. To my surprise, I submitted the claim online in the morning, received approval by evening, and got my car fixed the next day. As a Premium Plan member, I even got a courtesy car while mine was being repaired."</p>
          <div class="d-flex align-items-center">
            <div class="saved-amount-badge">
              <div class="saved-label">Saved</div>
              <div class="saved-value">RM 3,700</div>
            </div>
            <span class="text-muted small ms-3">Claim processed in 1 day</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
/* 用户头像样式 */
.user-avatar-container {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  overflow: hidden;
  background: linear-gradient(135deg, #e6f2ff, #f0f7ff);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 3px solid white;
}

.user-avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.glass-card:hover .user-avatar {
  transform: scale(1.05);
}

/* 节省金额徽章样式 */
.saved-amount-badge {
  background: linear-gradient(135deg, #07a74f, #05c959);
  color: white;
  border-radius: 8px;
  overflow: hidden;
  display: flex;
  box-shadow: 0 4px 10px rgba(5, 201, 89, 0.25);
  transition: all 0.3s ease;
}

.glass-card:hover .saved-amount-badge {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(5, 201, 89, 0.3);
}

.saved-label {
  background-color: rgba(0, 0, 0, 0.2);
  padding: 6px 10px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.saved-value {
  padding: 6px 12px;
  font-weight: 700;
  font-size: 14px;
}
</style> 
<?php
// User Profile Page
require_once '../init.php';

$pageTitle = "My Profile | Customer Portal";
$current_page = 'profile';

// Ensure user is logged in
$user = User::getInstance();

if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Get current user
$currentUser = $user->getCurrentUser(true);

// Check if user should be here (only customer role)
if (isset($currentUser['role_name']) && !in_array($currentUser['role_name'], ['customer', ''])) {
    header('Location: ../admin/dashboard.php');
    exit;
}

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    
    // Simple validation
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $error_message = 'Please fill all required fields.';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Update user profile
        $updateData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone
        ];
        
        try {
            if ($user->updateUser($currentUser['id'], $updateData)) {
                $success_message = 'Profile updated successfully!';
                // Refresh current user data
                $currentUser = $user->getCurrentUser();
            } else {
                $error_message = 'Failed to update profile. Please try again.';
            }
        } catch (Exception $e) {
            $error_message = 'An error occurred: ' . $e->getMessage();
        }
    }
}

// Include header
include_once("includes/header.php");
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">My Profile</h1>
</div>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo $error_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Profile Overview Card -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profile Overview</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar-preview mx-auto mb-3">
                        <img src="<?php echo isset($currentUser['avatar']) && !empty($currentUser['avatar']) ? '../' . $currentUser['avatar'] . '?v=' . time() : '../assets/images/default-avatar.png'; ?>" alt="Profile Picture">
                    </div>
                    <h5 class="mb-1"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></h5>
                    <p class="text-muted mb-3">
                        <!-- 移除用户角色和图标显示 -->
                    </p>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#avatarModal">
                        Change Photo
                    </button>
                </div>
                
                <hr>
                
                <div class="py-2">
                    <div class="mb-3">
                        <div class="small text-gray-500">Email</div>
                        <div class="mb-0"><?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-gray-500">Phone</div>
                        <div class="mb-0"><?php echo htmlspecialchars($currentUser['phone'] ?? 'Not provided'); ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-gray-500">Member Since</div>
                        <div class="mb-0">
                            <?php 
                            $created_date = isset($currentUser['created_at']) ? new DateTime($currentUser['created_at']) : null;
                            echo $created_date ? $created_date->format('F d, Y') : 'N/A'; 
                            ?>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-gray-500">Last Login</div>
                        <div class="mb-0">
                            <?php 
                            if (isset($currentUser['last_login']) && !empty($currentUser['last_login'])) {
                                $login_date = new DateTime($currentUser['last_login']);
                                echo $login_date->format('F d, Y g:i A');
                            } else {
                                echo 'Just now';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#passwordModal">
                        <span class="me-2">🔑</span>Change Password
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Account Security Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Account Security</h6>
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 fw-medium">Two-Factor Authentication</h6>
                        <p class="mb-0 small text-muted">Add an extra layer of security</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="twoFactorSwitch">
                    </div>
                </div>
                
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-1 fw-medium">Login Notifications</h6>
                        <p class="mb-0 small text-muted">Get alerted for new logins</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="loginNotifsSwitch" checked>
                    </div>
                </div>
                
                <hr>
                
                <div class="d-grid">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#deviceModal">
                        <span class="me-2">📱</span>Manage Devices
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Profile Card -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Profile</h6>
            </div>
            <div class="card-body">
                <form action="profile.php" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name*</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($currentUser['first_name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name*</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($currentUser['last_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address*</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($currentUser['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
        
        <!-- Notification Preferences Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Notification Preferences</h6>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <h6 class="fw-medium">Email Notifications</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="emailClaimUpdates" checked>
                            <label class="form-check-label" for="emailClaimUpdates">
                                Claims updates and status changes
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="emailPlanRenewals" checked>
                            <label class="form-check-label" for="emailPlanRenewals">
                                Plan renewal reminders
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="emailPaymentConfirmations" checked>
                            <label class="form-check-label" for="emailPaymentConfirmations">
                                Payment confirmations
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="emailPromotional">
                            <label class="form-check-label" for="emailPromotional">
                                Promotional offers and newsletters
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="fw-medium">WhatsApp Notifications</h6>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="whatsappClaimUpdates" checked>
                            <label class="form-check-label" for="whatsappClaimUpdates">
                                Claims updates and status changes
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="whatsappPlanRenewals">
                            <label class="form-check-label" for="whatsappPlanRenewals">
                                Plan renewal reminders
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="whatsappPromotional">
                            <label class="form-check-label" for="whatsappPromotional">
                                Promotional offers and newsletters
                            </label>
                        </div>
                        <div class="small text-muted mt-2">
                            WhatsApp notifications will be sent to your registered phone number via Twilio.
                        </div>
                    </div>
                    
                    <button type="button" id="savePreferences" class="btn btn-primary">Save Preferences</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Avatar Upload Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-labelledby="avatarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-effect">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="avatarModalLabel">Change Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="avatarForm" action="process/update-avatar.php" method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <div class="avatar-preview mx-auto mb-3" id="avatarPreview">
                            <img src="<?php echo isset($currentUser['avatar']) && !empty($currentUser['avatar']) ? '../' . $currentUser['avatar'] . '?v=' . time() : '../assets/images/default-avatar.png'; ?>" alt="Profile Picture">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Upload Image</label>
                        <input class="form-control" type="file" id="avatar" name="avatar" accept="image/*">
                        <div class="form-text">Recommended size: 300x300 pixels. Max file size: 2MB.</div>
                    </div>
                    <div class="avatar-status mt-3"></div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAvatarBtn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-effect">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="passwordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" required>
                        <div class="form-text">Use at least 8 characters with a mix of letters, numbers & symbols.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="changePasswordBtn">Change Password</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Devices Modal -->
<div class="modal fade" id="deviceModal" tabindex="-1" aria-labelledby="deviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-effect">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="deviceModalLabel">Manage Devices</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <span class="me-2">ℹ️</span>
                    These are devices that have been used to access your account.
                </div>
                
                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><span class="me-2">💻</span> Windows PC (Chrome)</h6>
                            <small class="text-muted">Current device • Last active: Now</small>
                        </div>
                        <span class="badge bg-success">This Device</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><span class="me-2">📱</span> iPhone (Safari)</h6>
                            <small class="text-muted">Last active: 2 days ago</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1"><span class="me-2">📱</span> iPad (Safari)</h6>
                            <small class="text-muted">Last active: 1 week ago</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger">Remove</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-danger">Sign Out All Devices</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #e3e6f0;
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.fw-medium {
    font-weight: 500;
}

.activity-log {
    max-height: 300px;
    overflow-y: auto;
}

.activity-item {
    padding: 15px 0;
    border-bottom: 1px solid #e3e6f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background-color: rgba(78, 115, 223, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Avatar preview
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    
    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    avatarPreview.innerHTML = `<img src="${e.target.result}" alt="Avatar Preview">`;
                };
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Save avatar
    const saveAvatarBtn = document.getElementById('saveAvatarBtn');
    const avatarForm = document.getElementById('avatarForm');
    const avatarStatus = document.querySelector('.avatar-status');
    
    if (saveAvatarBtn && avatarForm) {
        saveAvatarBtn.addEventListener('click', function() {
            // Check if file is selected
            if (!avatarInput.files || !avatarInput.files[0]) {
                avatarStatus.innerHTML = '<div class="alert alert-warning">Please select an image file.</div>';
                return;
            }
            
            // Create FormData object
            const formData = new FormData(avatarForm);
            
            // Disable button and show loading
            saveAvatarBtn.disabled = true;
            saveAvatarBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
            
            // Send AJAX request
            fetch(avatarForm.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    avatarStatus.innerHTML = '<div class="alert alert-success">Avatar updated successfully!</div>';
                    
                    // Update all avatar images on the page
                    const userAvatars = document.querySelectorAll('.user-avatar img, .avatar-preview img');
                    userAvatars.forEach(img => {
                        img.src = data.avatar_url + '?t=' + new Date().getTime(); // Add timestamp to bypass cache
                    });
                    
                    // Close modal after 1.5 seconds
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
                        modal.hide();
                        
                        // Reload page to ensure all instances are updated
                        window.location.reload();
                    }, 1500);
                } else {
                    avatarStatus.innerHTML = `<div class="alert alert-danger">${data.message || 'An error occurred while updating avatar.'}</div>`;
                    saveAvatarBtn.disabled = false;
                    saveAvatarBtn.innerHTML = 'Save Changes';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                avatarStatus.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
                saveAvatarBtn.disabled = false;
                saveAvatarBtn.innerHTML = 'Save Changes';
            });
        });
    }
    
    // Change password
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    if (changePasswordBtn) {
        changePasswordBtn.addEventListener('click', function() {
            const form = document.getElementById('passwordForm');
            if (form.checkValidity()) {
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (newPassword !== confirmPassword) {
                    alert('New passwords do not match.');
                    return;
                }
                
                // In production, submit form data via AJAX
                // For demo purposes, just close the modal and show alert
                const modal = bootstrap.Modal.getInstance(document.getElementById('passwordModal'));
                modal.hide();
                
                // Show success message
                alert('Password changed successfully!');
            } else {
                form.reportValidity();
            }
        });
    }
    
    // Save preferences
    const savePreferencesBtn = document.getElementById('savePreferences');
    if (savePreferencesBtn) {
        savePreferencesBtn.addEventListener('click', function() {
            // 收集通知偏好
            const preferences = {
                email: {
                    claimUpdates: document.getElementById('emailClaimUpdates').checked,
                    planRenewals: document.getElementById('emailPlanRenewals').checked,
                    paymentConfirmations: document.getElementById('emailPaymentConfirmations').checked,
                    promotional: document.getElementById('emailPromotional').checked
                },
                whatsapp: {
                    claimUpdates: document.getElementById('whatsappClaimUpdates').checked,
                    planRenewals: document.getElementById('whatsappPlanRenewals').checked,
                    promotional: document.getElementById('whatsappPromotional').checked
                }
            };
            
            console.log('Notification preferences:', preferences);
            
            // 在实际生产环境中，这里会通过AJAX提交到服务器
            // 显示成功消息
            alert('Notification preferences saved successfully!');
        });
    }
});
</script>

<?php include_once("includes/footer.php"); ?> 
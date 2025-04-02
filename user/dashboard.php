<?php
// Customer Dashboard Page
require_once '../init.php';

$pageTitle = "Dashboard | Customer Portal";
$current_page = 'dashboard';

// Ensure user is logged in
$user = User::getInstance();

if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Get current user
$currentUser = $user->getCurrentUser();

// Check if user should be here (only customer role)
if (isset($currentUser['role_name']) && !in_array($currentUser['role_name'], ['customer', ''])) {
    header('Location: ../admin/dashboard.php');
    exit;
}

// Initialize statistics with default values
$totalVehicles = 0;
$totalActivePlans = 0;
$totalClaims = 0;
$pendingClaims = 0;
$userVehicles = [];
$userPlans = [];
$userClaims = [];

// Get user statistics - using try/catch to handle possible database errors
try {
    $vehicleObj = new Vehicle($db);
    $userVehicles = $vehicleObj->getUserVehicles($currentUser['id']) ?: [];
    $totalVehicles = count($userVehicles);
} catch (Exception $e) {
    // Log error and continue
    error_log("Error loading vehicles: " . $e->getMessage());
}

try {
    $planObj = new Plan($db);
    $userPlans = $planObj->getUserActivePlans($currentUser['id']) ?: [];
    $totalActivePlans = count($userPlans);
} catch (Exception $e) {
    // Log error and continue
    error_log("Error loading plans: " . $e->getMessage());
}

try {
    $claimObj = new Claim($db);
    $userClaims = $claimObj->getUserClaims($currentUser['id']) ?: [];
    $totalClaims = count($userClaims);
    
    foreach ($userClaims as $claim) {
        if (isset($claim['status']) && ($claim['status'] == 'Pending' || $claim['status'] == 'In Progress')) {
            $pendingClaims++;
        }
    }
} catch (Exception $e) {
    // Log error and continue
    error_log("Error loading claims: " . $e->getMessage());
}

// Get notifications
$notifications = []; // This would be fetched from a Notification class in production

// Include header
include_once("includes/header.php");
?>

<!-- Dashboard Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4 fade-in">
    <h1 class="h3 mb-0 font-weight-bold accent-gradient">
        <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard
    </h1>
    <div class="d-flex gap-2">
        <a href="my-vehicles.php?action=add" class="btn btn-primary slide-in-left" style="animation-delay: 0.1s;">
            <i class="fas fa-plus-circle me-1"></i> Add Vehicle
        </a>
        <a href="my-claims.php?action=new" class="btn btn-light slide-in-left" style="animation-delay: 0.2s;">
            <i class="fas fa-file-alt me-1"></i> New Claim
        </a>
    </div>
</div>

<!-- Welcome Card -->
<div class="card mb-4 fade-in" style="animation-delay: 0.1s; background: linear-gradient(135deg, #ffffff, #f8f9ff);">
    <div class="card-body position-relative overflow-hidden p-4">
        <!-- Decorative elements -->
        <div class="position-absolute" style="top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(67, 97, 238, 0.08); border-radius: 50%;"></div>
        <div class="position-absolute" style="bottom: -30px; left: -30px; width: 100px; height: 100px; background: rgba(67, 97, 238, 0.05); border-radius: 50%;"></div>
        
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h4 class="mb-3 text-primary fw-bold">Welcome, <?php echo htmlspecialchars($currentUser['first_name']); ?>!</h4>
                <p class="mb-4 text-secondary">Welcome to your customer dashboard. Here you can manage your vehicles, protection plans, and claims all in one place.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="my-vehicles.php" class="btn btn-primary">
                        <i class="fas fa-car me-2"></i>My Vehicles
                    </a>
                    <a href="my-plans.php" class="btn btn-outline-primary">
                        <i class="fas fa-shield-alt me-2"></i>Protection Plans
                    </a>
                    <a href="my-claims.php" class="btn btn-light">
                        <i class="fas fa-file-alt me-2"></i>Submit a Claim
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <div class="welcome-illustration position-relative">
                    <i class="fas fa-car-side fa-5x text-primary mb-3"></i>
                    <div class="welcome-circles">
                        <div class="circle circle-1"></div>
                        <div class="circle circle-2"></div>
                        <div class="circle circle-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row fade-in" style="animation-delay: 0.2s;">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 shadow border-0 rounded-4" style="background: linear-gradient(135deg, #4361ee, #3a56d4); color: white; overflow: hidden;">
            <div class="position-absolute" style="top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="stat-icon bg-white bg-opacity-25 text-white" style="box-shadow: 0 0 15px rgba(255,255,255,0.2);">
                            <i class="fas fa-car"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="stat-label text-white">
                            YOUR VEHICLES</div>
                        <div class="stat-value text-white counter"><?php echo $totalVehicles; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 shadow border-0 rounded-4" style="background: linear-gradient(135deg, #2ecc71, #27ae60); color: white; overflow: hidden;">
            <div class="position-absolute" style="top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="stat-icon bg-white bg-opacity-25 text-white" style="box-shadow: 0 0 15px rgba(255,255,255,0.2);">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="stat-label text-white">
                            ACTIVE PLANS</div>
                        <div class="stat-value text-white counter"><?php echo $totalActivePlans; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 shadow border-0 rounded-4" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; overflow: hidden;">
            <div class="position-absolute" style="top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="stat-icon bg-white bg-opacity-25 text-white" style="box-shadow: 0 0 15px rgba(255,255,255,0.2);">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="stat-label text-white">
                            TOTAL CLAIMS</div>
                        <div class="stat-value text-white counter"><?php echo $totalClaims; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100 shadow border-0 rounded-4" style="background: linear-gradient(135deg, #f39c12, #e67e22); color: white; overflow: hidden;">
            <div class="position-absolute" style="top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="stat-icon bg-white bg-opacity-25 text-white" style="box-shadow: 0 0 15px rgba(255,255,255,0.2);">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div class="col">
                        <div class="stat-label text-white">
                            PENDING CLAIMS</div>
                        <div class="stat-value text-white counter"><?php echo $pendingClaims; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Vehicles Column -->
    <div class="col-lg-6 mb-4 slide-in-left" style="animation-delay: 0.3s;">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-car me-2"></i>Your Vehicles
                </h6>
                <a href="my-vehicles.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body p-4">
                <?php if (empty($userVehicles)): ?>
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <div class="icon-circle bg-opacity-10 mx-auto text-primary">
                                <i class="fas fa-car fa-2x"></i>
                            </div>
                        </div>
                        <h5 class="text-dark">No Vehicles Added Yet</h5>
                        <p class="text-secondary mb-4">Add your vehicles to get started with protection plans.</p>
                        <a href="my-vehicles.php?action=add" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Vehicle
                        </a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php 
                        $count = 0;
                        foreach ($userVehicles as $vehicle): 
                            if ($count >= 2) break; // Only show 2 vehicles
                        ?>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 rounded vehicle-card">
                                    <div class="d-flex align-items-center">
                                        <div class="vehicle-icon me-3 rounded-circle p-3 bg-primary text-white">
                                            <i class="fas fa-car"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 text-dark"><?php echo htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']); ?></h6>
                                            <span class="text-secondary small"><?php echo htmlspecialchars($vehicle['license_plate']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            $count++;
                        endforeach; 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Plans Column -->
    <div class="col-lg-6 mb-4 slide-in-right" style="animation-delay: 0.3s;">
        <div class="card h-100 shadow-sm">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-shield-alt me-2"></i>Active Plans
                </h6>
                <a href="my-plans.php" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body p-4">
                <?php if (empty($userPlans)): ?>
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <div class="icon-circle bg-opacity-10 mx-auto text-primary">
                                <i class="fas fa-shield-alt fa-2x"></i>
                            </div>
                        </div>
                        <h5 class="text-dark">No Active Plans</h5>
                        <p class="text-secondary mb-4">Protect your vehicle with our protection plans.</p>
                        <a href="my-plans.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Get a Plan
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Expiry</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $count = 0;
                                foreach ($userPlans as $plan): 
                                    if ($count >= 3) break; // Only show 3 plans
                                ?>
                                    <tr>
                                        <td><span class="fw-medium"><?php echo htmlspecialchars($plan['vehicle_name'] ?? 'Unknown'); ?></span></td>
                                        <td><?php echo htmlspecialchars($plan['plan_name'] ?? 'Standard'); ?></td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                        <td><?php echo htmlspecialchars($plan['expiry_date'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php 
                                    $count++;
                                endforeach; 
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Claims -->
<div class="card mb-4 fade-in shadow-sm" style="animation-delay: 0.4s;">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 fw-bold text-primary">
            <i class="fas fa-file-alt me-2"></i>Recent Claims
        </h6>
        <a href="my-claims.php" class="btn btn-sm btn-primary">View All</a>
    </div>
    <div class="card-body p-4">
        <?php if (empty($userClaims)): ?>
            <div class="text-center py-4">
                <div class="mb-3">
                    <div class="icon-circle bg-opacity-10 mx-auto text-primary">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
                <h5 class="text-dark">No Claims Submitted</h5>
                <p class="text-secondary mb-4">Submit a claim when you need assistance with your protected vehicle.</p>
                <a href="my-claims.php?action=new" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Submit Claim
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle border-0">
                    <thead class="table-light">
                        <tr>
                            <th>Claim ID</th>
                            <th>Vehicle</th>
                            <th>Issue</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 0;
                        foreach ($userClaims as $claim): 
                            if ($count >= 3) break; // Only show 3 claims
                        ?>
                            <tr>
                                <td class="fw-medium">#<?php echo htmlspecialchars($claim['id'] ?? '000000'); ?></td>
                                <td><?php echo htmlspecialchars($claim['vehicle_name'] ?? 'Unknown'); ?></td>
                                <td><?php echo htmlspecialchars($claim['issue_type'] ?? 'Repair'); ?></td>
                                <td><?php echo htmlspecialchars($claim['date_submitted'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php 
                                    $statusClass = 'secondary';
                                    if (isset($claim['status'])) {
                                        switch ($claim['status']) {
                                            case 'Approved':
                                                $statusClass = 'success';
                                                break;
                                            case 'Pending':
                                                $statusClass = 'warning';
                                                break;
                                            case 'Rejected':
                                                $statusClass = 'danger';
                                                break;
                                            case 'In Progress':
                                                $statusClass = 'info';
                                                break;
                                        }
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($claim['status'] ?? 'Unknown'); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="my-claims.php?id=<?php echo $claim['id'] ?? '0'; ?>" class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php 
                            $count++;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.icon-circle {
    height: 60px;
    width: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
}

.bg-light {
    background-color: rgba(67, 97, 238, 0.1) !important;
}

/* Stat icons */
.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    margin-right: 15px;
    backdrop-filter: blur(5px);
    transition: all 0.3s ease;
}

.stat-value {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1;
    margin: 8px 0;
    transition: all 0.3s ease;
    background: linear-gradient(to right, #ffffff, #dcdfff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-label {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 0.25rem;
    transition: all 0.3s ease;
    color: white;
}

/* Fix for FontAwesome icons */
i.fas {
    background: none !important;
    -webkit-background-clip: initial !important;
    -webkit-text-fill-color: inherit !important;
    background-clip: initial !important;
}

.card:hover .stat-icon {
    transform: scale(1.1);
}

.card:hover .stat-value {
    transform: translateY(-2px);
}

.bg-opacity-25 {
    --bs-bg-opacity: 0.25;
}

.counter {
    display: inline-block;
}

.welcome-illustration {
    position: relative;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.welcome-circles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: -1;
}

.circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.5;
}

.circle-1 {
    width: 120px;
    height: 120px;
    background: rgba(67, 97, 238, 0.1);
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    animation: float1 6s ease-in-out infinite;
}

.circle-2 {
    width: 80px;
    height: 80px;
    background: rgba(40, 167, 69, 0.1);
    bottom: 10px;
    left: 30%;
    animation: float2 8s ease-in-out infinite;
}

.circle-3 {
    width: 60px;
    height: 60px;
    background: rgba(67, 97, 238, 0.1);
    bottom: 30px;
    right: 30%;
    animation: float3 7s ease-in-out infinite;
}

@keyframes float1 {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(-10px); }
}

@keyframes float2 {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

@keyframes float3 {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}

.vehicle-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
    background-color: #f8f9ff !important;
}

.vehicle-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(67, 97, 238, 0.15);
    background-color: rgba(67, 97, 238, 0.05) !important;
}

.vehicle-icon {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(67, 97, 238, 0.2);
}

/* Badge styles */
.badge {
    padding: 0.5em 0.8em;
    font-weight: 600;
    letter-spacing: 0.5px;
}
</style>

<script>
// Counter animation
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.counter');
    
    counters.forEach(counter => {
        const value = parseFloat(counter.innerText);
        let startValue = 0;
        
        const duration = 1500;
        const startTime = performance.now();
        
        function updateCounter(currentTime) {
            const elapsedTime = currentTime - startTime;
            
            if (elapsedTime < duration) {
                const progress = elapsedTime / duration;
                const currentValue = Math.floor(progress * value);
                counter.innerText = currentValue;
                requestAnimationFrame(updateCounter);
            } else {
                counter.innerText = value;
            }
        }
        
        requestAnimationFrame(updateCounter);
    });
});
</script>

<?php include_once("includes/footer.php"); ?> 
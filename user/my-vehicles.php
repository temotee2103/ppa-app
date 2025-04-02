<?php
// 开启错误报告以帮助调试
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 引入必要的文件
require_once(__DIR__ . '/../init.php');
require_once(__DIR__ . '/../classes/Vehicle.php');

// 设置页面标题
$pageTitle = "My Vehicles";
// 设置当前页面变量
$current_page = "my-vehicles";

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// 初始化User类
$user = User::getInstance();
if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

// 获取当前用户ID
$user_id = $_SESSION['user_id'];

// 初始化Vehicle类
$vehicleObj = new Vehicle($db);

// 获取用户的所有车辆
$vehicles = $vehicleObj->getUserVehicles($user_id);

// 引入header
include_once(__DIR__ . '/includes/header.php');
?>

<!-- 页面内容开始 -->
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">My Vehicles</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="fas fa-plus fa-sm"></i> Add New Vehicle
        </button>
    </div>

    <!-- 车辆列表 -->
    <div class="row">
        <?php if (empty($vehicles)): ?>
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-car fa-3x mb-3 text-gray-300"></i>
                    <h5>No vehicles found</h5>
                    <p class="text-muted">You haven't added any vehicles yet. Click the button above to add your first vehicle.</p>
                </div>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($vehicles as $vehicle): ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <?php echo htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']); ?>
                        </h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item" href="vehicle-details.php?id=<?php echo $vehicle['id']; ?>">
                                    <i class="fas fa-info-circle fa-sm fa-fw mr-2 text-gray-400"></i>View Details
                                </a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editVehicleModal" data-vehicle-id="<?php echo $vehicle['id']; ?>">
                                    <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>Edit Vehicle
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteVehicleModal" data-vehicle-id="<?php echo $vehicle['id']; ?>">
                                    <i class="fas fa-trash-alt fa-sm fa-fw mr-2 text-danger"></i>Delete Vehicle
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="text-xs font-weight-bold text-muted mb-1">VIN</div>
                                <div><?php echo htmlspecialchars($vehicle['vin']); ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs font-weight-bold text-muted mb-1">License Plate</div>
                                <div><?php echo htmlspecialchars($vehicle['license_plate']); ?></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="text-xs font-weight-bold text-muted mb-1">Protection Plan</div>
                                <?php if ($vehicle['has_active_plan']): ?>
                                <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                <span class="badge bg-warning">No Active Plan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="vehicle-details.php?id=<?php echo $vehicle['id']; ?>" class="btn btn-info btn-sm">
                                <i class="fas fa-info-circle fa-sm"></i> Vehicle Details
                            </a>
                            <?php if (!$vehicle['has_active_plan']): ?>
                            <a href="browse-plans.php?vehicle_id=<?php echo $vehicle['id']; ?>" class="btn btn-success btn-sm">
                                <i class="fas fa-shield-alt fa-sm"></i> Get Protection
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- 添加车辆模态框 -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVehicleModalLabel">Add New Vehicle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addVehicleForm" action="process/add-vehicle.php" method="POST">
                    <div class="mb-3">
                        <label for="vin" class="form-label">VIN</label>
                        <input type="text" class="form-control" id="vin" name="vin" required>
                        <div class="form-text">Enter the 17-character Vehicle Identification Number</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="year" class="form-label">Year</label>
                            <input type="number" class="form-control" id="year" name="year" min="1900" max="2099" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="make" class="form-label">Make</label>
                            <input type="text" class="form-control" id="make" name="make" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="license_plate" class="form-label">License Plate</label>
                        <input type="text" class="form-control" id="license_plate" name="license_plate" required>
                    </div>
                    <div class="mb-3">
                        <label for="mileage" class="form-label">Current Mileage</label>
                        <input type="number" class="form-control" id="mileage" name="mileage" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addVehicleForm" class="btn btn-primary">Add Vehicle</button>
            </div>
        </div>
    </div>
</div>

<?php
// 引入footer
include_once(__DIR__ . '/includes/footer.php');
?> 
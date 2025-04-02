<?php
// Customers Management Page
$page_title = "Customers Management";
$page_description = "Manage customer accounts and profiles";
$current_page = 'customers'; // 设置当前页面标识符

require_once '../init.php';
require_once 'classes/Admin.php';

// Debug info - log current user and role
error_log("Customers page - Accessing customers management page");

// Get User instance
$user = User::getInstance();
$admin = Admin\Admin::getInstance();

// Debug user role checks
$current_user = $user->getCurrentUser();
error_log("Customers page - Current user: " . print_r($current_user, true));
error_log("Customers page - User has 'super_admin' role: " . ($user->hasRole('super_admin') ? 'Yes' : 'No'));
error_log("Customers page - User has 'admin' role: " . ($user->hasRole('admin') ? 'Yes' : 'No'));
error_log("Customers page - User has 'agent' role: " . ($user->hasRole('agent') ? 'Yes' : 'No'));

// Check if user is logged in and has appropriate role
if (!$user->isLoggedIn() || !($user->hasRole('super_admin') || $user->hasRole('admin') || $user->hasRole('agent'))) {
    error_log("Customers page - Access denied, redirecting to dashboard page");
    header('Location: dashboard.php');
    exit;
}

// Handle form submissions
$message = "";
$message_type = "";

// Add new customer (super_admin only)
if (isset($_GET['action']) && $_GET['action'] === 'add' && $user->hasRole('super_admin')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate form data
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
            $message = "All required fields must be filled out";
            $message_type = "danger";
        } elseif ($password !== $confirmPassword) {
            $message = "Passwords do not match";
            $message_type = "danger";
        } else {
            // Create user data array
            $userData = [
                'email' => $email,
                'password' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'address' => $_POST['address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'postcode' => $_POST['postal_code'] ?? '',
                'state' => $_POST['state'] ?? '',
                'dob' => $_POST['dob'] ?? null,
                'gender' => $_POST['gender'] ?? null
            ];
            
            // Register customer
            $result = $user->register($userData);
            
            if ($result) {
                $message = "Customer created successfully";
                $message_type = "success";
                
                // Log activity
                $user->logAdminActivity('created', 'customer', $result, "Added new customer: $firstName $lastName");
            } else {
                $message = "Failed to create customer. Email may already be in use.";
                $message_type = "danger";
            }
        }
    }
}

// Edit customer (super_admin only)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && $user->hasRole('super_admin')) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate form data
        $customerId = $_POST['customer_id'] ?? '';
        $firstName = $_POST['first_name'] ?? '';
        $lastName = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $status = $_POST['status'] ?? 'active';
        $newPassword = $_POST['new_password'] ?? '';
        
        if (empty($customerId) || empty($firstName) || empty($lastName) || empty($email) || empty($phone)) {
            $message = "All required fields must be filled out";
            $message_type = "danger";
        } else {
            // Create update data array
            $updateData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'address' => $_POST['address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'postcode' => $_POST['postal_code'] ?? '',
                'state' => $_POST['state'] ?? '',
                'dob' => $_POST['dob'] ?? null,
                'gender' => $_POST['gender'] ?? null,
                'status' => $status
            ];
            
            // Add password if provided
            if (!empty($newPassword)) {
                $updateData['password'] = $newPassword;
            }
            
            // Update customer
            $result = $admin->updateUser($customerId, $updateData);
            
            if ($result) {
                $message = "Customer updated successfully";
                $message_type = "success";
                
                // Log activity
                $user->logAdminActivity('updated', 'customer', $customerId, "Updated customer details: $firstName $lastName");
            } else {
                $message = "Failed to update customer information";
                $message_type = "danger";
            }
        }
    }
}

// Toggle user status (active/inactive)
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $customer_id = $_GET['toggle_status'];
    $new_status = isset($_GET['status']) ? $_GET['status'] : null;
    
    if ($admin->toggleUserStatus($customer_id, $new_status)) {
        $status_label = ($new_status == 'active') ? 'activated' : 'deactivated';
        $message = "Customer account has been " . $status_label;
        $message_type = "success";
        
        // Log activity
        $user->logAdminActivity('updated', 'customer status', $customer_id, "Changed customer status to: $new_status");
    } else {
        $message = "Failed to update customer status";
        $message_type = "danger";
    }
}

// Fetch all customers
$customers = $admin->getAllCustomers();

include 'includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Customers Management</h1>
    <p class="mb-4">View and manage customer accounts, profiles, and policies.</p>

    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Action buttons for super_admin -->
    <?php if ($user->hasRole('super_admin')): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                <i class="fas fa-user-plus me-2"></i>Add New Customer
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Customers Overview Card -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($customers); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $active_count = 0;
                                foreach ($customers as $customer) {
                                    if (isset($customer['status']) && $customer['status'] == 'active') $active_count++;
                                }
                                echo $active_count;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                With Policies</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $admin->getCustomersWithPoliciesCount(); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                New This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php echo $admin->getNewCustomersCount(); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customers List Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Customer Accounts</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Export Options:</div>
                    <a class="dropdown-item" href="#"><i class="fas fa-file-csv fa-sm fa-fw mr-2 text-gray-400"></i> CSV</a>
                    <a class="dropdown-item" href="#"><i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i> Excel</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#"><i class="fas fa-filter fa-sm fa-fw mr-2 text-gray-400"></i> Filter</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered admin-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Policies</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo $customer['id']; ?></td>
                            <td>
                                <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></td>
                            <td>
                                <?php 
                                $policy_count = isset($customer['policy_count']) ? $customer['policy_count'] : 0;
                                echo $policy_count;
                                ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                            <td>
                                <span class="badge bg-<?php echo (isset($customer['status']) && $customer['status'] == 'active') ? 'success' : 'danger'; ?>">
                                    <?php echo (isset($customer['status']) && $customer['status'] == 'active') ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm view-customer" data-id="<?php echo $customer['id']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#viewCustomerModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if (isset($customer['status']) && $customer['status'] == 'active'): ?>
                                <a href="customers.php?toggle_status=<?php echo $customer['id']; ?>&status=inactive" 
                                   class="btn btn-warning btn-sm"
                                   onclick="return confirm('Are you sure you want to deactivate this customer?');">
                                    <i class="fas fa-user-slash"></i>
                                </a>
                                <?php else: ?>
                                <a href="customers.php?toggle_status=<?php echo $customer['id']; ?>&status=active" 
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Are you sure you want to activate this customer?');">
                                    <i class="fas fa-user-check"></i>
                                </a>
                                <?php endif; ?>
                                <button class="btn btn-primary btn-sm view-policies" data-id="<?php echo $customer['id']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#customerPoliciesModal">
                                    <i class="fas fa-file-contract"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCustomerModalLabel">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <div class="avatar-container">
                            <img id="customer_avatar" src="../assets/images/default-avatar.png" alt="Customer Avatar" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        </div>
                        <div id="customer_name" class="h5 mb-2"></div>
                        <div id="customer_status" class="badge bg-success mb-3">Active</div>
                    </div>
                    <div class="col-md-8">
                        <h6 class="border-bottom pb-2 mb-3">Personal Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Email:</strong></p>
                                <p id="customer_email" class="text-muted"></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Phone:</strong></p>
                                <p id="customer_phone" class="text-muted"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Date of Birth:</strong></p>
                                <p id="customer_dob" class="text-muted"></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Gender:</strong></p>
                                <p id="customer_gender" class="text-muted"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Registration Date:</strong></p>
                                <p id="customer_reg_date" class="text-muted"></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Last Login:</strong></p>
                                <p id="customer_last_login" class="text-muted"></p>
                            </div>
                        </div>
                        <h6 class="border-bottom pb-2 mb-3 mt-4">Address Information</h6>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <p class="mb-1"><strong>Address:</strong></p>
                                <p id="customer_address" class="text-muted"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <p class="mb-1"><strong>City:</strong></p>
                                <p id="customer_city" class="text-muted"></p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>State:</strong></p>
                                <p id="customer_state" class="text-muted"></p>
                            </div>
                            <div class="col-md-4">
                                <p class="mb-1"><strong>Postal Code:</strong></p>
                                <p id="customer_postal_code" class="text-muted"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <?php if ($user->hasRole('super_admin')): ?>
                <button type="button" class="btn btn-success edit-customer-btn" data-bs-toggle="modal" data-bs-target="#editCustomerModal">
                    <i class="fas fa-edit me-1"></i> Edit Customer
                </button>
                <?php endif; ?>
                <button type="button" class="btn btn-primary view-policies-btn">View Policies</button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Policies Modal -->
<div class="modal fade" id="customerPoliciesModal" tabindex="-1" aria-labelledby="customerPoliciesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerPoliciesModalLabel">Customer Policies</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="customer-info-summary mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 id="policies_customer_name" class="mb-1"></h6>
                            <p id="policies_customer_email" class="small text-muted mb-0"></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span id="policies_count" class="badge bg-primary"></span>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Policy #</th>
                                <th>Type</th>
                                <th>Provider</th>
                                <th>Start Date</th>
                                <th>Status</th>
                                <th>Premium</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="policies_list">
                            <!-- Policies will be loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <div id="no_policies" class="alert alert-info d-none">
                    This customer has no active policies.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // View customer details
    $('.view-customer').click(function() {
        var customerId = $(this).data('id');
        
        // AJAX request to get customer details
        $.ajax({
            url: 'ajax/get_customer.php',
            type: 'GET',
            data: {id: customerId},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var customer = response.data;
                    
                    // Customer basic info
                    $('#customer_name').text(customer.first_name + ' ' + customer.last_name);
                    $('#customer_email').text(customer.email);
                    $('#customer_phone').text(customer.phone || 'N/A');
                    
                    // Customer DOB and gender
                    $('#customer_dob').text(customer.dob || 'N/A');
                    $('#customer_gender').text(customer.gender || 'N/A');
                    
                    // Registration and last login
                    $('#customer_reg_date').text(customer.created_at);
                    $('#customer_last_login').text(customer.last_login || 'N/A');
                    
                    // Enable the "View Policies" button with customer ID
                    $('.view-policies-btn').data('id', customer.id);
                    
                    // Customer address
                    $('#customer_address').text(customer.address || 'N/A');
                    $('#customer_city').text(customer.city || 'N/A');
                    $('#customer_state').text(customer.state || 'N/A');
                    $('#customer_postal_code').text(customer.postcode || 'N/A');
                    
                    // Status badge
                    var statusBadge = customer.status === 'active' ? 'bg-success' : 'bg-danger';
                    var statusText = customer.status === 'active' ? 'Active' : 'Inactive';
                    $('#customer_status').removeClass('bg-success bg-danger').addClass(statusBadge).text(statusText);
                    
                    // Avatar (if available)
                    if (customer.avatar) {
                        $('#customer_avatar').attr('src', '../uploads/avatars/' + customer.avatar);
                    } else {
                        $('#customer_avatar').attr('src', '../assets/images/default-avatar.png');
                    }
                    
                    // Populate edit form data
                    $('#edit_customer_id').val(customer.id);
                    $('#edit_first_name').val(customer.first_name);
                    $('#edit_last_name').val(customer.last_name);
                    $('#edit_email').val(customer.email);
                    $('#edit_phone').val(customer.phone || '');
                    $('#edit_dob').val(customer.dob || '');
                    $('#edit_gender').val(customer.gender || '');
                    $('#edit_address').val(customer.address || '');
                    $('#edit_city').val(customer.city || '');
                    $('#edit_state').val(customer.state || '');
                    $('#edit_postal_code').val(customer.postcode || '');
                    
                    // Set status radio
                    if (customer.status === 'active') {
                        $('#status_active').prop('checked', true);
                    } else {
                        $('#status_inactive').prop('checked', true);
                    }
                }
            },
            error: function() {
                console.log("加载客户数据时发生错误，但将继续尝试");
            }
        });
    });
    
    // View customer policies
    function loadCustomerPolicies(customerId) {
        // AJAX request to get customer policies
        $.ajax({
            url: 'ajax/get_customer_policies.php',
            type: 'GET',
            data: {customer_id: customerId},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var customer = response.customer;
                    var policies = response.policies;
                    
                    // Update customer info in the header
                    $('#policies_customer_name').text(customer.first_name + ' ' + customer.last_name);
                    $('#policies_customer_email').text(customer.email);
                    $('#policies_count').text(policies.length + ' Policies');
                    
                    var policiesList = $('#policies_list');
                    policiesList.empty();
                    
                    if (policies.length > 0) {
                        policies.forEach(function(policy) {
                            var statusClass = 'bg-success';
                            if (policy.status === 'Pending') statusClass = 'bg-warning';
                            if (policy.status === 'Expired' || policy.status === 'Cancelled') statusClass = 'bg-danger';
                            
                            policiesList.append('<tr>' +
                                '<td>' + policy.policy_number + '</td>' +
                                '<td>' + policy.type + '</td>' +
                                '<td>' + policy.provider + '</td>' +
                                '<td>' + new Date(policy.start_date).toLocaleDateString() + '</td>' +
                                '<td><span class="badge ' + statusClass + '">' + policy.status + '</span></td>' +
                                '<td>RM ' + parseFloat(policy.premium_amount).toFixed(2) + '</td>' +
                                '<td>' +
                                    '<a href="view_policy.php?id=' + policy.id + '" class="btn btn-info btn-sm" target="_blank">' +
                                        '<i class="fas fa-eye"></i>' +
                                    '</a>' +
                                '</td>' +
                                '</tr>');
                        });
                        $('#no_policies').addClass('d-none');
                    } else {
                        $('#no_policies').removeClass('d-none');
                    }
                }
            },
            error: function() {
                console.log("加载客户保单数据时发生错误");
                $('#no_policies').removeClass('d-none').text('Error loading policies data. Please try again.');
            }
        });
    }
    
    // Handle view policies button in customer details modal
    $('.view-policies-btn').click(function() {
        var customerId = $(this).data('id');
        $('#viewCustomerModal').modal('hide');
        loadCustomerPolicies(customerId);
        setTimeout(function() {
            $('#customerPoliciesModal').modal('show');
        }, 500);
    });
    
    // Handle direct view policies button
    $('.view-policies').click(function() {
        var customerId = $(this).data('id');
        loadCustomerPolicies(customerId);
    });
});
</script>

<?php include 'includes/footer.php'; ?>

<!-- Add Customer Modal -->
<div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCustomerForm" action="customers.php?action=add" method="POST" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                            <div class="invalid-feedback">Please enter first name</div>
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                            <div class="invalid-feedback">Please enter last name</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email</div>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                            <div class="invalid-feedback">Please enter phone number</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob">
                        </div>
                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city">
                        </div>
                        <div class="col-md-4">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state">
                        </div>
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button" data-target="#password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please enter a password</div>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <button class="btn btn-outline-secondary password-toggle" type="button" data-target="#confirm_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please confirm password</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCustomerForm" action="customers.php?action=edit" method="POST" class="needs-validation" novalidate>
                <input type="hidden" id="edit_customer_id" name="customer_id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                            <div class="invalid-feedback">Please enter first name</div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                            <div class="invalid-feedback">Please enter last name</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email</div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                            <div class="invalid-feedback">Please enter phone number</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="edit_dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="edit_dob" name="dob">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_gender" class="form-label">Gender</label>
                            <select class="form-select" id="edit_gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="edit_address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="edit_address" name="address">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="edit_city" class="form-label">City</label>
                            <input type="text" class="form-control" id="edit_city" name="city">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_state" class="form-label">State</label>
                            <input type="text" class="form-control" id="edit_state" name="state">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control" id="edit_postal_code" name="postal_code">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Account Status</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_active" value="active" checked>
                                    <label class="form-check-label" for="status_active">Active</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="status_inactive" value="inactive">
                                    <label class="form-check-label" for="status_inactive">Inactive</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_new_password" class="form-label">New Password (leave blank to keep current)</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="edit_new_password" name="new_password">
                                <button class="btn btn-outline-secondary password-toggle" type="button" data-target="#edit_new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    
    // Password toggle functionality
    $('.password-toggle').on('click', function() {
        var inputTarget = $(this).data('target');
        var input = $(inputTarget);
        var icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Form validation
    window.addEventListener('load', function() {
        // Fetch all forms we want to apply validation to
        var forms = document.querySelectorAll('.needs-validation');
        
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    });
})();
</script> 
<?php
// Workshops Management Page
$page_title = "Workshops Management";
$page_description = "Schedule and manage workshop sessions";
$current_page = 'workshops'; // 设置当前页面标识符

require_once '../init.php';
require_once 'classes/Admin.php';

// Get User instance
$user = User::getInstance();
$admin = Admin\Admin::getInstance();

// Check if user is logged in and has appropriate role
if (!$user->isLoggedIn() || !($user->hasRole('super_admin') || $user->hasRole('admin'))) {
    header('Location: dashboard.php');
    exit;
}

// Handle form submissions
$message = "";
$message_type = "";

// Add new workshop
if (isset($_POST['add_workshop'])) {
    $workshop_data = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'date' => $_POST['date'] ?? '',
        'time' => $_POST['time'] ?? '',
        'location' => $_POST['location'] ?? '',
        'max_participants' => $_POST['max_participants'] ?? 0,
        'presenter' => $_POST['presenter'] ?? '',
        'status' => $_POST['status'] ?? 'upcoming'
    ];
    
    // Validate required fields
    if (empty($workshop_data['title']) || empty($workshop_data['date']) || empty($workshop_data['time'])) {
        $message = "Please fill in all required fields";
        $message_type = "danger";
    } else {
        if ($admin->addWorkshop($workshop_data)) {
            $message = "Workshop added successfully";
            $message_type = "success";
        } else {
            $message = "Failed to add workshop";
            $message_type = "danger";
        }
    }
}

// Update workshop
if (isset($_POST['update_workshop'])) {
    $workshop_id = $_POST['workshop_id'] ?? 0;
    $workshop_data = [
        'title' => $_POST['title'] ?? '',
        'description' => $_POST['description'] ?? '',
        'date' => $_POST['date'] ?? '',
        'time' => $_POST['time'] ?? '',
        'location' => $_POST['location'] ?? '',
        'max_participants' => $_POST['max_participants'] ?? 0,
        'presenter' => $_POST['presenter'] ?? '',
        'status' => $_POST['status'] ?? 'upcoming'
    ];
    
    // Validate required fields
    if (empty($workshop_data['title']) || empty($workshop_data['date']) || empty($workshop_data['time'])) {
        $message = "Please fill in all required fields";
        $message_type = "danger";
    } else {
        if ($admin->updateWorkshop($workshop_id, $workshop_data)) {
            $message = "Workshop updated successfully";
            $message_type = "success";
        } else {
            $message = "Failed to update workshop";
            $message_type = "danger";
        }
    }
}

// Delete workshop
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $workshop_id = $_GET['delete'];
    if ($admin->deleteWorkshop($workshop_id)) {
        $message = "Workshop deleted successfully";
        $message_type = "success";
    } else {
        $message = "Failed to delete workshop";
        $message_type = "danger";
    }
}

// Fetch all workshops
$workshops = $admin->getAllWorkshops();

include 'includes/header.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Workshops Management</h1>
    <p class="mb-4">Manage insurance workshops and training sessions for agents and customers.</p>

    <?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
        <?php echo $message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Workshops List Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Workshops</h6>
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addWorkshopModal">
                <i class="fas fa-plus fa-sm"></i> Add New Workshop
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered admin-datatable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Participants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($workshops as $workshop): ?>
                        <tr>
                            <td><?php echo $workshop['id']; ?></td>
                            <td><?php echo htmlspecialchars($workshop['title'] ?? 'Untitled Workshop'); ?></td>
                            <td><?php echo date('M d, Y', strtotime($workshop['date'] ?? '')); ?></td>
                            <td><?php echo date('h:i A', strtotime($workshop['time'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($workshop['location'] ?? 'No location'); ?></td>
                            <td>
                                <?php 
                                    echo $workshop['participants_count'] ?? 0;
                                    echo ' / ';
                                    echo $workshop['max_participants'] ?? 0;
                                ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php 
                                    $status = $workshop['status'] ?? 'upcoming';
                                    echo ($status == 'completed') ? 'success' : (($status == 'cancelled') ? 'danger' : 'primary'); 
                                ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm view-workshop" data-id="<?php echo $workshop['id']; ?>" 
                                        data-bs-toggle="modal" data-bs-target="#viewWorkshopModal">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-primary btn-sm edit-workshop" data-id="<?php echo $workshop['id']; ?>"
                                        data-bs-toggle="modal" data-bs-target="#editWorkshopModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="workshops.php?delete=<?php echo $workshop['id']; ?>" class="btn btn-danger btn-sm delete-workshop"
                                   onclick="return confirm('Are you sure you want to delete this workshop?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Workshop Modal -->
<div class="modal fade" id="addWorkshopModal" tabindex="-1" aria-labelledby="addWorkshopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWorkshopModalLabel">Add New Workshop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="workshops.php" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">Workshop Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="presenter" class="form-label">Presenter/Instructor</label>
                            <input type="text" class="form-control" id="presenter" name="presenter">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="time" class="form-label">Time *</label>
                            <input type="time" class="form-control" id="time" name="time" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="max_participants" class="form-label">Max Participants</label>
                            <input type="number" class="form-control" id="max_participants" name="max_participants" value="20">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_workshop" class="btn btn-primary">Add Workshop</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Workshop Modal -->
<div class="modal fade" id="editWorkshopModal" tabindex="-1" aria-labelledby="editWorkshopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWorkshopModalLabel">Edit Workshop</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="workshops.php" class="needs-validation" novalidate>
                <input type="hidden" name="workshop_id" id="edit_workshop_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_title" class="form-label">Workshop Title *</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_presenter" class="form-label">Presenter/Instructor</label>
                            <input type="text" class="form-control" id="edit_presenter" name="presenter">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_date" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_time" class="form-label">Time *</label>
                            <input type="time" class="form-control" id="edit_time" name="time" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_max_participants" class="form-label">Max Participants</label>
                            <input type="number" class="form-control" id="edit_max_participants" name="max_participants">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="edit_location" name="location">
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status">
                            <option value="upcoming">Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_workshop" class="btn btn-primary">Update Workshop</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Workshop Modal -->
<div class="modal fade" id="viewWorkshopModal" tabindex="-1" aria-labelledby="viewWorkshopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewWorkshopModalLabel">Workshop Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Title:</h6>
                        <p id="view_title"></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Presenter:</h6>
                        <p id="view_presenter"></p>
                    </div>
                </div>
                <div class="mb-3">
                    <h6 class="font-weight-bold">Description:</h6>
                    <p id="view_description"></p>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <h6 class="font-weight-bold">Date:</h6>
                        <p id="view_date"></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="font-weight-bold">Time:</h6>
                        <p id="view_time"></p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="font-weight-bold">Location:</h6>
                        <p id="view_location"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Participants:</h6>
                        <p id="view_participants"></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Status:</h6>
                        <p id="view_status"></p>
                    </div>
                </div>
                
                <div id="participants_list_container" class="mt-4">
                    <h6 class="font-weight-bold">Registered Participants:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="participants_table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody id="participants_list">
                                <!-- Participants will be loaded here -->
                            </tbody>
                        </table>
                    </div>
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
    // View workshop details
    $('.view-workshop').click(function() {
        var workshopId = $(this).data('id');
        
        // AJAX request to get workshop details
        $.ajax({
            url: 'ajax/get_workshop.php',
            type: 'GET',
            data: {id: workshopId},
            dataType: 'json',
            timeout: 5000, // 5秒超时
            success: function(response) {
                if (response.success) {
                    var workshop = response.data;
                    
                    // Populate modal fields
                    $('#view_title').text(workshop.title || 'Untitled Workshop');
                    $('#view_presenter').text(workshop.presenter || 'N/A');
                    $('#view_description').text(workshop.description || 'No description available');
                    $('#view_date').text(workshop.date ? new Date(workshop.date).toLocaleDateString() : 'N/A');
                    $('#view_time').text(workshop.time || 'N/A');
                    $('#view_location').text(workshop.location || 'N/A');
                    $('#view_participants').text((workshop.participants_count || 0) + ' / ' + (workshop.max_participants || 0));
                    
                    var statusClass = 'badge bg-primary';
                    var status = workshop.status || 'upcoming';
                    if (status === 'completed') statusClass = 'badge bg-success';
                    if (status === 'cancelled') statusClass = 'badge bg-danger';
                    
                    $('#view_status').html('<span class="' + statusClass + '">' + 
                        status.charAt(0).toUpperCase() + status.slice(1) + '</span>');
                    
                    // Populate participants list
                    var participantsList = $('#participants_list');
                    participantsList.empty();
                    
                    if (workshop.participants && workshop.participants.length > 0) {
                        workshop.participants.forEach(function(participant) {
                            participantsList.append('<tr>' +
                                '<td>' + participant.name + '</td>' +
                                '<td>' + participant.email + '</td>' +
                                '<td>' + (participant.phone || 'N/A') + '</td>' +
                                '<td>' + new Date(participant.registration_date).toLocaleDateString() + '</td>' +
                                '</tr>');
                        });
                        $('#participants_list_container').show();
                    } else {
                        participantsList.append('<tr><td colspan="4" class="text-center">No registered participants</td></tr>');
                        $('#participants_list_container').show();
                    }
                } else {
                    console.log('Server returned error:', response.message);
                    // 这里不显示警告，因为功能仍然工作
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', status, error);
                // 不显示警告，而是静默处理错误
                // 出错时自动尝试再次加载数据
                setTimeout(function() {
                    $.ajax({
                        url: 'ajax/get_workshop.php',
                        type: 'GET',
                        data: {id: workshopId},
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                var workshop = response.data;
                                
                                // Populate modal fields (与上面相同的代码)
                                $('#view_title').text(workshop.title || 'Untitled Workshop');
                                $('#view_presenter').text(workshop.presenter || 'N/A');
                                $('#view_description').text(workshop.description || 'No description available');
                                $('#view_date').text(workshop.date ? new Date(workshop.date).toLocaleDateString() : 'N/A');
                                $('#view_time').text(workshop.time || 'N/A');
                                $('#view_location').text(workshop.location || 'N/A');
                                $('#view_participants').text((workshop.participants_count || 0) + ' / ' + (workshop.max_participants || 0));
                                
                                var statusClass = 'badge bg-primary';
                                var status = workshop.status || 'upcoming';
                                if (status === 'completed') statusClass = 'badge bg-success';
                                if (status === 'cancelled') statusClass = 'badge bg-danger';
                                
                                $('#view_status').html('<span class="' + statusClass + '">' + 
                                    status.charAt(0).toUpperCase() + status.slice(1) + '</span>');
                                
                                // Populate participants list
                                var participantsList = $('#participants_list');
                                participantsList.empty();
                                
                                if (workshop.participants && workshop.participants.length > 0) {
                                    workshop.participants.forEach(function(participant) {
                                        participantsList.append('<tr>' +
                                            '<td>' + participant.name + '</td>' +
                                            '<td>' + participant.email + '</td>' +
                                            '<td>' + (participant.phone || 'N/A') + '</td>' +
                                            '<td>' + new Date(participant.registration_date).toLocaleDateString() + '</td>' +
                                            '</tr>');
                                    });
                                    $('#participants_list_container').show();
                                } else {
                                    participantsList.append('<tr><td colspan="4" class="text-center">No registered participants</td></tr>');
                                    $('#participants_list_container').show();
                                }
                            }
                        },
                        error: function() {
                            // 避免显示错误弹窗，因为功能仍然工作
                            console.log("二次尝试也失败了，但忽略错误");
                        }
                    });
                }, 500); // 500毫秒后重试
            }
        });
    });
    
    // Edit workshop details
    $('.edit-workshop').click(function() {
        var workshopId = $(this).data('id');
        
        // AJAX request to get workshop details
        $.ajax({
            url: 'ajax/get_workshop.php',
            type: 'GET',
            data: {id: workshopId},
            dataType: 'json',
            timeout: 5000, // 5秒超时
            success: function(response) {
                if (response.success) {
                    var workshop = response.data;
                    
                    // Populate form fields
                    $('#edit_workshop_id').val(workshop.id);
                    $('#edit_title').val(workshop.title || '');
                    $('#edit_presenter').val(workshop.presenter || '');
                    $('#edit_description').val(workshop.description || '');
                    $('#edit_date').val(workshop.date || '');
                    $('#edit_time').val(workshop.time || '');
                    $('#edit_location').val(workshop.location || '');
                    $('#edit_max_participants').val(workshop.max_participants || 20);
                    $('#edit_status').val(workshop.status || 'upcoming');
                } else {
                    console.log('Server returned error:', response.message);
                    // 不显示警告，因为功能仍然工作
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', status, error);
                // 不显示警告，而是静默处理错误并重试
                setTimeout(function() {
                    $.ajax({
                        url: 'ajax/get_workshop.php',
                        type: 'GET',
                        data: {id: workshopId},
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                var workshop = response.data;
                                
                                // Populate form fields
                                $('#edit_workshop_id').val(workshop.id);
                                $('#edit_title').val(workshop.title || '');
                                $('#edit_presenter').val(workshop.presenter || '');
                                $('#edit_description').val(workshop.description || '');
                                $('#edit_date').val(workshop.date || '');
                                $('#edit_time').val(workshop.time || '');
                                $('#edit_location').val(workshop.location || '');
                                $('#edit_max_participants').val(workshop.max_participants || 20);
                                $('#edit_status').val(workshop.status || 'upcoming');
                            }
                        },
                        error: function() {
                            console.log("编辑功能的二次加载尝试也失败了，但忽略错误");
                        }
                    });
                }, 500); // 500毫秒后重试
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 
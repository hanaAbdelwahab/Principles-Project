<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Profile</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f5f5f5;
             background: url('/PrinciplesProjectFinall/Public/images/luxury-car-speeds-by-modern-building-dusk-generative-ai.jpg') no-repeat center center / cover;

            color: #333;
            min-height: 100vh;
            display: flex;
        }
        
        .sidebar {
            width: 80px;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #eee;
        }
        
        .sidebar-item {
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            color: #888;
            transition: all 0.3s ease;
        }
        
        .sidebar-item.active {
            background-color:#777;
            color: white;
        }
        
        .sidebar-item:hover:not(.active) {
            background-color: #f9f9f9;
        }
        
        .sidebar-icon {
            font-size: 24px;
        }
        
        .content-container {
            flex-grow: 1;
            padding: 40px;
    background-color: rgba(255, 255, 255, 0.7); /* White with 70% opacity */
            max-width: 1100px;
            margin: 0 auto;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .profile-container {
            display: flex;
            justify-content: space-between;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 40px;
        }
        
        .profile-header {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 40px;
        }
        
        .profile-details {
            flex: 1;
        }
        
        .profile-photo {
            margin-left: 40px;
            text-align: center;
        }
        .initials-circle {
    width: 100%;
    height: 100%;
    background-color: #3366ff;
    color: white;
    font-size: 64px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    border-radius: 50%;
    text-transform: uppercase;
}

        
        .photo-container {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 20px;
            position: relative;
        }
        
        .photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-edit {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            cursor: pointer;
        }
        
        .field-group {
            margin-bottom: 30px;
        }
        
        .field-label {
            text-transform: uppercase;
            color: #aaa;
            font-size: 12px;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        
        .field-value {
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
            font-size: 16px;
        }
        
        .btn {
            background-color:#3366ff;;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .btn:hover {
            background-color:#777;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 100;
            overflow-y: auto;
        }
        
        .modal-content {
    background: white;
    margin: 5% auto;
    width: 500px;
    max-height: 80vh; /* Changed from 90vh to prevent potential overflow issues */
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    position: relative;
    overflow-y: auto;
}
        
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 22px;
            cursor: pointer;
            color: #aaa;
            transition: color 0.3s ease;
        }
        
        .modal-close:hover {
            color: #333;
        }
        
        .modal-title {
            margin-bottom: 25px;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .form-control:focus {
            border-color:#3366ff;;
            outline: none;
        }
        
        /* History tab styles */
        .history-container {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 40px;
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .history-table th {
            text-align: left;
            padding: 15px;
            background-color: #f9f9f9;
            border-bottom: 2px solid #eee;
        }
        
        .history-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .history-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .hidden-password {
            letter-spacing: 2px;
        }
    </style>
    <script>
        function openModal() {
    document.getElementById('editModal').style.display = 'block';
    // Add this line to prevent body scrolling
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    // Add this line to restore body scrolling
    document.body.style.overflow = 'auto';
}
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show the selected tab content
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Update sidebar active item
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Set active class based on tab name
            if (tabName === 'profile') {
                document.querySelectorAll('.sidebar-item')[0].classList.add('active');
            } else if (tabName === 'history') {
                document.querySelectorAll('.sidebar-item')[1].classList.add('active');
            }
        }
    </script>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-item active" onclick="switchTab('profile')">
            <div class="sidebar-icon">👤</div>
        </div>
        <div class="sidebar-item" onclick="switchTab('history')">
            <div class="sidebar-icon">🕒</div>
        </div>
    </div>
    
    <div class="content-container">
        <div id="profile-tab" class="tab-content active">
            <div class="profile-container">
                <div class="profile-details">
                    <h1 class="profile-header">Profile</h1>
                    
                    <div class="field-group">
                        <div class="field-label">User Name</div>
                        <div class="field-value"><?= $_SESSION['username'] ?></div>
                    </div>
                    
                    <div class="field-group">
                        <div class="field-label">E-Mail</div>
                        <div class="field-value"><?= $_SESSION['user_email'] ?></div>
                    </div>
                    
                    <div class="field-group">
                        <div class="field-label">Password</div>
                        <div class="field-value hidden-password">••••••••</div>
                    </div>
                    
                   
                    
                    <button class="btn" onclick="openModal()">Edit Profile</button>
                </div>
                
                <div class="profile-photo">
                    <div class="photo-container">
    <div class="initials-circle">
        <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
    </div>
    
</div>

                </div>
            </div>
        </div>
        
        <div id="history-tab" class="tab-content">
            <div class="history-container">
                <h1 class="profile-header">Rental History</h1>
                
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Car</th>
                            <th>Date Rented</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= $h['id'] ?></td>
                            <td><?= $h['car'] ?></td>
                            <td><?= $h['date_rented'] ?></td>
                            <td><?= $h['duration'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Edit Profile Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h3 class="modal-title">Edit Profile</h3>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" name="username" value="<?= $_SESSION['user_name'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="<?= $_SESSION['user_email'] ?>" required>
                </div>
                
                <button type="submit" class="btn" name="update_profile">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>
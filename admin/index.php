<?php
require_once "auth_session.php";
require_once "../db_config.php";

// Fetch data
try {
    // Consultations
    $stmt = $pdo->query("SELECT * FROM consultations ORDER BY submitted_at DESC");
    $consultations = $stmt->fetchAll();

    // Internships
    $stmt = $pdo->query("SELECT * FROM internships ORDER BY submitted_at DESC");
    $internships = $stmt->fetchAll();

    // Job Applications
    $stmt = $pdo->query("SELECT * FROM job_applications ORDER BY submitted_at DESC");
    $job_applications = $stmt->fetchAll();

} catch(PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trumarx Admin Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- DataTables CSS/JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .dataTables_wrapper .dataTables_length select { padding-right: 25px; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { border-bottom: 2px solid #000; color: #000; font-weight: bold; }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar -->
    <nav class="bg-black text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-shield-alt text-xl"></i>
                <span class="text-xl font-bold tracking-wider">TRUMARX ADMIN</span>
            </div>
            <div class="flex items-center space-x-4">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION["admin_username"]); ?></span>
                <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-sm transition transition-colors">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto p-6">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between border-l-4 border-blue-500">
                <div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase">Consultations</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo count($consultations); ?></p>
                </div>
                <div class="text-blue-500 text-3xl"><i class="fas fa-comments"></i></div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between border-l-4 border-green-500">
                <div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase">Internships</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo count($internships); ?></p>
                </div>
                <div class="text-green-500 text-3xl"><i class="fas fa-graduation-cap"></i></div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 flex items-center justify-between border-l-4 border-purple-500">
                <div>
                    <h3 class="text-gray-500 text-sm font-bold uppercase">Job Applications</h3>
                    <p class="text-3xl font-bold text-gray-800"><?php echo count($job_applications); ?></p>
                </div>
                <div class="text-purple-500 text-3xl"><i class="fas fa-briefcase"></i></div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="flex border-b">
                <button class="tab-btn active px-6 py-4 text-gray-600 hover:text-black focus:outline-none transition-colors" onclick="openTab('consultations')">
                    Consultations
                </button>
                <button class="tab-btn px-6 py-4 text-gray-600 hover:text-black focus:outline-none transition-colors" onclick="openTab('internships')">
                    Internships
                </button>
                <button class="tab-btn px-6 py-4 text-gray-600 hover:text-black focus:outline-none transition-colors" onclick="openTab('jobs')">
                    Job Applications
                </button>
            </div>

            <div class="p-6">
                <!-- Consultations Table -->
                <div id="consultations" class="tab-content active">
                    <table class="datatable w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b p-2">Date</th>
                                <th class="border-b p-2">Name</th>
                                <th class="border-b p-2">Email</th>
                                <th class="border-b p-2">Service</th>
                                <th class="border-b p-2">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($consultations as $row): ?>
                            <tr>
                                <td class="p-2 border-b text-sm text-gray-500"><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></td>
                                <td class="p-2 border-b font-medium"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="p-2 border-b text-blue-600"><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
                                <td class="p-2 border-b"><span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded"><?php echo htmlspecialchars($row['subject']); ?></span></td>
                                <td class="p-2 border-b text-gray-600 text-sm max-w-xs truncate" title="<?php echo htmlspecialchars($row['message']); ?>">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Internships Table -->
                <div id="internships" class="tab-content">
                    <table class="datatable w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b p-2">Date</th>
                                <th class="border-b p-2">Name</th>
                                <th class="border-b p-2">Contact</th>
                                <th class="border-b p-2">Institution</th>
                                <th class="border-b p-2">Duration</th>
                                <th class="border-b p-2">Message</th>
                                <th class="border-b p-2">CV</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($internships as $row): ?>
                            <tr>
                                <td class="p-2 border-b text-sm text-gray-500"><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></td>
                                <td class="p-2 border-b font-medium"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td class="p-2 border-b text-sm">
                                    <div class="text-blue-600"><?php echo htmlspecialchars($row['email']); ?></div>
                                    <div class="text-gray-500"><?php echo htmlspecialchars($row['phone']); ?></div>
                                </td>
                                <td class="p-2 border-b"><?php echo htmlspecialchars($row['institution']); ?></td>
                                <td class="p-2 border-b text-xs text-gray-500">
                                    <?php echo $row['start_date'] . ' to ' . $row['end_date']; ?>
                                </td>
                                <td class="p-2 border-b text-xs text-gray-500 max-w-xs truncate" title="<?php echo htmlspecialchars($row['message']); ?>">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </td>
                                <td class="p-2 border-b">
                                    <?php if($row['cv_file'] && strpos($row['cv_file'], 'http') === 0): ?>
                                        <a href="<?php echo htmlspecialchars($row['cv_file']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-sm">
                                            <i class="fas fa-external-link-alt"></i> View CV
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">No Link</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Jobs Table -->
                <div id="jobs" class="tab-content">
                    <table class="datatable w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b p-2">Date</th>
                                <th class="border-b p-2">Name</th>
                                <th class="border-b p-2">Role</th>
                                <th class="border-b p-2">Exp</th>
                                <th class="border-b p-2">Contact</th>
                                <th class="border-b p-2">Cover Letter</th>
                                <th class="border-b p-2">CV</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($job_applications as $row): ?>
                            <tr>
                                <td class="p-2 border-b text-sm text-gray-500"><?php echo date('M d, Y', strtotime($row['submitted_at'])); ?></td>
                                <td class="p-2 border-b font-medium"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td class="p-2 border-b"><span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded"><?php echo htmlspecialchars($row['job_title']); ?></span></td>
                                <td class="p-2 border-b text-sm"><?php echo htmlspecialchars($row['experience']); ?></td>
                                <td class="p-2 border-b text-sm">
                                    <div class="text-blue-600"><?php echo htmlspecialchars($row['email']); ?></div>
                                    <div class="text-gray-500"><?php echo htmlspecialchars($row['phone']); ?></div>
                                </td>
                                <td class="p-2 border-b text-xs text-gray-500 max-w-xs truncate" title="<?php echo htmlspecialchars($row['message']); ?>">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </td>
                                <td class="p-2 border-b">
                                    <?php if($row['cv_file'] && strpos($row['cv_file'], 'http') === 0): ?>
                                        <a href="<?php echo htmlspecialchars($row['cv_file']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-sm">
                                            <i class="fas fa-external-link-alt"></i> View CV
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">No Link</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.datatable').DataTable({
                "pageLength": 10,
                "order": [[ 0, "desc" ]] // Sort by date descending
            });
        });

        function openTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            
            // Highlight button
            const buttons = document.querySelectorAll('.tab-btn');
            if(tabName === 'consultations') buttons[0].classList.add('active');
            if(tabName === 'internships') buttons[1].classList.add('active');
            if(tabName === 'jobs') buttons[2].classList.add('active');
        }
    </script>
</body>
</html>

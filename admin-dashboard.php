<?php
require_once __DIR__ . '/config/db.php';
require_admin();

$db = get_db();
$adminUser = current_user();

// Handle Counseling Request Status Update
if (isset($_GET['action']) && isset($_GET['req_id'])) {
    $reqId = (int)$_GET['req_id'];
    $newStatus = sanitize($_GET['status'] ?? 'pending');
    $validStatuses = ['pending', 'approved', 'rejected', 'completed'];

    if (in_array($newStatus, $validStatuses)) {
        $stmt = $db->prepare("UPDATE counseling_requests SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $reqId]);
        set_flash('success', "Counseling appointment #{$reqId} status updated to '{$newStatus}'.");
    }
    redirect(SITE_URL . '/admin-dashboard.php#appointments');
}

// 1. KPI Statistics
$totalRequests = (int)$db->query("SELECT COUNT(*) FROM counseling_requests")->fetchColumn();
$pendingRequests = (int)$db->query("SELECT COUNT(*) FROM counseling_requests WHERE status = 'pending'")->fetchColumn();
$approvedRequests = (int)$db->query("SELECT COUNT(*) FROM counseling_requests WHERE status = 'approved'")->fetchColumn();
$totalAssessments = (int)$db->query("SELECT COUNT(*) FROM assessments")->fetchColumn();
$highRiskCount = (int)$db->query("SELECT COUNT(*) FROM assessments WHERE is_high_risk = 1")->fetchColumn();
$totalStudents = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();

// 2. Counseling Requests List
$reqStmt = $db->query("SELECT * FROM counseling_requests ORDER BY id DESC");
$counselingList = $reqStmt->fetchAll();

// 3. High Risk Assessments
$riskStmt = $db->query("SELECT * FROM assessments WHERE is_high_risk = 1 ORDER BY id DESC LIMIT 20");
$highRiskList = $riskStmt->fetchAll();

// 4. All Assessment Logs
$assessStmt = $db->query("SELECT * FROM assessments ORDER BY id DESC LIMIT 50");
$allAssessments = $assessStmt->fetchAll();

// 5. Registered Students
$studentsStmt = $db->query("SELECT id, full_name, student_id, email, intake, created_at FROM users WHERE role = 'student' ORDER BY id DESC");
$allStudents = $studentsStmt->fetchAll();

// 6. Severity Stats for Chart
$minimalCount = (int)$db->query("SELECT COUNT(*) FROM assessments WHERE severity_level LIKE '%Minimal%'")->fetchColumn();
$mildCount = (int)$db->query("SELECT COUNT(*) FROM assessments WHERE severity_level LIKE '%Mild%'")->fetchColumn();
$modCount = (int)$db->query("SELECT COUNT(*) FROM assessments WHERE severity_level LIKE '%Moderate%'")->fetchColumn();
$sevCount = (int)$db->query("SELECT COUNT(*) FROM assessments WHERE severity_level LIKE '%Severe%'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counselor Control Center | Sansun (සන්සුන්)</title>
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>/assets/images/logo.png?v=2">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Noto+Sans+Sinhala:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --sidebar-bg: #0f172a;
            --main-bg: #f8fafc;
            --card-bg: #ffffff;
            --primary: #1e4d2b;
            --primary-accent: #2d6a4f;
            --primary-light: #f0f7f4;
            --accent: #40916c;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
            --danger: #dc2626;
            --success: #16a34a;
            --warning: #d97706;
        }

        body.dark-mode {
            --main-bg: #0b0f19;
            --card-bg: #1e293b;
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --primary-light: #182820;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', sans-serif; }
        body { background: var(--main-bg); color: var(--text-dark); display: flex; height: 100vh; overflow: hidden; transition: background 0.3s; }

        /* Sidebar */
        .sidebar {
            width: 270px; background: var(--sidebar-bg); color: #fff; padding: 1.8rem 1.2rem;
            display: flex; flex-direction: column; justify-content: space-between; position: sticky;
            top: 0; height: 100vh; flex-shrink: 0; z-index: 100;
        }
        .brand {
            display: flex; align-items: center; gap: 12px; margin-bottom: 2rem; padding: 0 0.5rem; text-decoration: none; color: #fff;
        }
        .brand-logo-img {
            width: 42px; height: 42px; object-fit: contain;
            border-radius: 10px;
            filter: drop-shadow(0 0 10px rgba(82, 183, 136, 0.65));
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .brand:hover .brand-logo-img {
            transform: scale(1.08) rotate(2deg);
        }
        .brand-text { line-height: 1.2; }
        .brand-text strong { display: block; font-size: 1.15rem; color: #fff; letter-spacing: -0.3px; }
        .brand-text small { font-size: 0.75rem; color: #94a3b8; }

        .menu-label {
            font-size: 0.725rem; text-transform: uppercase; color: #64748b;
            font-weight: 700; margin: 1.2rem 0 0.6rem 0.6rem; letter-spacing: 0.8px;
        }
        .menu-nav { display: flex; flex-direction: column; gap: 4px; }
        .menu-item {
            display: flex; align-items: center; justify-content: space-between;
            color: #94a3b8; text-decoration: none; padding: 0.75rem 0.95rem; border-radius: 10px;
            font-weight: 600; font-size: 0.9rem; cursor: pointer; border: none; background: transparent; width: 100%; text-align: left;
            transition: all 0.2s;
        }
        .menu-item:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .menu-item.active { background: #1e4d2b; color: #fff; box-shadow: 0 4px 12px rgba(30, 77, 43, 0.4); }
        .menu-item-left { display: flex; align-items: center; gap: 12px; }

        .badge-count {
            padding: 2px 7px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;
        }
        .badge-pending { background: #f59e0b; color: #fff; }
        .badge-danger { background: #ef4444; color: #fff; }

        .sidebar-footer { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 1rem; display: flex; flex-direction: column; gap: 6px; }

        /* Main Content */
        .main-content { flex: 1; overflow-y: auto; padding: 2rem 2.5rem; }

        .top-bar {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; background: var(--card-bg);
            padding: 16px 24px; border-radius: 16px; border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
        }
        .top-bar-title h2 { font-size: 1.4rem; font-weight: 800; color: var(--text-dark); letter-spacing: -0.3px; }
        .top-bar-title p { color: var(--text-muted); font-size: 0.85rem; margin-top: 2px; }

        .controls-container { display: flex; align-items: center; gap: 10px; }
        .btn-toggle {
            background: var(--main-bg); color: var(--text-dark); border: 1px solid var(--border-color);
            padding: 8px 14px; border-radius: 10px; cursor: pointer; font-weight: 600; font-size: 0.85rem;
            display: flex; align-items: center; gap: 6px; text-decoration: none; transition: all 0.2s;
        }
        .btn-toggle:hover { border-color: var(--primary); color: var(--primary); }

        .admin-profile {
            display: flex; align-items: center; gap: 10px; background: var(--main-bg);
            padding: 6px 14px; border-radius: 30px; border: 1px solid var(--border-color);
        }
        .avatar {
            width: 32px; height: 32px; border-radius: 50%; background: var(--primary);
            color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem; margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--card-bg); padding: 1.4rem; border-radius: 16px;
            box-shadow: var(--card-shadow); border: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
        }
        .stat-info span { font-size: 0.825rem; color: var(--text-muted); font-weight: 600; }
        .stat-info h3 { font-size: 1.8rem; font-weight: 800; color: var(--text-dark); margin-top: 4px; }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
        }

        /* Tab Panels */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: tabFadeIn 0.2s ease; }
        @keyframes tabFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Charts Grid */
        .charts-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
        .chart-card {
            background: var(--card-bg); padding: 1.5rem; border-radius: 16px;
            box-shadow: var(--card-shadow); border: 1px solid var(--border-color);
        }

        /* Table Card */
        .table-card {
            background: var(--card-bg); border-radius: 16px; padding: 1.5rem;
            box-shadow: var(--card-shadow); border: 1px solid var(--border-color); margin-bottom: 2rem;
        }
        .table-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.2rem; flex-wrap: wrap; gap: 12px;
        }
        .table-header h3 { font-size: 1.15rem; font-weight: 800; color: var(--text-dark); }
        
        .search-input {
            padding: 8px 14px; border: 1px solid var(--border-color); border-radius: 10px;
            background: var(--main-bg); color: var(--text-dark); font-size: 0.85rem; outline: none; width: 220px;
        }
        .search-input:focus { border-color: var(--primary); }

        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; padding: 0.85rem 1rem; color: var(--text-muted);
            font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
            border-bottom: 1px solid var(--border-color);
        }
        td {
            padding: 0.95rem 1rem; border-bottom: 1px solid var(--border-color);
            font-size: 0.88rem; font-weight: 500; color: var(--text-dark);
        }

        .badge {
            padding: 4px 10px; border-radius: 20px; font-size: 0.75rem;
            font-weight: 700; display: inline-block; text-transform: capitalize;
        }
        .badge.pending { background: #fff7ed; color: #c2410c; }
        .badge.approved { background: #f0fdf4; color: #15803d; }
        .badge.rejected { background: #fef2f2; color: #b91c1c; }
        .badge.completed { background: #f1f5f9; color: #475569; }

        .btn-action {
            border: none; padding: 6px 12px; border-radius: 8px; cursor: pointer;
            font-weight: 700; font-size: 0.8rem; text-decoration: none; display: inline-flex;
            align-items: center; gap: 4px; transition: all 0.15s;
        }
        .btn-approve { background: #dcfce7; color: #15803d; }
        .btn-approve:hover { background: #bbf7d0; }
        .btn-reject { background: #fee2e2; color: #ef4444; }
        .btn-reject:hover { background: #fecaca; }
        .btn-complete { background: #e0f2fe; color: #0284c7; }
        .btn-complete:hover { background: #bae6fd; }

        @media (max-width: 992px) {
            .charts-grid { grid-template-columns: 1fr; }
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: static; }
            .main-content { padding: 1.2rem; }
        }
    </style>
</head>
<body>

<?php display_flash(); ?>

<!-- ==========================================================================
     1. COUNSELOR SIDEBAR
     ========================================================================== -->
<aside class="sidebar">
    <div>
        <a href="<?php echo SITE_URL; ?>/admin-dashboard.php" class="brand" title="Sansun Counselor Center">
            <img src="<?php echo SITE_URL; ?>/assets/images/logo.png?v=2" alt="Sansun Logo" class="brand-logo-img">
            <div class="brand-text">
                <strong>Sansun (සන්සුන්)</strong>
                <small>Counselor Center</small>
            </div>
        </a>

        <div class="menu-label">Navigation & Control</div>
        <nav class="menu-nav">
            <button class="menu-item active" onclick="showAdminTab('overview', this)">
                <div class="menu-item-left">
                    <i class="fa-solid fa-gauge"></i> <span>Overview</span>
                </div>
            </button>

            <button class="menu-item" onclick="showAdminTab('appointments', this)">
                <div class="menu-item-left">
                    <i class="fa-solid fa-calendar-check"></i> <span>Appointments</span>
                </div>
                <?php if ($pendingRequests > 0): ?>
                    <span class="badge-count badge-pending"><?php echo $pendingRequests; ?></span>
                <?php endif; ?>
            </button>

            <button class="menu-item" onclick="showAdminTab('alerts', this)">
                <div class="menu-item-left">
                    <i class="fa-solid fa-triangle-exclamation"></i> <span>High Risk Alerts</span>
                </div>
                <?php if ($highRiskCount > 0): ?>
                    <span class="badge-count badge-danger"><?php echo $highRiskCount; ?></span>
                <?php endif; ?>
            </button>

            <button class="menu-item" onclick="showAdminTab('assessments', this)">
                <div class="menu-item-left">
                    <i class="fa-solid fa-clipboard-list"></i> <span>Assessment Logs</span>
                </div>
                <span class="badge-count" style="background:rgba(255,255,255,0.1); color:#fff;"><?php echo $totalAssessments; ?></span>
            </button>

            <button class="menu-item" onclick="showAdminTab('students', this)">
                <div class="menu-item-left">
                    <i class="fa-solid fa-users"></i> <span>Student Directory</span>
                </div>
                <span class="badge-count" style="background:rgba(255,255,255,0.1); color:#fff;"><?php echo $totalStudents; ?></span>
            </button>
        </nav>
    </div>

    <div class="sidebar-footer">
        <!-- Preview Student App without redirect loops! -->
        <a href="<?php echo SITE_URL; ?>/index.php?preview=1" target="_blank" class="menu-item" style="color:#86efac;" title="Preview student portal interface in new tab">
            <div class="menu-item-left">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> <span>Preview Student App</span>
            </div>
        </a>

        <a href="<?php echo SITE_URL; ?>/logout.php" class="menu-item" style="color:#f87171;" title="Sign out of administrative session">
            <div class="menu-item-left">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Sign Out</span>
            </div>
        </a>
    </div>
</aside>

<!-- ==========================================================================
     2. MAIN CONTENT AREA
     ========================================================================== -->
<main class="main-content">
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-title">
            <h2>Counselor Control Center</h2>
            <p>Student psychological wellness check-in analytics & appointment management</p>
        </div>
        
        <div class="controls-container">
            <button class="btn-toggle" onclick="location.reload()" title="Refresh latest data">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh
            </button>
            <button class="btn-toggle" onclick="toggleAdminTheme()" title="Toggle Dark/Light Mode">
                <i class="fa-solid fa-moon"></i> Theme
            </button>
            <a href="<?php echo SITE_URL; ?>/index.php?preview=1" target="_blank" class="btn-toggle" style="color:var(--primary); font-weight:700;">
                <i class="fa-solid fa-eye"></i> Student View
            </a>
            <div class="admin-profile">
                <div class="avatar">C</div>
                <div>
                    <span style="font-weight:700; font-size:0.875rem;"><?php echo htmlspecialchars($adminUser['name']); ?></span>
                    <small style="display:block; font-size:0.75rem; color:var(--text-muted);">Lead Counselor</small>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Stats KPI Cards (Always visible at the top) -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <span>Total Counseling Requests</span>
                <h3><?php echo $totalRequests; ?></h3>
            </div>
            <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Pending Approvals</span>
                <h3><?php echo $pendingRequests; ?></h3>
            </div>
            <div class="stat-icon" style="background:#fef3c7; color:#d97706;">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Approved Appointments</span>
                <h3><?php echo $approvedRequests; ?></h3>
            </div>
            <div class="stat-icon" style="background:#dcfce7; color:#16a34a;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="stat-card" style="<?php echo $highRiskCount > 0 ? 'border: 2px solid #ef4444;' : ''; ?>">
            <div class="stat-info">
                <span style="<?php echo $highRiskCount > 0 ? 'color:#ef4444; font-weight:700;' : ''; ?>">High Risk Screenings</span>
                <h3 style="<?php echo $highRiskCount > 0 ? 'color:#ef4444;' : ''; ?>"><?php echo $highRiskCount; ?></h3>
            </div>
            <div class="stat-icon" style="background:#fee2e2; color:#ef4444;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         TAB 1: OVERVIEW & ANALYTICS
         ====================================================================== -->
    <div id="tab-overview" class="tab-panel active">
        <div class="charts-grid">
            <div class="chart-card">
                <h3 style="font-size:1.05rem; font-weight:700; margin-bottom:1rem;">
                    <i class="fa-solid fa-chart-pie" style="color:var(--primary); margin-right:6px;"></i>
                    Mental Health Severity Distribution (Student Screenings)
                </h3>
                <div style="height:260px; position:relative;">
                    <canvas id="severityChart"></canvas>
                </div>
            </div>

            <div class="chart-card" style="display:flex; flex-direction:column; justify-content:center;">
                <h3 style="font-size:1.05rem; font-weight:700; margin-bottom:1rem;">
                    <i class="fa-solid fa-address-book" style="color:var(--primary); margin-right:6px;"></i>
                    Institutional Demographics
                </h3>
                <div style="padding:14px; background:var(--main-bg); border-radius:12px; margin-bottom:12px; border:1px solid var(--border-color);">
                    <div style="font-size:0.8rem; color:var(--text-muted); font-weight:600;">Active Student Accounts</div>
                    <div style="font-size:1.6rem; font-weight:800; color:var(--primary);"><?php echo $totalStudents; ?></div>
                </div>
                <div style="padding:14px; background:var(--main-bg); border-radius:12px; border:1px solid var(--border-color);">
                    <div style="font-size:0.8rem; color:var(--text-muted); font-weight:600;">Completed PHQ-9 / GAD-7 Screenings</div>
                    <div style="font-size:1.6rem; font-weight:800; color:#0284c7;"><?php echo $totalAssessments; ?></div>
                </div>
            </div>
        </div>

        <!-- Quick Summary of Pending Appointments in Overview -->
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fa-solid fa-clock" style="color:#d97706; margin-right:6px;"></i> Action Required: Pending Appointment Requests</h3>
                <button class="btn-toggle" onclick="showAdminTab('appointments')">View All (<?php echo $totalRequests; ?>)</button>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Schedule Date</th>
                            <th>Format</th>
                            <th>Confidentiality</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $pendingList = array_filter($counselingList, fn($r) => $r['status'] === 'pending');
                        if (empty($pendingList)): 
                        ?>
                            <tr>
                                <td colspan="5" style="text-align:center; color:var(--text-muted); padding:24px;">No pending counseling requests at this time.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach (array_slice($pendingList, 0, 5) as $req): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($req['student_name']); ?></strong>
                                        <small style="display:block; color:var(--text-muted);"><?php echo htmlspecialchars($req['student_id'] ?: 'Anonymous'); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y - H:i', strtotime($req['preferred_date'])); ?></td>
                                    <td><?php echo ucfirst($req['preferred_mode']); ?></td>
                                    <td>
                                        <span class="badge" style="background:<?php echo $req['request_type'] === 'anonymous' ? '#f3e8ff' : '#e0f2fe'; ?>; color:<?php echo $req['request_type'] === 'anonymous' ? '#7e22ce' : '#0369a1'; ?>;">
                                            <?php echo ucfirst($req['request_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo SITE_URL; ?>/admin-dashboard.php?action=update&req_id=<?php echo $req['id']; ?>&status=approved" class="btn-action btn-approve" title="Approve Appointment">
                                            <i class="fa-solid fa-check"></i> Approve
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/admin-dashboard.php?action=update&req_id=<?php echo $req['id']; ?>&status=rejected" class="btn-action btn-reject" title="Reject Request">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         TAB 2: COUNSELING APPOINTMENTS MANAGEMENT
         ====================================================================== -->
    <div id="tab-appointments" class="tab-panel">
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fa-solid fa-calendar-check" style="color:var(--primary); margin-right:6px;"></i> All Student Counseling Appointments</h3>
                <input type="text" class="search-input" placeholder="Search appointments..." onkeyup="filterTable('appointmentsTable', this.value)">
            </div>

            <div style="overflow-x:auto;">
                <table id="appointmentsTable">
                    <thead>
                        <tr>
                            <th>Student Name / ID</th>
                            <th>Scheduled Date</th>
                            <th>Mode</th>
                            <th>Privacy</th>
                            <th>Notes / Concern</th>
                            <th>Status</th>
                            <th>Manage Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($counselingList)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; color:var(--text-muted); padding:30px;">No counseling requests recorded in system.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($counselingList as $req): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($req['student_name']); ?></strong>
                                        <small style="display:block; color:var(--text-muted);"><?php echo htmlspecialchars($req['student_id'] ?: 'Anonymous'); ?></small>
                                    </td>
                                    <td><?php echo date('M d, Y - H:i', strtotime($req['preferred_date'])); ?></td>
                                    <td><?php echo ucfirst($req['preferred_mode']); ?></td>
                                    <td>
                                        <span class="badge" style="background:<?php echo $req['request_type'] === 'anonymous' ? '#f3e8ff' : '#e0f2fe'; ?>; color:<?php echo $req['request_type'] === 'anonymous' ? '#7e22ce' : '#0369a1'; ?>;">
                                            <?php echo ucfirst($req['request_type']); ?>
                                        </span>
                                    </td>
                                    <td style="max-width:260px; font-size:0.85rem; color:var(--text-muted);">
                                        <?php echo htmlspecialchars($req['notes'] ?: 'No notes provided'); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo htmlspecialchars($req['status']); ?>">
                                            <?php echo htmlspecialchars($req['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($req['status'] === 'pending'): ?>
                                            <a href="<?php echo SITE_URL; ?>/admin-dashboard.php?action=update&req_id=<?php echo $req['id']; ?>&status=approved" class="btn-action btn-approve" title="Approve Appointment">
                                                <i class="fa-solid fa-check"></i> Approve
                                            </a>
                                            <a href="<?php echo SITE_URL; ?>/admin-dashboard.php?action=update&req_id=<?php echo $req['id']; ?>&status=rejected" class="btn-action btn-reject" title="Reject Request">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                        <?php elseif ($req['status'] === 'approved'): ?>
                                            <a href="<?php echo SITE_URL; ?>/admin-dashboard.php?action=update&req_id=<?php echo $req['id']; ?>&status=completed" class="btn-action btn-complete" title="Mark Session Completed">
                                                <i class="fa-solid fa-circle-check"></i> Complete
                                            </a>
                                        <?php else: ?>
                                            <small style="color:var(--text-muted);">Closed</small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         TAB 3: HIGH RISK ALERTS
         ====================================================================== -->
    <div id="tab-alerts" class="tab-panel">
        <div class="table-card" style="border: 2px solid #ef4444;">
            <div class="table-header">
                <h3 style="color:#b91c1c;">
                    <i class="fa-solid fa-triangle-exclamation"></i> High Risk Alerts - Proactive Intervention Recommended
                </h3>
                <input type="text" class="search-input" placeholder="Search alerts..." onkeyup="filterTable('alertsTable', this.value)">
            </div>
            
            <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:16px;">
                These screening records triggered clinical alerts due to high severity scores or self-harm indications. Counselors should prioritize proactive outreach.
            </p>

            <div style="overflow-x:auto;">
                <table id="alertsTable">
                    <thead>
                        <tr>
                            <th>Screening Date</th>
                            <th>Student</th>
                            <th>Test Type</th>
                            <th>Score</th>
                            <th>Severity Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($highRiskList)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; color:var(--text-muted); padding:30px;">✓ No high-risk screening alerts currently flagged.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($highRiskList as $risk): ?>
                                <tr style="background:#fff5f5;">
                                    <td><?php echo date('M d, Y - H:i', strtotime($risk['created_at'])); ?></td>
                                    <td><strong><?php echo htmlspecialchars($risk['student_name'] ?: 'Student User'); ?></strong></td>
                                    <td><span class="badge" style="background:#f1f5f9;"><?php echo strtoupper($risk['test_type']); ?></span></td>
                                    <td><strong style="color:#b91c1c; font-size:1.15rem;"><?php echo $risk['total_score']; ?></strong></td>
                                    <td><span class="badge" style="background:#fee2e2; color:#b91c1c; font-weight:700;"><?php echo htmlspecialchars($risk['severity_level']); ?></span></td>
                                    <td>
                                        <a href="mailto:student@dit.ac.lk?subject=Follow-up%20Support%20Session" class="btn-action btn-complete">
                                            <i class="fa-solid fa-envelope"></i> Reach Out
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         TAB 4: ASSESSMENT LOGS
         ====================================================================== -->
    <div id="tab-assessments" class="tab-panel">
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fa-solid fa-clipboard-list" style="color:var(--primary); margin-right:6px;"></i> All Student Assessment Logs (PHQ-9 & GAD-7)</h3>
                <input type="text" class="search-input" placeholder="Search assessments..." onkeyup="filterTable('assessmentsTable', this.value)">
            </div>

            <div style="overflow-x:auto;">
                <table id="assessmentsTable">
                    <thead>
                        <tr>
                            <th>Date / Time</th>
                            <th>Student Name</th>
                            <th>Scale</th>
                            <th>Score</th>
                            <th>Severity Classification</th>
                            <th>Risk Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allAssessments)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center; color:var(--text-muted); padding:30px;">No assessment check-ins recorded yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allAssessments as $item): ?>
                                <tr>
                                    <td><?php echo date('M d, Y - H:i', strtotime($item['created_at'])); ?></td>
                                    <td><strong><?php echo htmlspecialchars($item['student_name']); ?></strong></td>
                                    <td><span class="badge" style="background:var(--primary-light); color:var(--primary); font-weight:700;"><?php echo strtoupper($item['test_type']); ?></span></td>
                                    <td><strong><?php echo $item['total_score']; ?></strong></td>
                                    <td><?php echo htmlspecialchars($item['severity_level']); ?></td>
                                    <td>
                                        <?php if ($item['is_high_risk']): ?>
                                            <span class="badge" style="background:#fee2e2; color:#b91c1c; font-weight:700;">High Risk</span>
                                        <?php else: ?>
                                            <span class="badge" style="background:#dcfce7; color:#15803d;">Standard</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================================================================
         TAB 5: STUDENT DIRECTORY
         ====================================================================== -->
    <div id="tab-students" class="tab-panel">
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fa-solid fa-users" style="color:var(--primary); margin-right:6px;"></i> Registered Student Directory</h3>
                <input type="text" class="search-input" placeholder="Search students..." onkeyup="filterTable('studentsTable', this.value)">
            </div>

            <div style="overflow-x:auto;">
                <table id="studentsTable">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Registration ID</th>
                            <th>Institutional Email</th>
                            <th>Intake / Batch</th>
                            <th>Registered Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allStudents)): ?>
                            <tr>
                                <td colspan="5" style="text-align:center; color:var(--text-muted); padding:30px;">No students registered yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($allStudents as $st): ?>
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div class="avatar" style="width:28px; height:28px; font-size:0.75rem;">
                                                <?php echo strtoupper(substr($st['full_name'], 0, 1)); ?>
                                            </div>
                                            <strong><?php echo htmlspecialchars($st['full_name']); ?></strong>
                                        </div>
                                    </td>
                                    <td><code><?php echo htmlspecialchars($st['student_id'] ?: '-'); ?></code></td>
                                    <td><?php echo htmlspecialchars($st['email']); ?></td>
                                    <td><?php echo htmlspecialchars($st['intake'] ?: 'DIT 14 Intake'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($st['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    // Tab Switching Logic
    function showAdminTab(tabName, btnElement) {
        // Hide all tab panels
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        // Deactivate all sidebar items
        document.querySelectorAll('.menu-nav .menu-item').forEach(m => m.classList.remove('active'));

        const targetPanel = document.getElementById('tab-' + tabName);
        if (targetPanel) {
            targetPanel.classList.add('active');
        }

        if (btnElement) {
            btnElement.classList.add('active');
        } else {
            // Match button by onclick attribute if called programmatically
            document.querySelectorAll('.menu-nav .menu-item').forEach(b => {
                if (b.getAttribute('onclick') && b.getAttribute('onclick').includes(tabName)) {
                    b.classList.add('active');
                }
            });
        }

        // Update URL hash
        window.location.hash = tabName;
    }

    // Filter table rows live
    function filterTable(tableId, query) {
        const q = query.toLowerCase();
        const table = document.getElementById(tableId);
        if (!table) return;

        const rows = table.getElementsByTagName('tr');
        for (let i = 1; i < rows.length; i++) {
            const rowText = rows[i].innerText.toLowerCase();
            rows[i].style.display = rowText.includes(q) ? '' : 'none';
        }
    }

    // Toggle Theme
    function toggleAdminTheme() {
        document.body.classList.toggle('dark-mode');
        localStorage.setItem('sansun_admin_theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
    }

    // Init state
    document.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('sansun_admin_theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }

        // Check hash
        const hash = window.location.hash.replace('#', '');
        if (['overview', 'appointments', 'alerts', 'assessments', 'students'].includes(hash)) {
            showAdminTab(hash);
        }

        // Initialize Severity Chart
        const ctx = document.getElementById('severityChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Minimal (0-4)', 'Mild (5-9)', 'Moderate (10-14)', 'Severe (15+)'],
                datasets: [{
                    data: [
                        <?php echo max(1, $minimalCount); ?>,
                        <?php echo max(1, $mildCount); ?>,
                        <?php echo max(1, $modCount); ?>,
                        <?php echo max(1, $sevCount); ?>
                    ],
                    backgroundColor: ['#1e4d2b', '#0284c7', '#f59e0b', '#dc2626'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });
    });
</script>
</body>
</html>

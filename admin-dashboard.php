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
        set_flash('success', "Counseling appointment status changed to '{$newStatus}'.");
    }
    redirect(SITE_URL . '/admin-dashboard.php');
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
$riskStmt = $db->query("SELECT * FROM assessments WHERE is_high_risk = 1 ORDER BY id DESC LIMIT 10");
$highRiskList = $riskStmt->fetchAll();

// 4. Severity Stats for Chart
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
    <title>Admin & Counselor Dashboard - Sansun</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Noto+Sans+Sinhala:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --sidebar-bg: #0f172a;
            --main-bg: #f8fafc;
            --card-bg: #ffffff;
            --primary: #4a7c59;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #f1f5f9;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        }

        body.dark-mode {
            --main-bg: #0b0f19;
            --card-bg: #1e293b;
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', sans-serif; transition: background-color 0.3s, color 0.3s; }
        body { background-color: var(--main-bg); color: var(--text-dark); display: flex; min-height: 100vh; }

        .sidebar { width: 260px; background: var(--sidebar-bg); color: #fff; padding: 2rem 1.2rem; display: flex; flex-direction: column; justify-content: space-between; position: sticky; top:0; height: 100vh; }
        .brand { font-size: 1.3rem; font-weight: 700; color: #48bb78; display: flex; align-items: center; gap: 10px; margin-bottom: 2.5rem; padding-left: 0.5rem; }
        .menu-label { font-size: 0.75rem; text-transform: uppercase; color: #475569; font-weight: 700; margin-bottom: 0.8rem; padding-left: 0.5rem; letter-spacing: 0.5px; }
        .menu-item { display: flex; align-items: center; gap: 12px; color: #94a3b8; text-decoration: none; padding: 0.85rem 1rem; border-radius: 10px; margin-bottom: 0.4rem; font-weight: 500; cursor: pointer; }
        .menu-item:hover, .menu-item.active { background: var(--primary); color: #ffffff; }

        .main-content { flex: 1; overflow-y: auto; padding: 2.5rem; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        
        .controls-container { display: flex; align-items: center; gap: 12px; }
        .btn-toggle { background: var(--card-bg); color: var(--text-dark); border: 1px solid var(--border-color); padding: 8px 14px; border-radius: 20px; cursor: pointer; font-weight: 600; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; box-shadow: var(--card-shadow); text-decoration: none; }

        .admin-profile { display: flex; align-items: center; gap: 12px; background: var(--card-bg); padding: 6px 16px; border-radius: 30px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); }
        .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: var(--card-bg); padding: 1.5rem; border-radius: 16px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; }
        .stat-info span { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; }
        .stat-info h3 { font-size: 1.8rem; font-weight: 700; color: var(--text-dark); margin-top: 4px; }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; }

        .charts-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
        .chart-card { background: var(--card-bg); padding: 1.5rem; border-radius: 16px; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); }

        .table-card { background: var(--card-bg); border-radius: 16px; padding: 1.8rem; box-shadow: var(--card-shadow); border: 1px solid var(--border-color); margin-bottom: 2rem; }
        .table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem; }
        .table-header h3 { font-size: 1.1rem; font-weight: 700; color: var(--text-dark); }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 1rem; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid var(--border-color); }
        td { padding: 1.1rem 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; font-weight: 500; color: var(--text-dark); }

        .badge { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; text-transform: capitalize; }
        .badge.pending { background: #fff7ed; color: #c2410c; }
        .badge.approved { background: #f0fdf4; color: #15803d; }
        .badge.rejected { background: #fef2f2; color: #b91c1c; }
        .badge.completed { background: #f1f5f9; color: #475569; }

        .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.8rem; margin-right: 4px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
        .btn-approve { background: #dcfce7; color: #15803d; }
        .btn-reject { background: #fee2e2; color: #ef4444; }
        .btn-complete { background: #e0f2fe; color: #0284c7; }

        @media (max-width: 992px) { .charts-grid { grid-template-columns: 1fr; } }
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar { width: 100%; height: auto; position: static; }
            .main-content { padding: 1rem; }
            table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>

<?php display_flash(); ?>

<aside class="sidebar">
    <div>
        <div class="brand"><i class="fa-solid fa-leaf"></i> Sansun Admin</div>
        <div class="menu-label">Main Menu</div>
        <a class="menu-item active"><i class="fa-solid fa-gauge"></i> Control Dashboard</a>
        <a href="<?php echo SITE_URL; ?>/index.php" class="menu-item" target="_blank"><i class="fa-solid fa-globe"></i> Public Web App</a>
    </div>
    <a href="<?php echo SITE_URL; ?>/logout.php" class="menu-item" style="color: #ef4444;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</aside>

<main class="main-content">
    <div class="top-bar">
        <div>
            <h2 style="font-weight: 700;">Counselor Control Panel</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Student mental wellness check-in analytics & appointment management</p>
        </div>
        
        <div class="controls-container">
            <button class="btn-toggle" onclick="toggleTheme()"><i class="fa-solid fa-moon"></i> Theme</button>
            <div class="admin-profile">
                <div class="avatar">C</div>
                <span style="font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($adminUser['name']); ?></span>
            </div>
        </div>
    </div>

    <!-- 1. Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <span>Total Counseling Requests</span>
                <h3><?php echo $totalRequests; ?></h3>
            </div>
            <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;"><i class="fa-solid fa-calendar"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Pending Requests</span>
                <h3><?php echo $pendingRequests; ?></h3>
            </div>
            <div class="stat-icon" style="background: #fef3c7; color: #d97706;"><i class="fa-solid fa-clock"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <span>Approved Sessions</span>
                <h3><?php echo $approvedRequests; ?></h3>
            </div>
            <div class="stat-icon" style="background: #dcfce7; color: #16a34a;"><i class="fa-solid fa-circle-check"></i></div>
        </div>

        <div class="stat-card" style="<?php echo $highRiskCount > 0 ? 'border-color:#ef4444;' : ''; ?>">
            <div class="stat-info">
                <span>High Risk Screenings</span>
                <h3 style="color:#ef4444;"><?php echo $highRiskCount; ?></h3>
            </div>
            <div class="stat-icon" style="background: #fee2e2; color: #ef4444;"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
    </div>

    <!-- 2. Charts Grid -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3 style="font-size:1.05rem; font-weight:700; margin-bottom:1rem;">Mental Health Severity Distribution (Student Screenings)</h3>
            <canvas id="severityChart" style="max-height:280px;"></canvas>
        </div>

        <div class="chart-card" style="display:flex; flex-direction:column; justify-content:center;">
            <h3 style="font-size:1.05rem; font-weight:700; margin-bottom:1rem;">Registered Demographics</h3>
            <div style="padding:14px; background:var(--main-bg); border-radius:12px; margin-bottom:12px;">
                <div style="font-size:0.8rem; color:var(--text-muted);">Active Student Accounts</div>
                <div style="font-size:1.6rem; font-weight:700; color:var(--primary);"><?php echo $totalStudents; ?></div>
            </div>
            <div style="padding:14px; background:var(--main-bg); border-radius:12px;">
                <div style="font-size:0.8rem; color:var(--text-muted);">Total Self-Check Assessments</div>
                <div style="font-size:1.6rem; font-weight:700; color:#5b82a6;"><?php echo $totalAssessments; ?></div>
            </div>
        </div>
    </div>

    <!-- 3. High Risk Student Alerts Banner / Table -->
    <?php if (!empty($highRiskList)): ?>
        <div class="table-card" style="border: 2px solid #ef4444;">
            <div class="table-header">
                <h3 style="color:#b91c1c;"><i class="fa-solid fa-triangle-exclamation"></i> High Risk Alerts - Proactive Counselor Intervention Needed</h3>
            </div>
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Student Name</th>
                            <th>Assessment</th>
                            <th>Score</th>
                            <th>Severity Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($highRiskList as $risk): ?>
                            <tr style="background:#fff5f5;">
                                <td><?php echo date('M d, Y - H:i', strtotime($risk['created_at'])); ?></td>
                                <td><strong><?php echo htmlspecialchars($risk['student_name'] ?: 'Student User'); ?></strong></td>
                                <td><span class="badge" style="background:#f1f5f9;"><?php echo strtoupper($risk['test_type']); ?></span></td>
                                <td><strong style="color:#b91c1c; font-size:1.1rem;"><?php echo $risk['total_score']; ?></strong></td>
                                <td><span class="badge" style="background:#fee2e2; color:#b91c1c; font-weight:700;"><?php echo htmlspecialchars($risk['severity_level']); ?></span></td>
                                <td>
                                    <a href="mailto:student@dit.ac.lk?subject=Follow-up%20Support%20Session" class="btn-action btn-complete">
                                        <i class="fa-solid fa-envelope"></i> Reach Out
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- 4. Counseling Requests Management Table -->
    <div class="table-card">
        <div class="table-header">
            <h3>Student Counseling Requests</h3>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Student Name / ID</th>
                        <th>Preferred Schedule</th>
                        <th>Session Mode</th>
                        <th>Privacy</th>
                        <th>Notes / Reason</th>
                        <th>Status</th>
                        <th>Manage Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($counselingList)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; color:var(--text-muted); padding:30px;">No counseling requests submitted yet.</td>
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
                                <td style="max-width:280px; font-size:0.85rem; color:var(--text-muted);">
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
                                        <a href="<?php echo SITE_URL; ?>/admin-dashboard.php?action=update&req_id=<?php echo $req['id']; ?>&status=completed" class="btn-action btn-complete" title="Mark Completed">
                                            <i class="fa-solid fa-circle-check"></i> Complete
                                        </a>
                                    <?php else: ?>
                                        <small style="color:var(--text-muted);">-</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    function toggleTheme() {
        document.body.classList.toggle('dark-mode');
    }

    document.addEventListener('DOMContentLoaded', () => {
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
                    backgroundColor: ['#4a7c59', '#5b82a6', '#f59e0b', '#dc2626'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
</body>
</html>

<?php
require_once __DIR__ . '/config/db.php';
require_login();

$user = current_user();
$userId = $user['id'];
$db = get_db();

// Fetch student profile details
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userData = $stmt->fetch();

// Fetch user assessments
$assessStmt = $db->prepare("SELECT * FROM assessments WHERE user_id = ? ORDER BY id DESC");
$assessStmt->execute([$userId]);
$assessments = $assessStmt->fetchAll();

// Fetch counseling requests
$counselStmt = $db->prepare("SELECT * FROM counseling_requests WHERE user_id = ? ORDER BY id DESC");
$counselStmt->execute([$userId]);
$counselingRequests = $counselStmt->fetchAll();

// Fetch recent moods
$moodStmt = $db->prepare("SELECT * FROM mood_logs WHERE user_id = ? ORDER BY id DESC LIMIT 5");
$moodStmt->execute([$userId]);
$userMoods = $moodStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Wellness Dashboard | Sansun</title>
    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Noto+Sans+Sinhala:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --main-bg: #f8fafc;
            --card-bg: #ffffff;
            --primary: #4a7c59;
            --primary-light: #eef4f0;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        [data-theme="dark"] {
            --main-bg: #0f172a;
            --card-bg: #1e293b;
            --primary: #529465;
            --primary-light: #1b2e24;
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', 'Noto Sans Sinhala', sans-serif; transition: background-color 0.3s, color 0.3s; }
        body { background-color: var(--main-bg); color: var(--text-dark); }

        .navbar { 
            background: var(--card-bg); padding: 1.1rem 2.5rem; 
            display: flex; justify-content: space-between; align-items: center; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.03); border-bottom: 1px solid var(--border-color);
        }
        .logo { font-size: 1.3rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .user-nav-profile { display: flex; align-items: center; gap: 14px; }
        .avatar { width: 38px; height: 38px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; }

        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1.5rem; }
        
        .welcome-card { 
            background: linear-gradient(135deg, #4a7c59, #3b6346); 
            color: white; border-radius: 20px; padding: 2rem 2.5rem; 
            margin-bottom: 2rem; box-shadow: 0 15px 30px rgba(74, 124, 89, 0.2); 
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;
        }
        .welcome-card h2 { font-size: 1.7rem; font-weight: 700; }
        .welcome-card p { opacity: 0.92; margin-top: 4px; font-size: 0.95rem; }

        .dashboard-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 2rem; }

        .card { 
            background: var(--card-bg); border-radius: 18px; padding: 1.8rem; 
            box-shadow: var(--card-shadow); margin-bottom: 2rem; border: 1px solid var(--border-color);
        }
        .card-header { 
            font-size: 1.1rem; font-weight: 700; color: var(--text-dark); 
            margin-bottom: 1.2rem; display: flex; align-items: center; justify-content: space-between; 
            border-bottom: 1px solid var(--border-color); padding-bottom: 0.8rem; 
        }

        .info-group { margin-bottom: 1.2rem; }
        .info-label { font-size: 0.78rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-val { font-weight: 600; font-size: 0.95rem; color: var(--text-dark); margin-top: 2px; }

        .btn-action { 
            background: var(--primary); color: white; border: none; padding: 10px 18px; 
            border-radius: 10px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.88rem;
        }
        .btn-pdf { background: #ffffff; color: var(--primary); font-weight: 700; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 12px; color: var(--text-muted); font-size: 0.8rem; font-weight: 700; text-transform: uppercase; border-bottom: 1px solid var(--border-color); }
        td { padding: 12px; border-bottom: 1px solid var(--border-color); font-size: 0.88rem; }

        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; display: inline-block; text-transform: capitalize; }
        .badge-pending { background: #fff7ed; color: #c2410c; }
        .badge-approved { background: #f0fdf4; color: #15803d; }
        .badge-completed { background: #f1f5f9; color: #475569; }
        .badge-rejected { background: #fef2f2; color: #b91c1c; }
        .badge-high-risk { background: #fee2e2; color: #b91c1c; font-weight: 700; }

        @media (max-width: 900px) { .dashboard-grid { grid-template-columns: 1fr; } }

        @media print {
            .navbar, .btn-no-print, .welcome-card button { display: none !important; }
            .dashboard-grid { grid-template-columns: 1fr; }
            body { background: #fff; }
        }
    </style>
</head>
<body>

<?php display_flash(); ?>

<!-- Top Navigation -->
<nav class="navbar">
    <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
        <i class="fa-solid fa-leaf"></i> Sansun Wellness
    </a>

    <div class="user-nav-profile">
        <button onclick="toggleTheme()" class="btn-action" style="background:var(--primary-light); color:var(--primary); padding:6px 12px; font-size:0.8rem;" aria-label="Theme">
            <i class="fa-solid fa-moon"></i>
        </button>
        <a href="<?php echo SITE_URL; ?>/index.php" class="btn-action" style="background:var(--primary-light); color:var(--primary); padding:6px 12px; font-size:0.8rem;">
            <i class="fa-solid fa-house"></i> Home
        </a>
        <div class="avatar"><?php echo strtoupper(substr($userData['full_name'], 0, 1)); ?></div>
        <div style="line-height: 1.2;">
            <div style="font-weight: 700; font-size: 0.9rem;"><?php echo htmlspecialchars($userData['full_name']); ?></div>
            <small style="color: var(--text-muted); font-size: 0.78rem;"><?php echo htmlspecialchars($userData['student_id'] ?: 'Student'); ?></small>
        </div>
        <a href="<?php echo SITE_URL; ?>/logout.php" class="btn-action" style="background:#fee2e2; color:#b91c1c; padding:6px 12px; font-size:0.8rem;" title="Logout">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</nav>

<div class="container">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <div>
            <h2>ආයුබෝවන්, <?php echo htmlspecialchars($userData['full_name']); ?>! 👋</h2>
            <p>Student Mental Wellness Portal &bull; <?php echo htmlspecialchars($userData['student_id']); ?> (<?php echo htmlspecialchars($userData['intake']); ?>)</p>
        </div>
        <div class="btn-no-print">
            <button onclick="window.print()" class="btn-action btn-pdf">
                <i class="fa-solid fa-print"></i> Print Wellness Report
            </button>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Left Column -->
        <div>
            <!-- Profile Info -->
            <div class="card">
                <div class="card-header">
                    <span><i class="fa-solid fa-id-card"></i> Student Profile</span>
                </div>
                <div class="info-group">
                    <div class="info-label">Full Name</div>
                    <div class="info-val"><?php echo htmlspecialchars($userData['full_name']); ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Student Registration No</div>
                    <div class="info-val"><?php echo htmlspecialchars($userData['student_id']); ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Email Address</div>
                    <div class="info-val"><?php echo htmlspecialchars($userData['email']); ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Batch / Course</div>
                    <div class="info-val"><?php echo htmlspecialchars($userData['intake']); ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label">Member Since</div>
                    <div class="info-val"><?php echo date('M d, Y', strtotime($userData['created_at'])); ?></div>
                </div>
            </div>

            <!-- Quick Mood Check-In -->
            <div class="card btn-no-print">
                <div class="card-header">
                    <span><i class="fa-solid fa-heart-pulse"></i> Quick State Check-In</span>
                </div>
                <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:14px;">How is your energy and headspace right now?</p>
                <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:8px; margin-bottom:16px;">
                    <button type="button" onclick="quickMood('thriving', 'fa-sun', 'Thriving')" title="Thriving" style="padding:10px 4px; background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; border-radius:12px; cursor:pointer; font-size:0.8rem; font-weight:700; display:flex; flex-direction:column; align-items:center; gap:4px;">
                        <i class="fa-solid fa-sun" style="font-size:1.15rem;"></i> Thriving
                    </button>
                    <button type="button" onclick="quickMood('balanced', 'fa-seedling', 'Balanced')" title="Balanced" style="padding:10px 4px; background:#ccfbf1; color:#0f766e; border:1px solid #99f6e4; border-radius:12px; cursor:pointer; font-size:0.8rem; font-weight:700; display:flex; flex-direction:column; align-items:center; gap:4px;">
                        <i class="fa-solid fa-seedling" style="font-size:1.15rem;"></i> Balanced
                    </button>
                    <button type="button" onclick="quickMood('fatigued', 'fa-cloud-rain', 'Fatigued')" title="Fatigued" style="padding:10px 4px; background:#fef3c7; color:#b45309; border:1px solid #fde68a; border-radius:12px; cursor:pointer; font-size:0.8rem; font-weight:700; display:flex; flex-direction:column; align-items:center; gap:4px;">
                        <i class="fa-solid fa-cloud-rain" style="font-size:1.15rem;"></i> Fatigued
                    </button>
                    <button type="button" onclick="quickMood('distressed', 'fa-bolt', 'Distressed')" title="Distressed" style="padding:10px 4px; background:#fee2e2; color:#b91c1c; border:1px solid #fecaca; border-radius:12px; cursor:pointer; font-size:0.8rem; font-weight:700; display:flex; flex-direction:column; align-items:center; gap:4px;">
                        <i class="fa-solid fa-bolt" style="font-size:1.15rem;"></i> Distressed
                    </button>
                </div>
                <div style="margin-top:16px;">
                    <div class="info-label" style="margin-bottom:6px;">Recent State Logs</div>
                    <ul style="list-style:none; font-size:0.85rem;">
                        <?php if (empty($userMoods)): ?>
                            <li style="color:var(--text-muted); font-size:0.82rem;">No logs recorded yet.</li>
                        <?php else: ?>
                            <?php foreach ($userMoods as $m): ?>
                                <li style="padding:6px 0; border-bottom:1px dashed var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                                    <span>
                                        <i class="fa-solid <?php echo htmlspecialchars($m['mood_icon'] ?? 'fa-circle-check'); ?>" style="color:var(--primary); margin-right:6px;"></i>
                                        <strong><?php echo htmlspecialchars($m['mood_label']); ?></strong>
                                    </span>
                                    <small style="color:var(--text-muted);"><?php echo date('M d, H:i', strtotime($m['created_at'])); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Emergency Support -->
            <div class="card" style="background:var(--primary-light); border-color:var(--primary);">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                    <i class="fa-solid fa-phone-volume" style="color:var(--primary); font-size:1.2rem;"></i>
                    <strong style="color:var(--primary);">24/7 Helpline Access</strong>
                </div>
                <p style="font-size:0.85rem; color:var(--text-dark); margin-bottom:10px;">
                    If you are experiencing academic distress, call <strong>1926</strong> (NIMH) or <strong>1333</strong> confidentially anytime.
                </p>
                <a href="tel:1926" class="btn-action" style="background:#dc2626; width:100%; justify-content:center;">
                    <i class="fa-solid fa-phone"></i> Call 1926 Free
                </a>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Stress & Anxiety Chart -->
            <div class="card">
                <div class="card-header">
                    <span><i class="fa-solid fa-chart-line"></i> Assessment Scores Trend</span>
                    <a href="<?php echo SITE_URL; ?>/index.php#assessment" class="btn-action btn-no-print" style="padding:4px 10px; font-size:0.75rem;">
                        <i class="fa-solid fa-plus"></i> New Check-in
                    </a>
                </div>
                <div>
                    <canvas id="studentScoreChart" style="max-height: 240px;"></canvas>
                </div>
            </div>

            <!-- Past Assessment Records Table -->
            <div class="card">
                <div class="card-header">
                    <span><i class="fa-solid fa-clipboard-check"></i> Past Assessment Records</span>
                </div>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Test</th>
                                <th>Score</th>
                                <th>Severity Level</th>
                                <th>Counselor Alert</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($assessments)): ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; color:var(--text-muted); padding:20px;">No assessment check-ins recorded yet. Take your first test today!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($assessments as $a): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($a['created_at'])); ?></td>
                                        <td><strong><?php echo strtoupper($a['test_type']); ?></strong></td>
                                        <td><strong><?php echo $a['total_score']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($a['severity_level']); ?></td>
                                        <td>
                                            <?php if ($a['is_high_risk']): ?>
                                                <span class="badge badge-high-risk"><i class="fa-solid fa-triangle-exclamation"></i> High Risk</span>
                                            <?php else: ?>
                                                <span class="badge" style="background:#dcfce7; color:#15803d;">Normal</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Counseling Appointments Table -->
            <div class="card">
                <div class="card-header">
                    <span><i class="fa-solid fa-calendar-check"></i> My Counseling Sessions</span>
                    <a href="<?php echo SITE_URL; ?>/index.php#counseling" class="btn-action btn-no-print" style="padding:4px 10px; font-size:0.75rem;">
                        <i class="fa-solid fa-calendar-plus"></i> Request Session
                    </a>
                </div>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Requested Date</th>
                                <th>Mode</th>
                                <th>Privacy</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($counselingRequests)): ?>
                                <tr>
                                    <td colspan="4" style="text-align:center; color:var(--text-muted); padding:20px;">No counseling sessions booked yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($counselingRequests as $c): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y - H:i', strtotime($c['preferred_date'])); ?></td>
                                        <td><?php echo ucfirst($c['preferred_mode']); ?></td>
                                        <td><?php echo ucfirst($c['request_type']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo htmlspecialchars($c['status']); ?>">
                                                <?php echo htmlspecialchars($c['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const SITE_ROOT = "<?php echo SITE_URL; ?>";

    function toggleTheme() {
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        document.body.setAttribute('data-theme', isDark ? '' : 'dark');
        localStorage.setItem('sansun_theme', isDark ? 'light' : 'dark');
    }

    async function quickMood(code, icon, label) {
        try {
            const res = await fetch(SITE_ROOT + '/api/mood.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    mood_code: code, 
                    mood_icon: icon, 
                    mood_label: label, 
                    note: 'Logged from Student Dashboard' 
                })
            });
            const data = await res.json();
            if (data.success) {
                alert("State [" + label + "] recorded successfully!");
                window.location.reload();
            }
        } catch (e) {
            alert("Error: " + e.message);
    }

    // Chart.js initialization
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('studentScoreChart');
        if (!ctx) return;

        <?php
        $chartLabels = [];
        $phqScores = [];
        $gadScores = [];
        foreach (array_reverse($assessments) as $item) {
            $chartLabels[] = date('M d', strtotime($item['created_at']));
            if ($item['test_type'] === 'phq9') {
                $phqScores[] = $item['total_score'];
                $gadScores[] = null;
            } else {
                $phqScores[] = null;
                $gadScores[] = $item['total_score'];
            }
        }
        ?>

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_slice($chartLabels, -8)); ?>,
                datasets: [
                    {
                        label: 'PHQ-9 (Depression)',
                        data: <?php echo json_encode(array_slice($phqScores, -8)); ?>,
                        borderColor: '#4a7c59',
                        backgroundColor: 'rgba(74, 124, 89, 0.1)',
                        spanGaps: true,
                        tension: 0.3
                    },
                    {
                        label: 'GAD-7 (Anxiety)',
                        data: <?php echo json_encode(array_slice($gadScores, -8)); ?>,
                        borderColor: '#5b82a6',
                        backgroundColor: 'rgba(91, 130, 166, 0.1)',
                        spanGaps: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 27 }
                }
            }
        });
    });
</script>
</body>
</html>

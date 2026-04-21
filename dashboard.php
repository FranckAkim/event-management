<?php
// dashboard.php - Protected Main Dashboard
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$role      = strtolower($_SESSION['role'] ?? 'requester');
$isAdmin   = ($role === 'admin');
$isOrg     = ($role === 'organiser');
$isReq     = ($role === 'requester');
$canCreate = ($isAdmin || $isOrg);
$canManage = $isAdmin;  // only admin sees Reports, Attendees
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>CelebrateHub — Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <script>
        window.currentUserRole = "<?= htmlspecialchars($role) ?>";
        window.currentUserID = <?= (int)($_SESSION['user_id'] ?? 0) ?>;
        window.canCreateEvent = <?= $canCreate ? 'true' : 'false' ?>;
        window.canManage = <?= $canManage ? 'true' : 'false' ?>;
    </script>
    <script>
        // Apply saved theme before page renders to prevent flash
        (function() {
            if (localStorage.getItem('ch-theme') === 'dark') {
                document.documentElement.classList.add('dark-pending');
            }
        })();
    </script>
</head>

<body>
    <div class="topbar">
        <div class="topbar-inner">
            <div class="brand">
                <img src="assets/images/logo.svg" alt="CelebrateHub" width="38" height="38"
                    style="border-radius:50%;box-shadow:0 4px 14px rgba(255,107,157,.35);" />
                <div class="brand-name">Celebrate<span style="font-style:italic;font-weight:300;color:var(--peach);">Hub</span></div>
            </div>
            <div class="top-actions">
                <span class="pill">Mode: <b id="rolePill"><?= ucfirst($role) ?></b></span>
                <button class="theme-toggle" id="themeToggleBtn" type="button" title="Toggle dark / light mode" aria-label="Toggle theme">
                    <span id="themeIcon">☀️</span>
                    <div class="toggle-track">
                        <div class="toggle-thumb"></div>
                    </div>
                </button>
                <button class="btn small" id="helpBtn" type="button">Tips</button>
                <a href="logout.php" class="btn danger small">Logout</a>
            </div>
        </div>
    </div>

    <main class="wrap">
        <div class="grid">

            <!-- LEFT SIDEBAR -->
            <section class="card" id="authCard">
                <div class="hd">
                    <div>
                        <h2>Welcome</h2>
                        <p id="userName"><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></p>
                    </div>
                    <span class="badge ok" id="sidebarBadge">
                        <?php if ($isAdmin): ?>DB Connected<?php
                                                        elseif ($isOrg): ?>Loading...<?php
                                                                                    else: ?>Loading...<?php endif; ?>
                    </span>
                </div>
                <div class="bd">
                    <div class="stack">
                        <div class="field">
                            <label>Email</label>
                            <input value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" readonly />
                        </div>
                        <div class="field">
                            <label>Role</label>
                            <input value="<?= $isAdmin ? 'Administrator' : ($isOrg ? 'Organizer' : 'Attendee') ?>" readonly />
                        </div>

                        <div class="divider"></div>

                        <?php if ($canCreate): ?>
                            <!-- Quick Filters — only for admin and organiser -->
                            <div class="panel">
                                <h3>Quick Filters</h3>
                                <div class="field">
                                    <label for="venueFilter">Venue</label>
                                    <select id="venueFilter">
                                        <option value="all" selected>All venues</option>
                                        <option value="Grand Ballroom">Grand Ballroom</option>
                                        <option value="Outdoor Meadow">Outdoor Meadow</option>
                                        <option value="Garden Pavilion">Garden Pavilion</option>
                                        <option value="Riverside Hall">Riverside Hall</option>
                                        <option value="Cozy Chapel Hall">Cozy Chapel Hall</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="dateFilter">Date</label>
                                    <input id="dateFilter" type="date" />
                                </div>
                                <div class="chips">
                                    <button class="chip active" data-status="all" type="button">All</button>
                                    <button class="chip" data-status="warn" type="button">Near capacity</button>
                                    <button class="chip" data-status="danger" type="button">Conflict</button>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Attendee: show a simple event search instead -->
                            <div class="panel">
                                <h3>Find Events</h3>
                                <div class="field">
                                    <label for="dateFilter">Filter by Date</label>
                                    <input id="dateFilter" type="date" />
                                </div>
                                <div class="field">
                                    <label for="venueFilter">Filter by Venue</label>
                                    <select id="venueFilter">
                                        <option value="all" selected>All venues</option>
                                        <option value="Grand Ballroom">Grand Ballroom</option>
                                        <option value="Outdoor Meadow">Outdoor Meadow</option>
                                        <option value="Garden Pavilion">Garden Pavilion</option>
                                        <option value="Riverside Hall">Riverside Hall</option>
                                        <option value="Cozy Chapel Hall">Cozy Chapel Hall</option>
                                    </select>
                                </div>
                                <button class="btn primary" style="width:100%;margin-top:8px;"
                                    onclick="setActiveTab('events')">Browse Events</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- MAIN DASHBOARD -->
            <section class="card main">
                <div class="hd">
                    <div>
                        <h2 id="viewTitle">
                            <?= $isAdmin ? 'Admin Dashboard' : ($isOrg ? 'Organizer Workspace' : 'Attendee Portal') ?>
                        </h2>
                        <p id="viewSubtitle">
                            <?= $isAdmin
                                ? 'Manage venues, resources, time slots, and resolve conflicts.'
                                : ($isOrg
                                    ? 'Create events, request resources, and prevent scheduling conflicts.'
                                    : 'Browse upcoming events and manage your registrations.') ?>
                        </p>
                    </div>
                    <div class="tabs">
                        <!-- Dashboard: all roles -->
                        <button class="tab active" data-tab="dashboard" type="button">Dashboard</button>

                        <!-- Schedule: all roles -->
                        <button class="tab" data-tab="schedule" type="button">Schedule</button>

                        <!-- Events: all roles (but form is hidden for requester) -->
                        <button class="tab" data-tab="events" type="button">Events</button>

                        <?php if ($canCreate): ?>
                            <!-- Resources: admin + organiser only -->
                            <button class="tab" data-tab="resources" type="button">Resources</button>
                        <?php endif; ?>

                        <?php if ($isAdmin): ?>
                            <!-- Attendees: admin only -->
                            <button class="tab" data-tab="attendees" type="button">Attendees</button>

                            <!-- Reports: admin only -->
                            <button class="tab" data-tab="reports" type="button">Reports</button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="bd" id="mainContent">
                    <!-- Dashboard content loaded by JS -->
                    <div style="text-align:center;padding:40px;color:var(--muted);">Loading dashboard...</div>
                </div>
            </section>
        </div>
    </main>

    <!-- Toast -->
    <div class="toast" id="toast" role="status" aria-live="polite">
        <h5 id="toastTitle"></h5>
        <p id="toastMsg"></p>
    </div>

    <!-- Event Action Modal — only for admin/organiser -->
    <?php if ($canCreate): ?>
        <div id="eventActionModal" style="display:none; position:fixed; inset:0; background:#0008; z-index:1000; align-items:center; justify-content:center;">
            <div style="background:var(--panel); border-radius:18px; padding:32px; min-width:340px; max-width:420px; box-shadow:0 8px 40px #0006;">
                <h3 id="modalEventTitle" style="margin:0 0 8px;">Event Name</h3>
                <p id="modalEventMeta" style="color:var(--muted); margin:0 0 24px; font-size:13px;">Venue • Time</p>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php if ($canCreate): ?>
                        <button class="btn primary" id="modalEditBtn" style="width:100%;">✏️ Edit Event</button>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <button class="btn danger" id="modalDeleteBtn" style="width:100%;">🗑️ Delete Event</button>
                    <?php endif; ?>
                    <button class="btn" id="modalCancelBtn" style="width:100%;">Cancel</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="assets/js/script.js"></script>
</body>

</html>
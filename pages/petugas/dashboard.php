<?php

declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
requireRole(2);
$unitId = (int) ($_SESSION['unit_id'] ?? 0);
if (!$unitId) {
    http_response_code(403);
    exit('Unit kerja Petugas belum ditetapkan.');
}
$name = (string) ($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Petugas');
$stmt = $pdo->prepare("SELECT COUNT(*) total, COALESCE(SUM(status_arsip='Aktif'),0) aktif, COALESCE(SUM(status_arsip='Aktif' AND tanggal_jatuh_tempo > CURDATE()),0) retensi, COALESCE(SUM(status_arsip='Musnah' AND YEAR(updated_at)=YEAR(CURDATE())),0) musnah FROM arsip WHERE unit_id=:unit");
$stmt->execute(['unit' => $unitId]);
$stats = $stmt->fetch() ?: ['total' => 0, 'aktif' => 0, 'retensi' => 0, 'musnah' => 0];
$stmt = $pdo->prepare("SELECT COALESCE(SUM(status_arsip='Aktif' AND (tanggal_jatuh_tempo IS NULL OR tanggal_jatuh_tempo <= CURDATE())),0) aktif, COALESCE(SUM(status_arsip='Aktif' AND tanggal_jatuh_tempo > CURDATE()),0) retensi, COALESCE(SUM(status_arsip='Permanen'),0) permanen, COALESCE(SUM(status_arsip='Musnah'),0) musnah FROM arsip WHERE unit_id=:unit");
$stmt->execute(['unit' => $unitId]);
$status = $stmt->fetch() ?: ['aktif' => 0, 'retensi' => 0, 'permanen' => 0, 'musnah' => 0];
$stmt = $pdo->prepare("SELECT DATE_FORMAT(created_at,'%Y-%m') month_key, COUNT(*) jumlah FROM arsip WHERE unit_id=:unit AND created_at >= DATE_SUB(DATE_FORMAT(CURDATE(),'%Y-%m-01'), INTERVAL 5 MONTH) GROUP BY DATE_FORMAT(created_at,'%Y-%m')");
$stmt->execute(['unit' => $unitId]);
$trendRows = $stmt->fetchAll();
$trendMap = [];
foreach ($trendRows as $row) $trendMap[(string) $row['month_key']] = (int) $row['jumlah'];
$trendLabels = $trendData = [];
$month = new DateTimeImmutable('first day of this month');
for ($i = 5; $i >= 0; $i--) {
    $date = $month->modify("-$i months");
    $key = $date->format('Y-m');
    $trendLabels[] = $date->format('M Y');
    $trendData[] = $trendMap[$key] ?? 0;
}
$stmt = $pdo->prepare('SELECT j.nama_jenis, COUNT(a.arsip_id) jumlah FROM jenis_surat j LEFT JOIN arsip a ON a.jenis_surat_id=j.jenis_surat_id AND a.unit_id=:unit GROUP BY j.jenis_surat_id,j.nama_jenis HAVING COUNT(a.arsip_id)>0 ORDER BY jumlah DESC,nama_jenis');
$stmt->execute(['unit' => $unitId]);
$types = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT a.arsip_id,a.no_registrasi,a.perihal,v.tanggal_jatuh_tempo,v.status_jadwal FROM view_jadwal_pemusnahan v INNER JOIN arsip a ON a.arsip_id=v.arsip_id WHERE a.unit_id=:unit AND v.tanggal_jatuh_tempo BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 30 DAY) ORDER BY v.tanggal_jatuh_tempo LIMIT 5");
$stmt->execute(['unit' => $unitId]);
$reminders = $stmt->fetchAll();
$stmt = $pdo->prepare('SELECT arsip_id,no_registrasi,no_surat,perihal,tanggal_surat,status_arsip,tanggal_jatuh_tempo FROM arsip WHERE unit_id=:unit ORDER BY created_at DESC LIMIT 5');
$stmt->execute(['unit' => $unitId]);
$items = $stmt->fetchAll();
$chart = ['trend' => ['labels' => $trendLabels, 'data' => $trendData], 'status' => ['labels' => ['Aktif', 'Dalam Retensi', 'Permanen', 'Musnah'], 'data' => array_map('intval', [$status['aktif'], $status['retensi'], $status['permanen'], $status['musnah']])], 'types' => ['labels' => array_column($types, 'nama_jenis'), 'data' => array_map('intval', array_column($types, 'jumlah'))]];
function archiveStatus(array $item): string
{
    return $item['status_arsip'] === 'Aktif' && !empty($item['tanggal_jatuh_tempo']) && $item['tanggal_jatuh_tempo'] > date('Y-m-d') ? 'Dalam Retensi' : (string) $item['status_arsip'];
}
function statusClass(string $status): string
{
    return match ($status) {
        'Aktif' => 'aktif',
        'Dalam Retensi' => 'retensi',
        'Permanen' => 'permanen',
        'Musnah' => 'musnah',
        default => 'default'
    };
}
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1"><?php require dirname(__DIR__, 2) . '/includes/favicon.php'; ?><title>Dashboard Petugas | e-ARSIP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Material+Symbols+Outlined" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --navy: #07306b;
            --navy-dark: #08285a;
            --blue: #2563eb;
            --ink: #12224a;
            --muted: #62708c;
            --line: #e6ebf3;
            --bg: #f4f6fb
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font: 14px Inter, "Segoe UI", Arial, sans-serif
        }

        .sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 2;
            width: 264px;
            min-width: 264px;
            max-width: 264px;
            box-sizing: border-box;
            padding: 18px 14px;
            background: linear-gradient(180deg, var(--navy), var(--navy-dark));
            color: #fff;
            display: flex;
            flex-direction: column
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            padding: 5px 10px 28px
        }

        .brand>.petugas-logo {
            display: block;
            width: 112px;
            height: 56px;
            margin: 0 0 12px;
            border-radius: 10px;
            overflow: hidden;
            clip-path: inset(0 round 10px)
        }

        .brand img {
            display: block;
            width: 112px;
            height: 56px;
            object-fit: contain;
            border-radius: 10px
        }

        .brand b,
        .brand small,
        .brand span {
            display: block
        }

        .brand b {
            color: #f97316;
            font-size: 20px;
            font-weight: 800;
            line-height: 1.1
        }

        .brand small {
            margin-top: 3px;
            color: #f4f7ff;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .04em;
            line-height: 1.15
        }

        .brand>div span {
            margin-top: 7px;
            color: #dbeafe;
            font-size: 11px;
            line-height: 1.35
        }

        nav {
            display: grid;
            gap: 5px
        }

        nav p {
            margin: 18px 10px 10px;
            color: #dbeafe;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase
        }

        .menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 12px;
            border-radius: 10px;
            color: #f4f7ff;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none
        }

        .menu:hover,
        .menu.active {
            background: var(--blue)
        }

        .menu i {
            font-size: 20px
        }

        .access {
            margin-top: auto;
            padding: 15px;
            border: 1px solid var(--blue);
            border-radius: 16px;
            background: var(--navy-dark)
        }

        .access i {
            color: #86aefb
        }

        .access b,
        .access span,
        .access small {
            display: block
        }

        .access b {
            margin: 9px 0 4px;
            font-size: 12px
        }

        .access span,
        .access small {
            color: #dbeafe;
            font-size: 11px;
            line-height: 1.5
        }

        .access small {
            margin-top: 6px;
            font-size: 10px;
            font-weight: 700
        }

        .main {
            min-height: 100vh;
            margin-left: 264px
        }

        .header {
            min-height: 84px;
            padding: 14px 28px;
            border-bottom: 1px solid var(--line);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px
        }

        .header-brand b,
        .header-brand small {
            display: block
        }

        .header-brand b {
            font-size: 18px
        }

        .header-brand small {
            margin-top: 3px;
            color: var(--muted);
            font-size: 11px
        }

        .user-menu {
            position: relative
        }

        .user-menu summary {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            list-style: none
        }

        .user-menu summary::-webkit-details-marker {
            display: none
        }

        .avatar {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #eaf2ff;
            color: var(--blue);
            font-weight: 800
        }

        .user-copy b,
        .user-copy small {
            display: block
        }

        .user-copy b {
            font-size: 12px
        }

        .user-copy small {
            margin-top: 2px;
            color: var(--muted);
            font-size: 10px
        }

        .user-menu summary>i {
            color: #71809a;
            font-size: 18px
        }

        .dropdown {
            position: absolute;
            right: 0;
            top: 48px;
            width: 180px;
            padding: 8px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 12px 28px #0f172a18
        }

        .dropdown a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px;
            border-radius: 8px;
            color: #b91c1c;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none
        }

        .dropdown a:hover {
            background: #fef2f2
        }

        .dropdown i {
            font-size: 18px
        }

        .content {
            max-width: 1600px;
            padding: 18px 22px 28px
        }

        .welcome {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 6px 0 18px
        }

        .welcome h1 {
            margin: 0 0 6px;
            font-size: 24px
        }

        .welcome p {
            margin: 0;
            color: var(--muted);
            font-size: 13px
        }

        .welcome>i {
            padding: 12px;
            border-radius: 14px;
            background: #eaf2ff;
            color: var(--blue);
            font-size: 27px
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px
        }

        .stat,
        .panel {
            border: 1px solid var(--line);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 1px 2px #0f172a0a, 0 10px 24px #0f172a06
        }

        .stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px
        }

        .stat small,
        .stat strong {
            display: block
        }

        .stat small {
            color: #7182a1;
            font-size: 12px
        }

        .stat strong {
            margin-top: 7px;
            font-size: 28px
        }

        .stat i {
            display: grid;
            place-items: center;
            width: 50px;
            height: 50px;
            border: 1px solid;
            border-radius: 14px;
            font-size: 24px
        }

        .s-blue i {
            background: #eff6ff;
            color: #2563eb;
            border-color: #dbeafe
        }

        .s-cyan i {
            background: #ecfeff;
            color: #0891b2;
            border-color: #cffafe
        }

        .s-purple i {
            background: #f5f3ff;
            color: #7c3aed;
            border-color: #e9d5ff
        }

        .s-red i {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fee2e2
        }

        .grid {
            display: grid;
            grid-template-columns: 1.3fr .9fr;
            gap: 16px;
            margin-bottom: 16px
        }

        .panel {
            padding: 18px
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px
        }

        .panel h2 {
            margin: 0;
            font-size: 15px
        }

        .panel-head span {
            color: var(--muted);
            font-size: 11px
        }

        .chart {
            height: 270px
        }

        .lower {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px
        }

        .reminders {
            display: grid;
            gap: 10px
        }

        .reminder {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 10px 0;
            border-bottom: 1px solid #edf1f6
        }

        .reminder:last-child {
            border: 0
        }

        .reminder i {
            display: grid;
            place-items: center;
            width: 35px;
            height: 35px;
            border-radius: 10px;
            background: #fff7ed;
            color: #ea580c;
            font-size: 20px
        }

        .reminder b,
        .reminder small {
            display: block
        }

        .reminder b {
            font-size: 12px
        }

        .reminder small {
            margin-top: 3px;
            color: var(--muted);
            font-size: 11px
        }

        .hday {
            margin-left: auto;
            white-space: nowrap;
            color: #d97706;
            font-size: 12px;
            font-weight: 800
        }

        table {
            width: 100%;
            border-collapse: collapse
        }

        th {
            padding: 0 12px 11px;
            text-align: left;
            color: #71809a;
            font-size: 10px;
            letter-spacing: .05em;
            text-transform: uppercase
        }

        td {
            padding: 13px 12px;
            border-top: 1px solid #edf1f6;
            font-size: 12px
        }

        td:first-child,
        th:first-child {
            padding-left: 0
        }

        td:last-child,
        th:last-child {
            padding-right: 0
        }

        .subject {
            max-width: 310px;
            font-weight: 600
        }

        .muted {
            color: var(--muted)
        }

        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            white-space: nowrap
        }

        .aktif {
            background: #dcfce7;
            color: #166534
        }

        .retensi {
            background: #ffedd5;
            color: #9a3412
        }

        .permanen {
            background: #ede9fe;
            color: #6d28d9
        }

        .musnah {
            background: #fee2e2;
            color: #b91c1c
        }

        .default {
            background: #eef2f7;
            color: #475569
        }

        .edit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            max-width: max-content;
            padding: 7px 10px;
            border: 1px solid #c7d7fe;
            border-radius: 9px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
            transition: background .15s ease, border-color .15s ease, box-shadow .15s ease
        }

        .edit:hover {
            border-color: #93b5fd;
            background: #dbeafe;
            box-shadow: 0 4px 10px #2563eb1a
        }

        .edit i {
            font-size: 16px;
            line-height: 1
        }

        .empty {
            padding: 14px 0;
            color: var(--muted);
            font-size: 12px
        }

        @media(max-width:1050px) {
            .stat-grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .grid,
            .lower {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:760px) {
            .sidebar {
                position: static;
                width: auto;
                min-height: 0
            }

            .access {
                display: none
            }

            .main {
                margin-left: 0
            }

            .header {
                padding: 14px 18px
            }

            .content {
                padding: 18px
            }

            .user-copy {
                display: none
            }

            .welcome>i {
                display: none
            }

            .stat-grid {
                grid-template-columns: 1fr
            }

            .table-wrap {
                overflow: auto;
                max-width: 100%
            }

            table {
                min-width: 670px
            }
        }

        @media(max-width:430px) {
            .brand {
                padding-bottom: 15px
            }

            nav {
                grid-template-columns: 1fr
            }

            .header-brand b {
                font-size: 14px
            }

            .welcome h1 {
                font-size: 20px
            }
        }

        .user-menu {
            position: relative;
            margin-left: auto
        }

        .user-menu summary {
            display: flex;
            align-items: center;
            gap: 10px;
            list-style: none;
            padding: 8px 10px;
            border: 1px solid #d9e2f2;
            border-radius: 999px;
            background: #fff;
            color: #00173e;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s ease, box-shadow .15s ease, border-color .15s ease;
            white-space: nowrap
        }

        .user-menu summary::-webkit-details-marker {
            display: none
        }

        .user-menu summary:hover {
            border-color: #b9c8e6;
            box-shadow: 0 6px 16px #00000010;
            background: #f8fbff
        }

        .user-avatar {
            display: grid;
            place-items: center;
            overflow: hidden;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: linear-gradient(135deg, #00173e, #1459cf);
            color: #fff;
            font-size: 12px;
            font-weight: 700
        }

        .user-avatar img {
            display: block !important;
            flex: 0 0 100% !important;
            width: 100% !important;
            min-width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
            min-height: 100% !important;
            max-height: 100% !important;
            object-fit: cover !important;
            border-radius: inherit
        }

        .user-summary-text {
            display: grid;
            gap: 2px;
            min-width: 0;
            text-align: left
        }

        .user-summary-text b,
        .user-summary-text small {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap
        }

        .user-summary-text small {
            color: #667085;
            font-size: 10px;
            font-weight: 500
        }

        .user-summary-chevron {
            color: #667085;
            font-size: 18px
        }

        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            z-index: 30;
            width: min(280px, calc(100vw - 32px));
            padding: 8px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 14px 34px #00000018
        }

        .user-dropdown-header {
            display: grid;
            gap: 2px;
            padding: 10px 12px 12px
        }

        .user-dropdown-header b {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #101828;
            font-size: 14px
        }

        .user-dropdown-header small {
            color: #667085;
            font-size: 11px;
            font-weight: 500
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: #101828;
            text-decoration: none;
            transition: background .15s ease, color .15s ease
        }

        .user-dropdown a:hover {
            background: #fff1f2;
            color: #b42318
        }

        .user-dropdown .material-symbols-outlined {
            font-size: 18px
        }

        @media(max-width:760px) {
            .user-avatar img {
                display: block !important;
                flex: 0 0 100% !important;
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                height: 100% !important;
                min-height: 100% !important;
                max-height: 100% !important;
                object-fit: cover !important;
                border-radius: inherit
            }

            .user-summary-text {
                display: none
            }

            .user-menu summary {
                padding: 7px
            }

            .user-summary-chevron {
                display: none
            }
        }

        h1,
        h2,
        h3,
        .section-title {
            color: var(--ink)
        }

        p,
        small,
        .muted {
            color: var(--muted)
        }

        a:not(.menu) {
            color: var(--blue)
        }

        .content .button,
        .content .btn,
        .content .primary-button {
            background: var(--blue);
            color: #fff
        }

        .content .button:hover,
        .content .btn:hover,
        .content .primary-button:hover,
        .content .button:focus-visible,
        .content .btn:focus-visible,
        .content .primary-button:focus-visible {
            background: var(--navy);
            outline: 3px solid #bfdbfe;
            outline-offset: 2px
        }

        .content .button:disabled,
        .content .btn:disabled,
        .content .primary-button:disabled {
            background: var(--muted);
            color: #fff;
            cursor: not-allowed;
            opacity: .7
        }

        .badge,
        .status,
        .icon-box,
        .chart-icon {
            color: var(--blue)
        }

        .progress-bar,
        .progress-fill {
            background: var(--blue)
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="brand"><span class="petugas-logo"><img src="../../assets/images/logofix.png" alt="KAI"></span>
            <div><b>e-ARSIP</b><small>KAI DIVRE III</small><span>Web Pengarsipan Digital</span></div>
        </div>
        <nav>
            <p>Menu Utama</p><a class="menu active" href="dashboard.php"><i class="material-symbols-outlined">dashboard</i>Dashboard</a><a class="menu" href="arsip.php"><i class="material-symbols-outlined">folder_open</i>Arsip Digital</a><a class="menu" href="../shared/jadwal-pemusnahan.php"><i class="material-symbols-outlined">event</i>Jadwal Pemusnahan</a>
        </nav>
        <section class="access"><i class="material-symbols-outlined">verified_user</i><b>Akses Petugas</b><span>Anda login sebagai Petugas.</span><span>Kelola arsip sesuai unit kerja Anda.</span><small>Unit scoped</small></section>
    </aside>
    <div class="main">
        <header class="header">
            <div class="header-brand"><b>Dashboard Petugas</b><small>e-ARSIP KAI Divre III</small></div>
            <details class="user-menu">
                <summary><span class="user-avatar"><?= profileAvatar('../..', $name) ?></span><span class="user-summary-text"><b><?= e($name) ?></b><small>Petugas</small></span><i class="user-summary-chevron material-symbols-outlined">expand_more</i></summary>
                <div class="user-dropdown">
                    <div class="user-dropdown-header"><b><?= e($name) ?></b><small>Petugas</small></div><a href="../profile/ubah-profil.php"><i class="material-symbols-outlined">person</i>Ubah Profil</a><a href="../profile/ubah-password.php"><i class="material-symbols-outlined">key</i>Ubah Password</a><a href="../../logout.php"><i class="material-symbols-outlined">logout</i>Logout</a>
                </div>
            </details>
        </header>
        <main class="content">
            <section class="welcome">
                <div>
                    <h1>Selamat datang, <?= e($name) ?> 👋</h1>
                    <p>Kelola arsip digital dengan tertib dan pastikan pengarsipan berjalan optimal.</p>
                </div><i class="material-symbols-outlined">folder_managed</i>
            </section>
            <section class="stat-grid">
                <article class="stat s-blue">
                    <div><small>Total Arsip</small><strong><?= (int) $stats['total'] ?></strong></div><i class="material-symbols-outlined">inventory_2</i>
                </article>
                <article class="stat s-cyan">
                    <div><small>Arsip Aktif</small><strong><?= (int) $stats['aktif'] ?></strong></div><i class="material-symbols-outlined">task_alt</i>
                </article>
                <article class="stat s-purple">
                    <div><small>Dalam Retensi</small><strong><?= (int) $stats['retensi'] ?></strong></div><i class="material-symbols-outlined">hourglass_top</i>
                </article>
                <article class="stat s-red">
                    <div><small>Musnah Tahun Ini</small><strong><?= (int) $stats['musnah'] ?></strong></div><i class="material-symbols-outlined">delete_outline</i>
                </article>
            </section>
            <section class="grid">
                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Tren Arsip</h2><span>Enam bulan terakhir</span>
                        </div>
                    </div>
                    <div class="chart"><canvas id="trend"></canvas></div>
                </article>
                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Status Arsip</h2><span>Distribusi arsip unit</span>
                        </div>
                    </div>
                    <div class="chart"><canvas id="status"></canvas></div>
                </article>
            </section>
            <section class="lower">
                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Arsip Berdasarkan Jenis</h2><span>Jenis surat pada unit</span>
                        </div>
                    </div>
                    <div class="chart"><canvas id="types"></canvas></div>
                </article>
                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Pengingat Pemusnahan</h2><span>30 hari ke depan</span>
                        </div><a class="edit" href="../shared/jadwal-pemusnahan.php">Lihat semua</a>
                    </div>
                    <div class="reminders"><?php foreach ($reminders as $reminder): $days = (int) ((new DateTimeImmutable('today'))->diff(new DateTimeImmutable($reminder['tanggal_jatuh_tempo']))->format('%r%a')); ?><div class="reminder"><i class="material-symbols-outlined">event</i>
                                <div><b><?= e((string) $reminder['no_registrasi']) ?></b><small><?= e((string) $reminder['perihal']) ?></small></div><span class="hday">H-<?= $days ?></span>
                            </div><?php endforeach; ?><?php if (!$reminders): ?><div class="empty">Tidak ada jadwal pemusnahan dalam 30 hari ke depan.</div><?php endif; ?></div>
                </article>
            </section>
            <section class="panel">
                <div class="panel-head">
                    <div>
                        <h2>Arsip Terbaru</h2><span>Lima arsip terakhir pada unit kerja</span>
                    </div><a class="edit" href="arsip.php">Lihat semua</a>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Registrasi</th>
                                <th>No. Surat</th>
                                <th>Perihal</th>
                                <th>Tanggal Surat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody><?php foreach ($items as $item): $label = archiveStatus($item); ?><tr>
                                    <td><b><?= e((string) $item['no_registrasi']) ?></b></td>
                                    <td class="muted"><?= e((string) $item['no_surat']) ?></td>
                                    <td class="subject"><?= e((string) $item['perihal']) ?></td>
                                    <td class="muted"><?= e((string) $item['tanggal_surat']) ?></td>
                                    <td><span class="badge <?= statusClass($label) ?>"><?= e($label) ?></span></td>
                                    <td><a class="edit" href="arsip.php?edit=<?= (int) $item['arsip_id'] ?>"><i class="material-symbols-outlined">edit</i><span>Edit</span></a></td>
                                </tr><?php endforeach; ?><?php if (!$items): ?><tr>
                                    <td colspan="6" class="empty">Belum ada arsip pada unit kerja ini.</td>
                                </tr><?php endif; ?></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
    <script>
        const data = <?= json_encode($chart, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            base = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            color: '#62708c',
                            font: {
                                family: 'Inter',
                                size: 11
                            }
                        }
                    }
                }
            };
        new Chart(document.getElementById('trend'), {
            type: 'line',
            data: {
                labels: data.trend.labels,
                datasets: [{
                    data: data.trend.data,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,.10)',
                    fill: true,
                    tension: .35,
                    pointRadius: 3
                }]
            },
            options: {
                ...base,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#71809a'
                        },
                        grid: {
                            color: '#edf1f6'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#71809a'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        new Chart(document.getElementById('status'), {
            type: 'doughnut',
            data: {
                labels: data.status.labels,
                datasets: [{
                    data: data.status.data,
                    backgroundColor: ['#22c55e', '#f59e0b', '#8b5cf6', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                ...base,
                cutout: '68%'
            }
        });
        new Chart(document.getElementById('types'), {
            type: 'bar',
            data: {
                labels: data.types.labels,
                datasets: [{
                    data: data.types.data,
                    backgroundColor: '#2563eb',
                    borderRadius: 6,
                    maxBarThickness: 42
                }]
            },
            options: {
                ...base,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            color: '#71809a'
                        },
                        grid: {
                            color: '#edf1f6'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#71809a'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>
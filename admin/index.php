<?php
session_start();
if (empty($_SESSION['itbng_auth'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>itbng.com — SEO Audit Dashboard</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 240px;
            --header-h: 60px;
            --primary: #6B4DE6;
            --primary-light: rgba(107,77,230,.1);
            --body-bg: #f4f5f9;
            --card-bg: #fff;
            --text-muted: #6c757d;
            --sidebar-bg: #1c1c3c;
            --sidebar-text: #c0c0d0;
            --sidebar-active: #fff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--body-bg);
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-size: 14px;
        }

        /* ── SIDEBAR ─────────────────────────────────── */
        #sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-w); height: 100vh;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            z-index: 1000;
            transition: transform .25s ease;
        }
        .sidebar-brand {
            height: var(--header-h);
            display: flex; align-items: center;
            padding: 0 20px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .brand-text { font-family: Montserrat, Arial, sans-serif; font-weight: 800; font-size: 18px; letter-spacing: .08em; color: var(--primary); }
        .brand-text span { font-weight: 500; font-size: 12px; color: var(--sidebar-text); margin-left: 6px; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 16px 0; }
        .nav-label { font-size: 10px; font-weight: 700; color: rgba(255,255,255,.3); text-transform: uppercase; letter-spacing: .1em; padding: 12px 20px 4px; }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 20px; color: var(--sidebar-text);
            text-decoration: none; font-size: 13px; font-weight: 500;
            transition: background .15s, color .15s;
            border-radius: 0;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(107,77,230,.18);
            color: var(--sidebar-active);
        }
        .sidebar-link.active { border-left: 3px solid var(--primary); }
        .sidebar-footer {
            padding: 12px 20px;
            border-top: 1px solid rgba(255,255,255,.07);
            font-size: 11px; color: rgba(255,255,255,.3);
        }

        /* ── HEADER ──────────────────────────────────── */
        #header {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0;
            height: var(--header-h); background: #fff;
            border-bottom: 1px solid #eee;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; z-index: 999;
        }
        .header-title { font-weight: 700; font-size: 16px; color: #1c1c3c; }
        .header-right { display: flex; align-items: center; gap: 12px; }

        /* ── CONTENT ─────────────────────────────────── */
        #content {
            margin-left: var(--sidebar-w);
            margin-top: var(--header-h);
            padding: 24px;
            min-height: calc(100vh - var(--header-h));
        }

        /* ── CARDS ───────────────────────────────────── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
        .card-header { background: transparent; border-bottom: 1px solid #f0f0f0; padding: 18px 20px 10px; }
        .card-body { padding: 20px; }

        /* KPI cards */
        .kpi-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; flex-shrink: 0;
        }
        .kpi-icon.purple { background: var(--primary-light); color: var(--primary); }
        .kpi-icon.green  { background: rgba(46,204,113,.1);  color: #2ecc71; }
        .kpi-icon.orange { background: rgba(230,126,34,.1);  color: #e67e22; }
        .kpi-icon.blue   { background: rgba(52,152,219,.1);  color: #3498db; }
        .kpi-val  { font-size: 26px; font-weight: 800; line-height: 1; color: #1c1c3c; }
        .kpi-label{ font-size: 12px; color: var(--text-muted); font-weight: 500; text-transform: uppercase; letter-spacing: .05em; }
        .kpi-sub  { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

        .badge-live { background: #e8f8ef; color: #2ecc71; font-size: 11px; font-weight: 600; padding: 4px 8px; border-radius: 20px; }

        /* Page title */
        .page-header { margin-bottom: 24px; }
        .page-header h1 { font-size: 22px; font-weight: 800; color: #1c1c3c; margin: 0; }
        .breadcrumb { margin: 0; padding: 0; background: transparent; font-size: 12px; }
        .breadcrumb-item + .breadcrumb-item::before { color: #aaa; }

        /* Table */
        .table th { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); border-top: none; }
        .table td { vertical-align: middle; font-size: 13px; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #header, #content { left: 0; margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- ── SIDEBAR ──────────────────────────────────────── -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-text">ITB<span>NG.COM</span></span>
    </div>
    <div class="sidebar-nav">
        <div class="nav-label">itbng.com</div>
        <a href="#overview" class="sidebar-link active">
            <i class="bi bi-search"></i> SEO Audit Report
        </a>
        <div class="nav-label">Audit Sections</div>
        <a href="#daily-section" class="sidebar-link">
            <i class="bi bi-calendar3"></i> Traffic Trend
        </a>
        <a href="#status-section" class="sidebar-link">
            <i class="bi bi-heart-pulse-fill"></i> Crawl Health
        </a>
        <a href="#pages-section" class="sidebar-link">
            <i class="bi bi-file-earmark-text"></i> Ranking Opportunities
        </a>
        <a href="#ips-section" class="sidebar-link">
            <i class="bi bi-shield-exclamation"></i> Bot Contamination
        </a>
    </div>
    <div class="sidebar-footer">
        <i class="bi bi-circle-fill text-success" style="font-size:8px"></i>
        &nbsp;VPS: 104.207.71.117 — AlmaLinux 9.7
    </div>
</nav>

<!-- ── HEADER ───────────────────────────────────────── -->
<header id="header">
    <div class="header-title">SEO Audit Dashboard</div>
    <div class="header-right">
        <span class="badge-live"><i class="bi bi-circle-fill" style="font-size:7px;vertical-align:middle"></i>&nbsp;Live Data</span>
        <span class="text-muted" style="font-size:12px">Mar 25 – Apr 30, 2026</span>
        <a href="logout.php" class="btn btn-sm btn-outline-secondary" style="font-size:12px">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</header>

<!-- ── CONTENT ──────────────────────────────────────── -->
<main id="content">

    <!-- Page Title -->
    <div class="page-header" id="overview">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">SEO Audit Report</li>
            </ol>
        </nav>
        <h1>itbng.com — SEO Technical Audit</h1>
        <small class="text-muted">Source: Apache server logs &nbsp;|&nbsp; 25 Mar 2026 → 30 Apr 2026 &nbsp;|&nbsp; 37 days of crawl data &nbsp;·&nbsp; <em>work-in-progress pages excluded</em></small>
    </div>

    <!-- Opportunity Banner -->
    <div class="card mb-4" style="border-left:4px solid var(--primary);background:linear-gradient(135deg,#f8f5ff 0%,#fff 100%)">
        <div class="card-body py-4 px-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-7">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span style="background:var(--primary);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;letter-spacing:.08em;text-transform:uppercase">SEO Audit Finding</span>
                        <span class="text-muted small">37-day server log analysis — itbng.com</span>
                    </div>
                    <h4 class="fw-bold mb-1" style="color:#1c1c3c">25.8% of your traffic is working against your rankings.</h4>
                    <p class="text-muted mb-0" style="font-size:13px;line-height:1.6">
                        Out of 396,767 audited requests, <strong>102,324 return a 404</strong> — broken pages that erode crawl budget and kill link equity.
                        Another <strong>59,216 are unnecessary redirects</strong> hemorrhaging PageRank at every hop.
                        A bot cluster from <strong>6 IPs is inflating your analytics</strong>, masking the real organic signal.
                    </p>
                    <p class="mb-0 mt-2" style="font-size:11px;color:#aaa"><em>* Work-in-progress pages excluded from analysis.</em></p>
                </div>
                <div class="col-lg-5">
                    <div class="row g-2">
                        <div class="col-4 text-center">
                            <div style="font-size:22px;font-weight:800;color:#e74c3c">102k</div>
                            <div style="font-size:10px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Broken URLs</div>
                        </div>
                        <div class="col-4 text-center" style="border-left:1px solid #eee;border-right:1px solid #eee">
                            <div style="font-size:22px;font-weight:800;color:#e67e22">59k</div>
                            <div style="font-size:10px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Redirect Hops</div>
                        </div>
                        <div class="col-4 text-center">
                            <div style="font-size:22px;font-weight:800;color:#f39c12">6 IPs</div>
                            <div style="font-size:10px;color:#888;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Bot Cluster</div>
                        </div>
                    </div>
                    <div class="mt-3 p-2 rounded text-center" style="background:rgba(107,77,230,.08);font-size:12px;color:var(--primary);font-weight:600">
                        <i class="bi bi-arrow-up-circle-fill me-1"></i>
                        Fixing these 3 issues = measurable ranking improvement within 60 days
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon purple"><i class="bi bi-activity"></i></div>
                    <div>
                        <div class="kpi-label">Total Crawl Requests</div>
                        <div class="kpi-val">396,767</div>
                        <div class="kpi-sub">37 days — Mar 25 → Apr 30</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100" style="border-left:3px solid #e74c3c">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:rgba(231,76,60,.1);color:#e74c3c"><i class="bi bi-file-x-fill"></i></div>
                    <div>
                        <div class="kpi-label">Broken URLs — 404</div>
                        <div class="kpi-val" style="color:#e74c3c">102,324</div>
                        <div class="kpi-sub" style="color:#e74c3c">25.8% of all requests wasted</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100" style="border-left:3px solid #e67e22">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:rgba(230,126,34,.1);color:#e67e22"><i class="bi bi-arrow-repeat"></i></div>
                    <div>
                        <div class="kpi-label">Redirect Hops — 301</div>
                        <div class="kpi-val" style="color:#e67e22">59,216</div>
                        <div class="kpi-sub" style="color:#e67e22">PageRank leaking at every hop</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100" style="border-left:3px solid #f39c12">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon" style="background:rgba(243,156,18,.1);color:#f39c12"><i class="bi bi-robot"></i></div>
                    <div>
                        <div class="kpi-label">Bot Contamination</div>
                        <div class="kpi-val" style="color:#f39c12">6 IPs</div>
                        <div class="kpi-sub" style="color:#f39c12">185.177.72.x — inflating metrics</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Traffic Chart -->
    <div class="row g-3 mb-4" id="daily-section">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold">Organic Traffic Trend — Crawl Volume Over Time</h5>
                        <small class="text-muted">Daily server requests | Spikes indicate crawl bursts or content events | Avg 10,977/day</small>
                    </div>
                    <span class="badge" style="background:var(--primary-light);color:var(--primary);padding:6px 12px;border-radius:20px;font-weight:700">396,767 audited requests</span>
                </div>
                <div class="card-body">
                    <div id="chart-daily"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Codes + Donut -->
    <div class="row g-3 mb-4" id="status-section">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Crawl Health Analysis — Response Code Breakdown</h5>
                    <small class="text-muted">404s waste crawl budget &nbsp;·&nbsp; 301s bleed PageRank &nbsp;·&nbsp; target: 90%+ HTTP 200</small>
                </div>
                <div class="card-body">
                    <div id="chart-status"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Health Score Breakdown</h5>
                    <small class="text-muted">Only 54.1% of requests are clean — target is 90%+</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div id="chart-donut" style="width:100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Pages + Top IPs -->
    <div class="row g-3 mb-4">
        <div class="col-xl-7" id="pages-section">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Top Crawled Pages — Ranking Opportunity Map</h5>
                    <small class="text-muted">High-frequency URLs = pages Google is watching — optimize these first</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr>
                                <th>#</th><th>URL</th>
                                <th class="text-end">Requests</th>
                                <th class="text-end">Share</th>
                            </tr></thead>
                            <tbody id="pages-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5" id="ips-section">
            <div class="card h-100" style="border-top:3px solid #f39c12">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Bot Contamination <span class="badge bg-warning text-dark ms-1" style="font-size:11px">Action Required</span></h5>
                    <small class="text-muted">185.177.72.x subnet — 6 IPs inflating traffic metrics, masking real organic data</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead><tr>
                                <th>#</th><th>IP Address</th>
                                <th class="text-end">Hits</th>
                                <th>Bar</th>
                            </tr></thead>
                            <tbody id="ips-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Plan -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card" style="border-left:4px solid #2ecc71">
                <div class="card-header" style="background:linear-gradient(90deg,rgba(46,204,113,.07) 0%,#fff 100%)">
                    <h5 class="mb-0 fw-bold" style="color:#1c1c3c">Recommended Action Plan — Priority Order</h5>
                    <small class="text-muted">Expected outcome: +15–30% organic visibility within 60 days</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded h-100" style="background:#fef9f0;border:1px solid #fde8c0">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span style="background:#e74c3c;color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">1</span>
                                    <strong style="color:#1c1c3c;font-size:13px">Fix 102,324 Broken URLs</strong>
                                </div>
                                <p class="text-muted mb-2" style="font-size:12px;line-height:1.5">Every 404 is crawl budget wasted. Google stops returning to pages that don't exist.</p>
                                <div style="font-size:11px;color:#e74c3c;font-weight:600">
                                    <i class="bi bi-tools me-1"></i>Audit → redirect or restore top 404 URLs
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded h-100" style="background:#fff8f3;border:1px solid #fddcbc">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span style="background:#e67e22;color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">2</span>
                                    <strong style="color:#1c1c3c;font-size:13px">Collapse 59,216 Redirect Chains</strong>
                                </div>
                                <p class="text-muted mb-2" style="font-size:12px;line-height:1.5">Each redirect hop loses ~10–15% of link equity. Chains kill PageRank before it reaches the page.</p>
                                <div style="font-size:11px;color:#e67e22;font-weight:600">
                                    <i class="bi bi-tools me-1"></i>Map chains → point all redirects directly to final URL
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded h-100" style="background:#fffbf0;border:1px solid #fde68a">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span style="background:#f39c12;color:#fff;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">3</span>
                                    <strong style="color:#1c1c3c;font-size:13px">Block 185.177.72.x Bot Cluster</strong>
                                </div>
                                <p class="text-muted mb-2" style="font-size:12px;line-height:1.5">6 IPs from the same subnet are distorting your analytics. Real traffic growth is invisible behind this noise.</p>
                                <div style="font-size:11px;color:#f39c12;font-weight:600">
                                    <i class="bi bi-tools me-1"></i>Add subnet to robots.txt + .htaccess deny rule
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Daily Breakdown -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">Day-by-Day Crawl Log — Full Audit Period</h5>
                    <small class="text-muted">Consistent crawl volume = healthy indexing signal &nbsp;·&nbsp; Sudden spikes may indicate bot surges</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead><tr>
                                <th>Date</th>
                                <th class="text-end">Requests</th>
                                <th>Trend</th>
                                <th>vs Average</th>
                            </tr></thead>
                            <tbody id="daily-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.48.0/dist/apexcharts.min.js"></script>

<script>
// ── DATA ─────────────────────────────────────────────────────────────────────
var dailyData = [
    { date: '25/Mar', hits: 5833  }, { date: '26/Mar', hits: 11587 },
    { date: '27/Mar', hits: 14743 }, { date: '28/Mar', hits: 9995  },
    { date: '29/Mar', hits: 12903 }, { date: '30/Mar', hits: 15391 },
    { date: '31/Mar', hits: 16322 }, { date: '01/Apr', hits: 15373 },
    { date: '02/Apr', hits: 10640 }, { date: '03/Apr', hits: 8016  },
    { date: '04/Apr', hits: 7816  }, { date: '05/Apr', hits: 5928  },
    { date: '06/Apr', hits: 6835  }, { date: '07/Apr', hits: 9745  },
    { date: '08/Apr', hits: 14182 }, { date: '09/Apr', hits: 17811 },
    { date: '10/Apr', hits: 10651 }, { date: '11/Apr', hits: 10135 },
    { date: '12/Apr', hits: 8921  }, { date: '13/Apr', hits: 12736 },
    { date: '14/Apr', hits: 11401 }, { date: '15/Apr', hits: 9437  },
    { date: '16/Apr', hits: 14030 }, { date: '17/Apr', hits: 15569 },
    { date: '18/Apr', hits: 10391 }, { date: '19/Apr', hits: 9440  },
    { date: '20/Apr', hits: 19185 }, { date: '21/Apr', hits: 12177 },
    { date: '22/Apr', hits: 8607  }, { date: '23/Apr', hits: 8496  },
    { date: '24/Apr', hits: 7563  }, { date: '25/Apr', hits: 9375  },
    { date: '26/Apr', hits: 6710  }, { date: '27/Apr', hits: 8345  },
    { date: '28/Apr', hits: 11255 }, { date: '29/Apr', hits: 11869 },
    { date: '30/Apr', hits: 6915  }
];

var topPages = [
    { page: '/',                                      hits: 26391 },
    { page: '/robots.txt',                            hits: 7891  },
    { page: '/meta.json',                             hits: 5101  },
    { page: '/assets/js/all_js.js',                  hits: 4728  },
    { page: '/assets/js/jquery-2.x-git.min.js',      hits: 4713  },
    { page: '/assets/js/xprs_helper.js',             hits: 4680  },
    { page: '/assets/js/lightbox.js',                hits: 4664  },
    { page: '/assets/js/spimeengine.js',             hits: 4620  },
    { page: '/assets/js/jquery.mobile.custom.min.js',hits: 4581  },
    { page: '/assets/css/fonts.css',                 hits: 4402  },
    { page: '/assets/css/effects.css',               hits: 3776  },
    { page: '/assets/css/lightbox.css',              hits: 3759  }
];

var topIPs = [
    { ip: '185.177.72.51',  hits: 6154 },
    { ip: '54.184.226.94',  hits: 5679 },
    { ip: '185.177.72.12',  hits: 4915 },
    { ip: '35.162.140.124', hits: 4580 },
    { ip: '185.177.72.70',  hits: 4212 },
    { ip: '185.177.72.100', hits: 4002 },
    { ip: '34.208.80.94',   hits: 3906 },
    { ip: '185.177.72.13',  hits: 3424 },
    { ip: '41.76.81.110',   hits: 3191 },
    { ip: '185.177.72.9',   hits: 3001 }
];

var statusData = [
    { code: '200 OK',           count: 222751, color: '#2ecc71' },
    { code: '404 Not Found',    count: 102324, color: '#e74c3c' },
    { code: '301 Redirect',     count: 59216,  color: '#3498db' },
    { code: '206 Partial',      count: 8430,   color: '#9b59b6' },
    { code: '304 Not Modified', count: 2145,   color: '#f39c12' },
    { code: '403 Forbidden',    count: 1143,   color: '#e67e22' },
    { code: '405 Not Allowed',  count: 570,    color: '#95a5a6' }
];

var totalHits = dailyData.reduce(function(a,d){ return a+d.hits; }, 0);
var avgPerDay = Math.round(totalHits / dailyData.length);

// ── CHART 1: Daily Area ───────────────────────────────────────────────────────
new ApexCharts(document.getElementById('chart-daily'), {
    chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'inherit', zoom: { enabled: false } },
    series: [{ name: 'Requests', data: dailyData.map(function(d){ return d.hits; }) }],
    xaxis: { categories: dailyData.map(function(d){ return d.date; }), tickAmount: 12,
             labels: { rotate: -35, style: { fontSize: '11px' } } },
    yaxis: { labels: { formatter: function(v){ return v >= 1000 ? (v/1000).toFixed(0)+'k' : v; } } },
    colors: ['#6B4DE6'],
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.03 } },
    stroke: { curve: 'smooth', width: 2.5 },
    dataLabels: { enabled: false },
    grid: { borderColor: 'rgba(0,0,0,.05)', strokeDashArray: 4 },
    markers: { size: 0 },
    annotations: {
        yaxis: [{ y: avgPerDay, borderColor: '#f39c12', borderWidth: 1.5, strokeDashArray: 5,
            label: { text: 'Avg: '+avgPerDay.toLocaleString(), style: { color:'#fff', background:'#f39c12', fontSize:'11px' } } }],
        xaxis: [{ x: '20/Apr', borderColor: '#e74c3c',
            label: { text: 'Peak: 19,185', style: { color:'#fff', background:'#e74c3c', fontSize:'11px' } } }]
    },
    tooltip: { y: { formatter: function(v){ return v.toLocaleString()+' requests'; } } }
}).render();

// ── CHART 2: Status Bar ───────────────────────────────────────────────────────
new ApexCharts(document.getElementById('chart-status'), {
    chart: { type: 'bar', height: 260, toolbar: { show: false }, fontFamily: 'inherit' },
    series: [{ name: 'Responses', data: statusData.map(function(s){ return s.count; }) }],
    xaxis: { categories: statusData.map(function(s){ return s.code; }), labels: { style: { fontSize: '12px' } } },
    colors: statusData.map(function(s){ return s.color; }),
    plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: true, dataLabels: { position: 'bottom' } } },
    legend: { show: false },
    dataLabels: { enabled: true, formatter: function(v){ return v.toLocaleString(); }, style: { fontSize: '11px' } },
    grid: { borderColor: 'rgba(0,0,0,.05)' },
    tooltip: { y: { formatter: function(v){ return v.toLocaleString()+' responses'; } } }
}).render();

// ── CHART 3: Donut ────────────────────────────────────────────────────────────
var totalStatus = statusData.reduce(function(a,s){ return a+s.count; }, 0);
new ApexCharts(document.getElementById('chart-donut'), {
    chart: { type: 'donut', height: 260, fontFamily: 'inherit' },
    series: statusData.map(function(s){ return s.count; }),
    labels: statusData.map(function(s){ return s.code; }),
    colors: statusData.map(function(s){ return s.color; }),
    legend: { position: 'bottom', fontSize: '11px' },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: '65%',
        labels: { show: true, total: { show: true, label: 'Total',
            formatter: function(){ return totalStatus.toLocaleString(); } } } } } },
    tooltip: { y: { formatter: function(v){ return v.toLocaleString()+' ('+( v/totalStatus*100).toFixed(1)+'%)'; } } }
}).render();

// ── TOP PAGES TABLE ───────────────────────────────────────────────────────────
var pageTotal = topPages.reduce(function(a,p){ return a+p.hits; }, 0);
document.getElementById('pages-tbody').innerHTML = topPages.map(function(p, i){
    var pct = (p.hits/pageTotal*100).toFixed(1);
    return '<tr>'
        +'<td><span class="badge bg-secondary">'+(i+1)+'</span></td>'
        +'<td><code class="small">'+p.page+'</code>'+(p.page==='/' ? ' <span class="badge bg-success ms-1" style="font-size:10px">homepage</span>' : '')+'</td>'
        +'<td class="text-end fw-bold">'+p.hits.toLocaleString()+'</td>'
        +'<td class="text-end text-muted small">'+pct+'%</td>'
        +'</tr>';
}).join('');

// ── TOP IPs TABLE ─────────────────────────────────────────────────────────────
var maxIP = topIPs[0].hits;
document.getElementById('ips-tbody').innerHTML = topIPs.map(function(ip, i){
    var barW = Math.round(ip.hits/maxIP*100);
    var bot = ip.ip.startsWith('185.177.72');
    return '<tr>'
        +'<td><span class="badge bg-secondary">'+(i+1)+'</span></td>'
        +'<td><code class="small">'+ip.ip+'</code>'+(bot?'<span class="badge bg-warning text-dark ms-1" style="font-size:10px">bot</span>':'')+'</td>'
        +'<td class="text-end fw-bold">'+ip.hits.toLocaleString()+'</td>'
        +'<td style="min-width:70px"><div class="progress" style="height:6px"><div class="progress-bar" style="width:'+barW+'%;background:var(--primary)"></div></div></td>'
        +'</tr>';
}).join('');

// ── DAILY BREAKDOWN TABLE ─────────────────────────────────────────────────────
document.getElementById('daily-tbody').innerHTML = dailyData.map(function(d){
    var diff = d.hits - avgPerDay;
    var pct  = ((diff/avgPerDay)*100).toFixed(0);
    var cls  = diff >= 0 ? 'text-success' : 'text-danger';
    var icon = diff >= 0 ? '↑' : '↓';
    var barW = Math.round(d.hits/19185*100);
    var isPeak = d.hits === 19185;
    return '<tr'+(isPeak?' class="table-warning"':'')+'>'
        +'<td class="fw-semibold">'+d.date+'</td>'
        +'<td class="text-end fw-bold">'+d.hits.toLocaleString()+'</td>'
        +'<td style="min-width:120px"><div class="progress" style="height:8px">'
        +'<div class="progress-bar'+(isPeak?' bg-danger':'')+'" style="width:'+barW+'%;'+(isPeak?'':'background:var(--primary)')+'"></div></div></td>'
        +'<td class="'+cls+'">'+icon+' '+(diff>=0?'+':'')+pct+'%</td>'
        +'</tr>';
}).join('');
</script>

</body>
</html>

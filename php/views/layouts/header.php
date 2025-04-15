<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - SiGestEventos' : 'SiGestEventos'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1a56db;
            --secondary-color: #1e40af;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --text-light: #f8fafc;
            --text-dark: #1e293b;
            --border-radius: 0.5rem;
            --transition-speed: 0.3s;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .wrapper {
            display: flex;
            height: 100vh;
            width: 100%;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.5rem;
            color: white;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .sidebar-brand i {
            font-size: 1.5rem;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            margin: 0.25rem 0.75rem;
            transition: all var(--transition-speed) ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
        }

        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .sidebar .nav-link i {
            font-size: 1.25rem;
            width: 1.5rem;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            background-color: #f8fafc;
            position: relative;
        }

        .content-wrapper {
            padding: 2rem;
            height: calc(100vh - 70px); /* 70px es aproximadamente la altura del navbar */
            overflow-y: auto;
        }

        .navbar {
            background-color: white !important;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 2rem;
            height: 70px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .navbar-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-icon {
            color: #64748b;
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all var(--transition-speed) ease;
        }

        .notification-icon:hover {
            background-color: #f1f5f9;
            color: var(--primary-color);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: var(--border-radius);
            transition: all var(--transition-speed) ease;
            text-decoration: none;
            color: var(--text-dark);
        }

        .user-profile:hover {
            background-color: #f1f5f9;
        }

        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all var(--transition-speed) ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table th {
            font-weight: 500;
            color: #64748b;
            border-bottom-width: 1px;
        }

        .badge {
            padding: 0.35em 0.65em;
            font-weight: 500;
            font-size: 0.75em;
            border-radius: 9999px;
        }

        .badge-success {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-warning {
            background-color: #fef9c3;
            color: #854d0e;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .content-wrapper {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <?php include dirname(__FILE__) . '/submenu.php'; ?>
        </aside>

        <!-- Main content -->
        <main class="main-content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container-fluid px-0 d-flex justify-content-between align-items-center">
                    <h1 class="navbar-title mb-0"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
                    <div class="user-menu">
                        <a href="#" class="notification-icon">
                            <i class="bi bi-bell"></i>
                        </a>
                        <a href="#" class="user-profile">
                            <div class="user-avatar">
                                <i class="bi bi-person"></i>
                            </div>
                            <span>Usuario</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Content wrapper -->
            <div class="content-wrapper">
                <?php if(isset($content)) { echo $content; } ?>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar on mobile
        document.querySelector('.navbar-toggler')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
</body>
</html>

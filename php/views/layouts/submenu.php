<?php
// Obtener la página actual
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>

<div class="d-flex flex-column h-100">
    <a href="/php/views/dashboard/index.php" class="sidebar-brand">
        <i class="bi bi-calendar-check"></i>
        <span>SiGestEventos</span>
    </a>
    
    <ul class="nav nav-pills flex-column mb-auto px-0">
        <li class="nav-item">
            <a href="/php/views/dashboard/index.php" class="nav-link <?php echo $currentDir == 'dashboard' ? 'active' : ''; ?>">
                <i class="bi bi-house-door"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="/php/views/eventos/index.php" class="nav-link <?php echo $currentDir == 'eventos' ? 'active' : ''; ?>">
                <i class="bi bi-calendar-event"></i>
                <span>Eventos</span>
            </a>
        </li>
        <li>
            <a href="/php/views/participantes/index.php" class="nav-link <?php echo $currentDir == 'participantes' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i>
                <span>Participantes</span>
            </a>
        </li>
        <li>
            <a href="/php/views/inscripciones/index.php" class="nav-link <?php echo $currentDir == 'inscripciones' ? 'active' : ''; ?>">
                <i class="bi bi-person-check"></i>
                <span>Inscripciones</span>
            </a>
        </li>
        <li>
            <a href="/php/views/expositores/index.php" class="nav-link <?php echo $currentDir == 'expositores' ? 'active' : ''; ?>">
                <i class="bi bi-person-badge"></i>
                <span>Expositores</span>
            </a>
        </li>
    </ul>
</div>

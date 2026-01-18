<?php

/**
 * Admin Statistic Card Component
 * 
 * Renders a statistic card for the dashboard.
 * 
 * @var string $title Card Title
 * @var string $value Main Value
 * @var string $icon FontAwesome Icon Class
 * @var string $color Color theme (primary, success, info, warning, danger)
 * @var string|null $subtitle Optional subtitle text
 */

$color = $color ?? 'primary';
?>

<div class="card border-left-<?php echo $color; ?> shadow h-100 py-2">
    <div class="card-body">
        <div class="row no-gutters align-items-center">
            <div class="col mr-2">
                <div class="text-xs font-weight-bold text-<?php echo $color; ?> text-uppercase mb-1">
                    <?php echo htmlspecialchars($title); ?>
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                    <?php echo $value; // Allow HTML if formatted externally or escape before passing 
                    ?>
                </div>
                <?php if (isset($subtitle) && $subtitle !== ''): ?>
                    <div class="text-xs text-gray-800 mt-1"><?php echo $subtitle; ?></div>
                <?php endif; ?>
            </div>
            <div class="col-auto">
                <i class="<?php echo $icon; ?> fa-2x text-gray-300"></i>
            </div>
        </div>
    </div>
</div>
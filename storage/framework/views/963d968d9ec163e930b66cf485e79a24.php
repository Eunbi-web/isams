<?php
    $theme = auth()->user()->theme ?? 'system';
?>
<div style="position:relative;">
    <button type="button"
            id="themeToggleBtn"
            class="topbar-btn"
            style="width:38px;height:38px;"
            title="Theme: <?php echo e(ucfirst($theme)); ?> (System → Light → Dark)"
            aria-label="Toggle theme">
        <i id="themeToggleIcon" class="fas fa-adjust"></i>
    </button>
</div>

<?php /**PATH C:\Users\Acer\Herd\isams\resources\views/components/theme-toggle.blade.php ENDPATH**/ ?>
@php
    $theme = auth()->user()->theme ?? 'system';
@endphp
<div style="position:relative;">
    <button type="button"
            id="themeToggleBtn"
            class="topbar-btn"
            style="width:38px;height:38px;"
            title="Theme: {{ ucfirst($theme) }} (System → Light → Dark)"
            aria-label="Toggle theme">
        <i id="themeToggleIcon" class="fas fa-adjust"></i>
    </button>
</div>


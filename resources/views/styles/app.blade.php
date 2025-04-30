*,:root {
    --bs-primary: {{ $theme['primary_color'] }};
    --bs-primary-rgb: {{ $theme['primary_rgba'] }};
    --bs-primary-bg-subtle: {{ $theme['bg_subtle'] }};
    --bs-info-bg-subtle: {{ $theme['bg_subtle'] }};
    --bs-secondary-bg-subtle: {{ $theme['bg_subtle'] }};
    --bs-nav-pills-link-active-color: #fff;
    --bs-nav-pills-link-active-bg: {{ $theme['primary_color'] }};
}

[data-bs-theme=light][data-color-theme=Blue_Theme]:root .btn-primary, 
[data-bs-theme=dark][data-color-theme=Blue_Theme]:root .btn-primary {
    --bs-btn-bg: {{ $theme['btn_bg'] }};
    --bs-btn-border-color: {{ $theme['btn_border'] }};
    --bs-btn-hover-bg: {{ $theme['btn_hover_bg'] }};
    --bs-btn-hover-border-color: {{ $theme['btn_hover_border'] }};
}

.form-control:focus, .form-select:focus{
    border-color: {{ $theme['primary_color'] }};
}

.sidebar-nav ul .sidebar-item > .sidebar-link.active{
    background: var(--bs-primary-bg-subtle);
    color: var(--bs-primary);
}

.sidebar-nav ul .sidebar-item.selected > .sidebar-link.active, .sidebar-nav ul .sidebar-item.selected > .sidebar-link{
    background: var(--bs-primary) !impportant;
    color: var(--bs-white) !important;
}
.sidebar-nav ul .sidebar-item .sidebar-link.active.has-arrow::after,.sidebar-nav ul .sidebar-item .sidebar-link.active:hover.has-arrow::after{
    border-color: var(--bs-primary);
}
.sidebar-nav ul .sidebar-item.selected .sidebar-link.active.has-arrow::after,.sidebar-nav ul .sidebar-item.selected .sidebar-link.active:hover.has-arrow::after{
    border-color: var(--bs-white);
}
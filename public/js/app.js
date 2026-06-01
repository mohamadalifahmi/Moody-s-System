(function () {
    'use strict';

    var sidebar = document.getElementById('sidebar');
    var mainContent = document.getElementById('mainContent');
    var toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar && mainContent) {
        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            if (window.innerWidth <= 992) {
                sidebar.classList.toggle('open');
                document.body.classList.toggle('sidebar-open');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            }
        });

        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 992 && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                    sidebar.classList.remove('open');
                    document.body.classList.remove('sidebar-open');
                }
            }
        });
    }

    var submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            var parent = this.closest('.has-submenu');
            if (parent) {
                parent.classList.toggle('open');
            }
        });
    });

    if (typeof bootstrap !== 'undefined') {
        try {
            var autoAlerts = document.querySelectorAll('#autoAlert');
            autoAlerts.forEach(function (alert) {
                setTimeout(function () {
                    try {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                        bsAlert.close();
                    } catch(e) {}
                }, 5000);
            });
        } catch(e) {}

        try {
            var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            if (tooltipTriggerList.length > 0) {
                Array.from(tooltipTriggerList).map(function (el) {
                    try { return new bootstrap.Tooltip(el); } catch(e) {}
                });
            }
        } catch(e) {}

        try {
            var popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            if (popoverTriggerList.length > 0) {
                Array.from(popoverTriggerList).map(function (el) {
                    try { return new bootstrap.Popover(el); } catch(e) {}
                });
            }
        } catch(e) {}
    }

    document.addEventListener('click', function (e) {
        var confirmBtn = e.target.closest('[data-confirm]');
        if (confirmBtn) {
            var message = confirmBtn.getAttribute('data-confirm') || 'هل أنت متأكد من تنفيذ هذا الإجراء؟';
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        }
    });

    if (typeof flatpickr !== 'undefined') {
        try {
            var locale = 'ar';
            try { flatpickr.l10ns.ar; } catch(e) { locale = 'en'; }
            document.querySelectorAll('input[type="date"], input[type="time"], input[type="datetime-local"]').forEach(function (input) {
                try {
                    var config = {
                        locale: locale,
                        dateFormat: 'Y-m-d',
                        altFormat: 'Y/m/d',
                        altInput: true,
                        altInputClass: 'form-control flatpickr-alt',
                        disableMobile: true,
                    };
                    if (input.type === 'time') {
                        config.enableTime = true;
                        config.noCalendar = true;
                        config.dateFormat = 'H:i';
                        config.altFormat = 'H:i';
                        config.altInput = false;
                        config.disableMobile = false;
                    } else if (input.type === 'datetime-local') {
                        config.enableTime = true;
                        config.dateFormat = 'Y-m-d H:i';
                        config.altFormat = 'Y/m/d H:i';
                    }
                    var fp = flatpickr(input, config);
                } catch (err) { /* silently skip */ }
            });
        } catch(e) {}
    }

    var sidebarNavLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    sidebarNavLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 992 && sidebar.classList.contains('open')) {
                if (!this.classList.contains('submenu-toggle')) {
                    sidebar.classList.remove('open');
                    document.body.classList.remove('sidebar-open');
                }
            }
        });
    });

    var activeItem = document.querySelector('.sidebar-nav .nav-link.active');
    if (activeItem) {
        var parentSubmenu = activeItem.closest('.has-submenu');
        if (parentSubmenu) {
            parentSubmenu.classList.add('open');
        }
    }
})();

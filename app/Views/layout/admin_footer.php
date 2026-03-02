</div> <!-- .main-content -->

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- html2canvas -->
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('mainContent');
    const overlay = document.getElementById('sidebarOverlay');

    // Expandir on hover
    sidebar.addEventListener('mouseenter', function () {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('collapsed');
            content.classList.remove('expanded');
        }
    });

    // Colapsar al salir
    sidebar.addEventListener('mouseleave', function () {
        if (window.innerWidth > 768) {
            sidebar.classList.add('collapsed');
            content.classList.add('expanded');
        }
    });

    document.getElementById('toggleSidebar')?.addEventListener('click', function () {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
        }
    });

    document.getElementById('sidebarOverlay')?.addEventListener('click', function () {
        document.getElementById('sidebar').classList.remove('show');
        this.classList.remove('show');
    });

    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('show');
                document.getElementById('sidebarOverlay').classList.remove('show');
            }
        });
    });
</script>
</body>

</html>
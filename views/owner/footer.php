</div> </div> <footer style="text-align: center; padding: 20px; color: #7f8c8d; font-size: 13px; margin-left: var(--sidebar-width); transition: margin-left 0.3s ease;">
        &copy; <?= date('Y'); ?> Rasya.co Coffee & Eatery - E-CRM System
    </footer>

    <script>
        // 1. Fungsi Toggle Mini Sidebar (Hamburger)
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-mini');
            
            // Atur ulang margin footer saat sidebar mengecil
            const footer = document.querySelector('footer');
            if(document.body.classList.contains('sidebar-mini')) {
                footer.style.marginLeft = '75px';
            } else {
                footer.style.marginLeft = '260px';
            }
        }

        // 2. Fungsi Dropdown Sidebar (Jika Owner butuh submenu nantinya)
        function handleToggle(id, element) {
            if (document.body.classList.contains('sidebar-mini')) {
                document.body.classList.remove('sidebar-mini');
                document.querySelector('footer').style.marginLeft = '260px';
            }
            var target = document.getElementById(id);
            var icon = element.querySelector('.arrow-icon');
            if (target) target.classList.toggle('buka-dong');
            if (icon) icon.classList.toggle('putar-panah');
        }

        // 3. Fungsi Dropdown Profil di Top Bar
        function toggleProfileDropdown() {
            var dropdown = document.getElementById("profileDropdown");
            var panah = document.getElementById("panahProfil");
            if (dropdown) dropdown.classList.toggle("show");
            if (panah) panah.classList.toggle("putar-panah");
        }

        // 4. Menutup dropdown profil secara otomatis jika klik di luar area
        window.onclick = function(event) {
            if (!event.target.closest('.profile-container')) {
                var dropdown = document.getElementById("profileDropdown");
                var panah = document.getElementById("panahProfil");
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                    panah.classList.remove('putar-panah');
                }
            }
        }
    </script>
</body>

</html>
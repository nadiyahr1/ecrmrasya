</div> </div> <script>
        // 1. Fungsi Toggle Mini Sidebar (Hamburger)
        function toggleSidebar() {
            document.body.classList.toggle('sidebar-mini');
        }

        // 2. Fungsi Dropdown Sidebar (Manajemen Data & Laporan)
        function handleToggle(id, element) {
            // Cerdas: Jika sidebar sedang mode kecil (mini), buka dulu otomatis 
            // agar admin bisa melihat isi sub-menunya dengan jelas
            if (document.body.classList.contains('sidebar-mini')) {
                document.body.classList.remove('sidebar-mini');
            }

            var target = document.getElementById(id);
            var icon = element.querySelector('.arrow-icon');

            // Toggle Class Buka/Tutup
            target.classList.toggle('buka-dong');

            // Putar Panah
            if (icon) {
                icon.classList.toggle('putar-panah');
            }
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
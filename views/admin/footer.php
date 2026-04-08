 </div>
 <script>
     function toggleDropdown(id, el) {
         var drop = document.getElementById(id);
         var arrow = el.querySelector('.arrow');
         if (drop.style.display === "block") {
             drop.style.display = "none";
             arrow.style.transform = "rotate(0deg)";
         } else {
             drop.style.display = "block";
             arrow.style.transform = "rotate(180deg)";
         }
     }
 </script>
 </body>

 </html>
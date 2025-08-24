
  function initializeTheme() {
        const themeToggle = document.getElementById('theme-toggle');
        if (!themeToggle) return;

        const applyTheme = (theme) => {
          if (theme === 'dark') {
            document.body.classList.add('dark-theme');
            themeToggle.innerHTML = '<i class="fas fa-sun"></i>';
          } else {
            document.body.classList.remove('dark-theme');
            themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
          }
          if (typeof createUsageChart === 'function') {
            createUsageChart();
          }
        };

        const savedTheme = localStorage.getItem('theme') || 'light';
        applyTheme(savedTheme);

        // Remove existing listeners to avoid duplicates
        themeToggle.replaceWith(themeToggle.cloneNode(true));
        document.getElementById('theme-toggle').addEventListener('click', () => {
          const isDarkMode = document.body.classList.toggle('dark-theme');
          const newTheme = isDarkMode ? 'dark' : 'light';
          localStorage.setItem('theme', newTheme);
          applyTheme(newTheme);
        });
}
      
  // Sidebar toggle for mobile
        function showToggle() {
          document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
          });
}

         // Logout from navbar
function logoutAction() {
         
          document.getElementById('logout-link-navbar').addEventListener('click', function(e) {
            e.preventDefault();
            localStorage.removeItem('jwt');
            window.location.href = '/login.php';
          });
        }
<style>

  @media (max-width: 320px) {
  
    .navbar-brand img {
      height: 25px; 
    }

   
    .profile img {
      width: 30px; 
    }


    .profile span {
      font-size: 12px; 
    }
  }

  .profile a {
    text-decoration: none;
    color: inherit;
  }
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-light shadow-sm">
  <div class="container-fluid">
    <!-- Brand Logo -->
    <a class="navbar-brand" href="#">
      <img src="admin_dashboard_pics/logo.png" height="50" style="margin-left:25px">
    </a>

    <!-- Profile Section -->
    <div class="profile ms-auto">
      <img src="admin_dashboard_pics/admin_profile.png" alt="Profile Icon" class="rounded-circle">
      <a href="admin_settings.php"><span><?php echo $_SESSION['lname']; ?></span></a>
    </div>
  </div>
</nav>

<?php 

include '../debug.php';
include '../config/dep.php';

session_start();
if (!isset($_SESSION['User']) || $_SESSION['User']===null) {
  header('Location: /account/login.php');
  exit();
}

$user = $_SESSION['User'];

$user->getVMs();
$user->getBalance();

?>

<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Online VPS Service">
        <meta name="author" content="workhorse.">
        <title>workhorse. - Dashboard</title>
        <!-- Bootstrap core CSS -->
        <link href="/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom fonts for this template -->
        <link href="/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
        <link href="/vendor/simple-line-icons/css/simple-line-icons.css" rel="stylesheet" type="text/css">
        <link href="/https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

        <!-- Custom styles for this template -->
        <link href="/css/landing-page.min.css" rel="stylesheet">
        <link href="/css/site.css" rel="stylesheet">
    </head>
    <body>
        <!-- Navigation -->
        <nav class="navbar navbar-light bg-light static-top">
        <div class="container">
          <a class="navbar-brand" href="/">
            <img src="/img/logo1.png" id="logo-wh"/>
          </a>
          <a class="btn btn-primary" href="/account/signout.php">Sign out</a>
        </div>
      </nav>

        <!-- Masthead -->
      <header class="masthead text-white text-center" style="padding-bottom:0;padding-top:0;">
        <div class="overlay"></div>
        <div class="container">
      <div class="row">
        <div class="col-xl-9 mx-auto">
          <h1 class="mb-5 mt-5">Dashboard</h1>
        </div>
        </div>
        </div>
      </header>

        
      <!-- Icons Grid -->
      <section class="features-icons bg-light text-center" style="padding-top:20px;">
        <div class="container">
          <div class="row">
            <div class="col-lg-12">
              <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3">
                <div class="features-icons-icon d-flex">
                  <i class="icon-user m-auto text-primary"></i>
                </div>
                <h3><?php echo "Hello, ".$user->email."<br>"; ?></h3>
                <p class="lead mb-0"><?php echo "Your balance is $".number_format(((float)$user->balance)/1000, 2, '.', ''); ?></p>
                <a href="/account/redeem.php"> Click here to redeem a coupon!</a>
              </div>
            </div>
          </div>
        </div>
        <div class="container" id="options">
            <div class="row">
                <div class="col-lg-6">
                    <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3" style="border:1px solid #007bff;border-radius: 25px;padding:10px;">
                    <a href="/dashboard/addvm.php" class="text-decoration-none">
                    <div class="features-icons-icon d-flex">
                        <i class="icon-plus m-auto text-primary"></i>
                    </div>
                    <h3>Create VM</h3>
                    <p class="lead mb-0">$14/mo ($0.021/hour)</p>
                    </a>
                  </div>
              </div>
              <div class="col-lg-6">
                    <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-3" style="border:1px solid #007bff;border-radius: 25px;padding:10px;">
                    <a href="/account/sshkeys.php" class="text-decoration-none">
                    <div class="features-icons-icon d-flex">
                        <i class="icon-key m-auto text-primary"></i>
                    </div>
                    <h3>Manage SSH Keys</h3>
                    <p class="lead mb-0">Changes will apply to new VMs</p>
                    </a>
                  </div>
              </div>
            </div>
            <br>
            <div class="text-center">
            <p class="lead mb-0"><?php echo "You have ".sizeof($user->vms)." active Virtual Machine(s)"; ?></p>
            </div>
        </div>
        <div class="container" id="virtual-machines">
          <?php for ($i=0;$i<sizeof($user->vms);$i++) { ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="features-icons-item mx-auto mb-5 mb-lg-0 mb-lg-4" style="max-width:1000px;">
                    <div class="features-icons-icon d-flex">
                        <i class="icon-screen-desktop m-auto text-primary"></i>
                    </div>
                    <h3><?php echo $user->vms[$i]->subdomain; ?></h3>
                    <p class="lead mb-0"><span style="font-weight: 700;">http:</span> <?php echo $user->vms[$i]->subdomain; ?>.wrecktheline.com (port 80)</p>
                    <p class="lead mb-0"><span style="font-weight: 700;">ssh:</span> ssh vagrant@wrecktheline.com -p <?php echo $user->vms[$i]->sshport; ?></p>
                    <p class="lead mb-0"><span style="font-weight: 700;">ftp:</span> ftp wrecktheline.com <?php echo $user->vms[$i]->ftpport; ?> (Active only)</p>
                    <br>
                    <form action="/dashboard/removevm.php" method="POST">
                      <input type="hidden" name="id" value="<?php echo $user->vms[$i]->id;?>">
                      <button type="submit" class="btn btn-danger">Destroy machine</button>
                    </form>
                  </div>
              </div>
            </div>
          <?php } ?>

        </div>
      </section>

          <!-- Footer -->
        <footer class="footer bg-light">
        <div class="container">
          <div class="row">
            <div class="col-lg-12 h-100 text-center text-lg-center my-auto">
              <p class="text-muted small mb-4 mb-lg-0">&copy; workhorse. 2019. All Rights Reserved.</p>
            </div>
          </div>
        </div>
      </footer>
    
      <!-- Bootstrap core JavaScript -->
      <script src="/vendor/jquery/jquery.min.js"></script>
      <script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <script> 
      $(document).ready(function() {
          $('form button[type=submit]').click(function() {
              return confirm('Are you sure you want to delete this virtual machine?');
          });
      });
    </script>
    </body>

</html>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
  <a class="navbar-brand" href="/">WALLAPUSH</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_SESSION['nick']?></a>
        <div class="dropdown-menu" aria-labelledby="dropdown01">
          <a class="dropdown-item text-dark" href="/crud.php?op=3"><i class="fas fa-sign-out-alt"></i> Logout</a>
          <a class="dropdown-item text-danger" style="cursor: pointer;" data-toggle="modal" data-target="#delete_modal"><i class="fas fa-skull-crossbones"></i> Delete Account</a>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php?load=myads">My Ads</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="index.php?load=makead">Make Ad</a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0" action="index.php" method="GET">
      <input type="hidden" name="load" value="adslist">
      <input class="form-control mr-sm-2" type="text" name="filter" placeholder="Ad" aria-label="Search">
      <button class="btn btn-secondary my-2 my-sm-0" type="submit">Search</button>
    </form>
  </div>
</nav>

<!-- The Modal -->
<div class="modal" id="delete_modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Are you sure?</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        Remember if you delete the account you will never log in again. And your ads will be deleted.
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <a href="/crud.php?op=9" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>

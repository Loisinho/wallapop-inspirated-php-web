<main role="main" class="container">
    
    <div class="starter-template">
        <form class="text-center border border-light p-5" action="/crud.php?op=8" method="post">
            <p class="h4 mb-4">Reset Password</p>
            <input type="password" id="password" name="password" class="form-control" placeholder="Password" maxlength="125" pattern="(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$" required>
            <input class="form-control mb-4" type="hidden" id="email" name="email" value="<?php echo($_GET['email']) ?>">
            <input class="form-control mb-4" type="hidden" id="token" name="token" value="<?php echo($_GET['token']) ?>">
            <button class="btn btn-info my-4 btn-block" type="submit">Reset</button>
        </form>
    </div>
    
</main><!-- /.container -->

<main role="main" class="container">
    
    <div class="starter-template">

        <?php
        if (isset($_GET['id']) && $_GET['id'] != '') {
            try {
                $stmt = Database::getConection()->prepare('select * from anuncios where id = ? and id_usuario = ?');

                $stmt->bindParam(1, $_GET['id']);
                $stmt->bindParam(2, $_SESSION['id']);

                $stmt->execute();

                if ($stmt->rowCount() == 1) {
                    $ad = $stmt->fetch();
                    ?>

                    <form class="text-center border border-light p-5" action="/crud.php?op=5" method="post" enctype="multipart/form-data">
                        <p class="h4 mb-4">Edit Ad</p>
                        <div class="form-row mb-4">
                            <div class="col">
                                <input type="text" id="titulo" name="titulo" value="<?php echo($ad['titulo']) ?>" class="form-control" placeholder="Title.." maxlength="30" required>
                            </div>
                        </div>
                        <input type="text" id="descripcion" name="descripcion" value="<?php echo($ad['descripcion']) ?>" class="form-control mb-4" placeholder="Description.." required>
                        <input class="form-control mb-4" type="hidden" id="oldimg" name="oldimg" value="<?php echo($ad['imagen']) ?>">
                        <img id="print" class="mb-4 border" src="<?php if ($ad['imagen'] != '') echo('img/'.$ad['imagen']) ?>" alt="NO IMG" width="200" height="100">
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="img" onchange="writeFileName(this)">
                                <label id="filename" class="custom-file-label" for="file">Choose File</label>
                            </div>
                        </div>
                        <script>
                            function writeFileName(fileinput) {
                                var name = fileinput.value.split('\\').pop();
                                document.getElementById('filename').innerHTML = name;
                            }
                        </script>
                        <input class="form-control mb-4" type="hidden" id="id" name="id" value="<?php echo($ad['id']) ?>">
                        <button class="btn btn-info my-4 btn-block" type="submit">Edit</button>
                    </form>
                    
                    <?php
                } else {
                    $_SESSION['flashMsg'] = 'Ad not found.';
                    $_SESSION['alertType'] = 'alert-danger';
                    header('location: index.php?load=');
                }
            } catch (PDOException $e) {
                echo "SQL Error: ".$e->getMessage();
            }
        }
        ?>

    </div>
    
</main><!-- /.container -->

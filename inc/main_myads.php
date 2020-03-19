<main role="main" class="container">
    
    <div class="starter-template">

        <?php
        try {
            $stmt = Database::getConection()->prepare("select * from anuncios where id_usuario = ?");
            
            $stmt->bindParam(1, $_SESSION['id']);
            $stmt->execute();

            if ($stmt->rowCount() != 0) {
                $myAds = $stmt->fetchAll();
                echo "<div class='card-columns'>";
                foreach ($myAds as $k => $ad ) {
                    echo '<div class="card bg-secondary text-light">';
                    if (isset($ad['imagen']))
                        echo '<img class="card-img-top" src="img/'.$ad['imagen'].'" alt="ERROR '.$ad['imagen'].'">';
                    else
                        echo '<img class="card-img-top" src="" alt="NO IMG">';
                    echo '<div class="card-body">
                            <h5 class="card-title">'.$ad['titulo'].'</h5>
                            <p class="card-text">'.$ad['descripcion'].'</p>
                          </div>';
                    echo '<div class="card-footer bg-light">
                            <small class="text-muted">'.convertirFecha($ad['fecha']).'</small>
                            <br><a href="index.php?load=editad&id='.$ad['id'].'">Editar</a> <a href="crud.php?op=6&id='.$ad['id'].'">Borrar</a>
                          </div>';
                    echo '</div>';
                }
                echo "</div>";
            } else {
                echo "<p>There are no ads yet.</p>";
            }
        } catch (PDOException $e) {
            echo "SQL Error: ".$e->getMessage();
        }
        ?>
        
    </div>

</main>
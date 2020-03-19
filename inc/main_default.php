<main role="main" class="container">
    
    <div class="starter-template">

        <h1>WALLAPUSH</h1>
        <p class="lead">Join us for free.<br/>We are more than 30 million users.<br>Sell, exchange and buy used products.</p>
        
        <?php
        $colors = ['primary', 'secondary', 'danger', 'warning', 'info', 'dark'];

        try {
            $filter = false;
            if (isset($_GET['filter']) && $_GET['filter'] != '') {
                $stmt = Database::getConection()->prepare("select * from anuncios where titulo like '%".$_GET['filter']."%' order by fecha");
                $filter = true;
            } else {
                $stmt = Database::getConection()->prepare("select * from anuncios order by fecha");
            }

            $stmt->execute();

            if ($stmt->rowCount() != 0) {
                $myAds = $stmt->fetchAll();
                echo "<br><div class='card-columns'>";
                foreach ($myAds as $k => $ad ) {
                    $n = rand(0, 5);
                    echo '<div class="card bg-'.$colors[$n].' text-light">';
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
                          </div>';
                    echo '</div>';
                }
                echo "</div>";
            } else {
                if ($filter)
                    echo "<p>Ads not found.</p>";
                else
                    echo "<p>There are no ads yet.</p>";
            }
        } catch (PDOException $e) {
            echo "SQL Error: ".$e->getMessage();
        }
        ?>
    
    </div>
    
</main><!-- /.container -->

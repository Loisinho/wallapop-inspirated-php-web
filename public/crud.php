<?php
require '../inc/config.php';
$pdo = Database::getConection();

// TODO: Complete before use.
$url = '';

if (isset($_GET['op']) && $_GET['op'] != '') {
    switch ($_GET['op']) {
        case 1: // User registration.
            $_POST = limpiarFiltrar($_POST);

            if (camposObligatoriosOK(['nick', 'apellidos', 'nombre', 'email', 'password'], 'post')) {
                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                    if (filter_var($_POST['password'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>"/(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/")))) {
                        try {
                            $stmt = $pdo->prepare('select * from usuarios where nick=? or email=?');
                            
                            $stmt->bindParam(1, $_POST['nick']);
                            $stmt->bindParam(2, $_POST['email']);
                            $stmt->execute();
            
                            $user = $stmt->fetch();
                            if($_POST['nick'] != $user['nick']) {
                                if ($_POST['email'] != $user['email']) {
                                    try {
                                        $stmt = $pdo->prepare('insert into usuarios(nick, nombre, apellidos, email, password) values(?, ?, ?, ?, ?)');
                                        
                                        $encriptada = encriptar($_POST['password']);
                        
                                        $stmt->bindParam(1, $_POST['nick']);
                                        $stmt->bindParam(2, $_POST['nombre']);
                                        $stmt->bindParam(3, $_POST['apellidos']);
                                        $stmt->bindParam(4, $_POST['email']);
                                        $stmt->bindParam(5, $encriptada);
                                        $stmt->execute();
                        
                                        $_SESSION['flashMsg'] = "User {$_POST['nombre']} with nick {$_POST['nick']} inserted correctly.";
                                        $_SESSION['alertType'] = "alert-success";
                                        header("location: index.php?load=login");
                                    } catch (PDOException $e) {
                                        echo "SQL Error: ".$e->getMessage();
                                    }
                                } else {
                                    $_SESSION['flashMsg'] = 'Email already in use.';
                                    $_SESSION['alertType'] = 'alert-warning';
                                    header('location: index.php?load=signin');
                                }
                            } else {
                                $_SESSION['flashMsg'] = 'Nick already in use.';
                                $_SESSION['alertType'] = 'alert-warning';
                                header('location: index.php?load=signin');
                            }
                        } catch (PDOException $e) {
                            echo "SQL Error: ".$e->getMessage();
                        }
                    } else {
                        $_SESSION['flashMsg'] = 'Password is not valid.';
                        $_SESSION['alertType'] = 'alert-warning';
                        header('location: index.php?load=signin');
                    }
                } else {
                    $_SESSION['flashMsg'] = 'Email is not valid.';
                    $_SESSION['alertType'] = 'alert-warning';
                    header('location: index.php?load=signin');
                }
            } else {
                $_SESSION['flashMsg'] = 'Some required field is in blank.';
                $_SESSION['alertType'] = 'alert-warning';
                header('location: index.php?load=signin');
            }
            break;
        case 2: // Log in
            $_POST = limpiarFiltrar($_POST);

            if (camposObligatoriosOK(['user', 'password'], 'post')) {
                try {
                    $stmt = $pdo->prepare('select * from usuarios where nick=? or email=?');
                    
                    $stmt->bindParam(1, $_POST['user']);
                    $stmt->bindParam(2, $_POST['user']);
                    $stmt->execute();

                    if ($stmt->rowCount() != 0) {
                        $user = $stmt->fetch();
                        if (comprobarPassword($_POST['password'], $user['password'])) {
                            $_SESSION['logedIn'] = true;
                            $_SESSION['id'] = $user['id'];
                            $_SESSION['nick'] = $user['nick'];
                            $_SESSION['nombre'] = $user['nombre'];
                            $_SESSION['apellidos'] = $user['apellidos'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['password'] = $user['password'];
                            header("location: /");
                        } else {
                            $_SESSION['flashMsg'] = "Wrong password.";
                            $_SESSION['alertType'] = "alert-danger";
                            header('location: index.php?load=login');
                        }
                    } else {
                        $_SESSION['flashMsg'] = "Wrong user.";
                        $_SESSION['alertType'] = "alert-danger";
                        header('location: index.php?load=login');
                    }
                } catch (PDOException $e) {
                    echo "SQL Error: ".$e->getMessage();
                }
            } else {
                $_SESSION['flashMsg'] = 'Wrong user or password.';
                $_SESSION['alertType'] = 'alert-warning';
                header('location: index.php?load=login');
            }
            break;
        case 3: // Log out
            if ($_SESSION['logedIn'] == true) {
                session_destroy();
                session_start();
            } else {
                $_SESSION['flashMsg'] = 'Logout Error.';
                $_SESSION['alertType'] = 'alert-danger';
            }
            header('location: /');
            break;
        case 4: // Make ad
            $_POST = limpiarFiltrar($_POST);

            if (camposObligatoriosOK(['titulo', 'descripcion'], 'post')) {
                try {
                    $stmt = $pdo->prepare('insert into anuncios (id_usuario, titulo, descripcion, imagen) values (?, ?, ?, ?)');
                    
                    $stmt->bindParam(1, $_SESSION['id']);
                    $stmt->bindParam(2, $_POST['titulo']);
                    $stmt->bindParam(3, $_POST['descripcion']);

                    if (isset($_FILES['img']) && $_FILES['img']['size'] > 0) {
                        $img = subirFichero($config['img'], 'img');
                        if ($img) {
                            $stmt->bindParam(4, $img);
                            $stmt->execute();
    
                            $_SESSION['flashMsg'] = 'Ad has been made correctly.';
                            $_SESSION['alertType'] = 'alert-success';
                            header("location: index.php?load=myads");
                        } else {
                            $_SESSION['flashMsg'] = 'Fail uploading image.';
                            $_SESSION['alertType'] = 'alert-danger';
                            header("location: index.php?load=myads");
                        }
                    } else {
                        $stmt->bindValue(4, null);
                        $stmt->execute();

                        $_SESSION['flashMsg'] = 'Ad has been made correctly.';
                        $_SESSION['alertType'] = 'alert-success';
                        header("location: index.php?load=myads");
                    }
                } catch (PDOException $e) {
                    echo "SQL Error: ".$e->getMessage();
                }
            } else {
                $_SESSION['flashMsg'] = 'Missing one or more required fields.';
                $_SESSION['alertType'] = 'alert-warning';
                header('location: index.php?load=makead');
            }
            break;
        case 5: // Edit ad
            $_POST = limpiarFiltrar($_POST);

            if (camposObligatoriosOK(['titulo', 'descripcion'], 'post')) {
                try {
                    $stmt = $pdo->prepare("update anuncios set titulo = ?, descripcion = ?, imagen = ? where id = ? and id_usuario = ?");
    
                    $stmt->bindParam(1, $_POST['titulo']);
                    $stmt->bindParam(2, $_POST['descripcion']);
                    $stmt->bindParam(4, $_POST['id']);
                    $stmt->bindParam(5, $_SESSION['id']);
    
                    if (isset($_FILES['img']) && $_FILES['img']['size'] > 0) {
                        $img = subirFichero($config['img'], 'img');
    
                        unlink($config['img'].$_POST['oldimg']);
                        $stmt->bindParam(3, $img);
                    } else {
                        $stmt->bindParam(3, $_POST['oldimg']);
                    }
    
                    $stmt->execute();
    
                    $_SESSION['flashMsg'] = 'Updated correctly';
                    $_SESSION['alertType'] = 'alert-success';
                    header('location: index.php?load=myads');
                } catch (PDOException $e) {
                    echo 'SQL Error: '.$e->getMessage();
                }
                break;
            } else {
                $_SESSION['flashMsg'] = 'Missing one or more required fields.';
                $_SESSION['alertType'] = 'alert-warning';
                header('location: index.php?load=editad');
            }
        case 6: // Delete ad
            if ($_GET['id']) {
                try {
                    $stmt = $pdo->prepare('select * from anuncios where id = ? and id_usuario = ?');

                    $stmt->bindParam(1, $_GET['id']);
                    $stmt->bindParam(2, $_SESSION['id']);

                    $stmt->execute();

                    $ad = $stmt->fetch();

                    unlink($config['img'].$ad['imagen']);

                    $stmt = $pdo->prepare('delete from anuncios where id = ? and id_usuario = ?');

                    $stmt->bindParam(1, $_GET['id']);
                    $stmt->bindParam(2, $_SESSION['id']);

                    $stmt->execute();

                    $_SESSION['flashMsg'] = 'Ad has been deleted correctly.';
                    $_SESSION['alertType'] = 'alert-success';
                    header('location: index.php?load=myads');
                } catch(PDOException $e) {
                    echo "SQL Error: ".$e->getMessage();
                }
            } else {
                $_SESSION['flashMsg'] = 'Ad has not been deleted correctly.';
                $_SESSION['alertType'] = 'alert-danger';
                header('location: index.php?load=myads');
            }
            break;
        case 7: // Password recovery.
            $_POST = limpiarFiltrar($_POST);

            if (camposObligatoriosOK(['email'], 'post') && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                try {
                    $stmt = $pdo->prepare('select * from usuarios where email=? limit 1');
                    
                    $stmt->bindParam(1, $_POST['email']);
                    $stmt->execute();

                    if ($stmt->rowCount() != 0) {
                        $user = $stmt->fetch();
                        $token = md5(time());
                        try {
                            $stmt = $pdo->prepare("update usuarios set token = ? where email = ?");
                            $stmt->bindParam(1, $token);
                            $stmt->bindParam(2, $user['email']);
                            $stmt->execute();

                            $title = 'Wallapush Password Recovery';
                            $msg = '<html>
                                        <head>
                                            <title>Wallapush password recovery</title>
                                        </head>
                                        <body style="text-align: center;">
                                            <h2 style="color: navy;">Wallapush</h2>
                                            <p>Hi <b>'.$user['nick'].'</b>,</p>
                                            <p>You recently requested to reset your password for your account. Use this link below to reset it:</p>
                                            <br>
                                            <a href="'.$url.'index.php?load=recover&email='.$user['email'].'&token='.$token.'" style="padding: 10px; background: navy; border-radius: 10px; color: #fff;">RESET PASSWORD</a>
                                            <br><br>
                                            <p>If you did not request a password reset, please ignore this email or contact us if you have questions.</p>
                                            <br>
                                            <p>Thanks,<br>Wallapush team.</p>
                                        </body>
                                    </html>';
                            $headers  = 'MIME-Version: 1.0'."\r\n";
                            $headers .= 'Content-type: text/html; charset=UTF-8'."\r\n";
                            mail($user['email'], $title, $msg, $headers);
    
                            $_SESSION['flashMsg'] = "An email has been sent to: ".$user['email'];
                            $_SESSION['alertType'] = "alert-success";
                            header('location: index.php?load=blank');
                        } catch (PDOException $e) {
                            echo "SQL Error: ".$e->getMessage();
                        }
                    } else {
                        $_SESSION['flashMsg'] = "This email does not exist.";
                        $_SESSION['alertType'] = "alert-warning";
                        header('location: index.php?load=login');
                    }
                } catch (PDOException $e) {
                    echo "SQL Error: ".$e->getMessage();
                }
            } else {
                $_SESSION['flashMsg'] = 'Wrong email.';
                $_SESSION['alertType'] = 'alert-warning';
                header('location: index.php?load=login');
            }
            break;
        case 8: // Reset Password.
            $_POST = limpiarFiltrar($_POST);
            
            if (camposObligatoriosOK(['password'], 'post') && filter_var($_POST['password'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>"/(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/")))) {
                try {
                    $stmt = $pdo->prepare('select * from usuarios where email = ? and token = ?');

                    $stmt->bindParam(1, $_POST['email']);
                    $stmt->bindParam(2, $_POST['token']);

                    $stmt->execute();

                    if ($stmt->rowCount() == 1) {
                        $user = $stmt->fetch();
                        $encriptada = encriptar($_POST['password']);
                        try {
                            $stmt = $pdo->prepare('update usuarios set password = ?, token = ? where id = ?');

                            $stmt->bindParam(1, $encriptada);
                            $stmt->bindValue(2, null);
                            $stmt->bindParam(3, $user['id']);
        
                            $stmt->execute();

                            $_SESSION['flashMsg'] = 'Password has been updated successfully.';
                            $_SESSION['alertType'] = 'alert-success';
                            header('location: index.php?load=login');
                        } catch (PDOException $e) {
                            echo "SQL Error: ".$e->getMessage();
                        }
                    } else {
                        $_SESSION['flashMsg'] = 'Password has not been reset.';
                        $_SESSION['alertType'] = 'alert-danger';
                        header('location: index.php?load=login');
                    }    
                } catch(PDOException $e) {
                    echo "SQL Error: ".$e->getMessage();
                }
            } else {
                $_SESSION['flashMsg'] = 'Password has not been reset.';
                $_SESSION['alertType'] = 'alert-danger';
                header('location: index.php?load=login');
            }
            break;
        case 9: // Delete user & the user ads.
            try {
                $stmt = $pdo->prepare('select * from anuncios where id_usuario = ?');
                $stmt->bindParam(1, $_SESSION['id']);
                $stmt->execute();

                $ads = $stmt->fetchAll();

                $stmt = $pdo->prepare('delete from anuncios where id_usuario = ?');
                $stmt->bindParam(1, $_SESSION['id']);
                $stmt->execute();

                foreach ($ads as &$ad)
                    unlink($config['img'].$ad['imagen']);
    
                $stmt = $pdo->prepare('delete from usuarios where id = ?');
                $stmt->bindParam(1, $_SESSION['id']);
                $stmt->execute();

                session_destroy();
                session_start();
                header('location: /');
            } catch (PDOException $e) {
                echo "SQL Error: ".$e->getMessage();
            }
            break;
    }
}

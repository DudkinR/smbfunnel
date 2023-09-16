<?php
    global $app_variant;
    $app_name= ($app_variant==='cloudfunnels')
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->app_name ?>: Choose editor</title>
    <link rel="stylesheet" href="<?= $this->install_url ?>/assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= plugins_url("../assets/css/style.css", __FILE__) ?>" />
</head>

<body>
    <div class="container-fluid easy-editor-container">
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <h4 class="disclaimer-title">Disclaimer</h4>
                                <p>
                                    <strong><?= $this->editor_name ?></strong> is in beta mode, for setting up core
                                    functionality like creating OTO links and creating products & membership loops we
                                    still suggest using the default editor.
                                </p>
                            </div>
                            <div class="form-group">
                                <label><input type="checkbox" name="easy_editor_remember_choice">&nbsp;Remember my
                                    choice</label>
                            </div>
                            <div class="form-group text-right">
                                <button class="btn btn-default" name="easy_editor_select_editor" value="default">Use
                                    Default</button>
                                &nbsp;
                                <button class="btn btn-primary" name="easy_editor_select_editor"
                                    value="<?= CF_EASY_EDITOR_BUILDER_TYPE ?>">Use Easy Editor</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
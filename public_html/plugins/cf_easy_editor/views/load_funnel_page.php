<?php 
    if(isset($_GET['app_dir']))
    {
        $dir= base64_decode($_GET['app_dir']);
        ob_start();
        require_once($dir.'/req.php');
        $res= ob_get_contents();
        ob_end_clean();

        $blank_element_reg= "/<body>(\s|\n|\r)*<\/body>/i";
        $blank_template= '<body class="builderjs-layout">    
        <div builder-element="PageElement"></div></body>';

        $bootstrap= $load->loadBootstrap(true);
        $bootstrap_relative= $load->loadBootstrap();

        $additional_css= "<style>
        [builder-element=CellElement] .container {
          padding: 0;
          }
        </style>";
        
        if(preg_match($blank_element_reg, $res)){
            $path= "templates/default/6037a0a8583a7";
            $res= '<!DOCTYPE html>
            <html>
              <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta name="description" content="">
                <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
                <meta name="generator" content="AcelleSystemLayouts">
                <title>Blank</title>
                <!-- Bootstrap core CSS -->
                '.$bootstrap.$additional_css.'
              </head>
              <body class="builderjs-layout">
                <div builder-element="PageElement">
                </div>
              </body>
            </html>';
        }
        else{
            $res= str_replace($bootstrap_relative, $bootstrap.$additional_css, $res);
            if(strpos($res, 'class="builderjs-layout"')<1)
            {
              $res= str_replace("<body>", '<body class="builderjs-layout">', $res);
            }
        }
        echo $res;
    }
?>
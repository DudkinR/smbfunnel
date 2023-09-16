<?php
    global $document_root;

    $editor_path= $this->editor_url_path; // Contains trailing slash
    $templates= $this->loadEditorTemplates();
    $widgets= $this->loadCustomWidgets();
?>
<?php
    // find template url
    //$url = $editor_path.'templates/' . $_GET['type'] . '/' . $_GET['id'];

    $funnel_page_url= plugins_url('load_funnel_page.php', __FILE__);
    $funnel_page_url= add_query_arg(array(
        'app_dir'=> base64_encode($document_root),
        'loadalltemplatedata_get'=> 1,
        'fid'=> $_GET['fid'],
        'lbl'=> $_GET['lbl'],
        'abtype'=> $_GET['abtype']
    ), $funnel_page_url);

    $url= (isset($_GET['load_easy_editor_template']))? $editor_path.base64_decode($_GET['load_easy_editor_template']): $funnel_page_url;//"req.php?loadalltemplatedata_get=1&fid=".$_GET['fid']."&lbl=".$_GET['lbl']."&abtype=".$_GET['abtype']."";
?>
<!doctype html>
<html>

<head>
    <title><?= $this->editor_name ?> - (<?= $this->app_name ?>)</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php
                echo $header;
            ?>
    <link href="assets/img/logo.png" rel="icon" type="image/x-icon" />
    <link rel="stylesheet" href="<?= plugins_url('../assets/editor/dist/builder.css', __FILE__); ?>">
    <script>
    var cf_installation_url = "<?php echo get_option('install_url'); ?>";
    var cf_page_fid = <?php echo $_GET['fid']; ?>;
    var cf_page_abtype = "<?php echo $_GET['abtype']; ?>";
    var cf_page_lbl = "<?php echo $_GET['lbl']; ?>";
    var cf_page_categ = "<?php echo $_GET['category']; ?>";
    var cf_page_folder = "<?php echo $_GET['folder']; ?>";
    var cf_request = new ajaxRequest();

    var cf_global_fontawesome_url =
        `<link rel="stylesheet" href="<?php echo get_option('install_url'); ?>/assets/fontawesome/css/all.css"/>`;

    <?php
            for($i=0;$i<count($content['input_names']);$i++)
            {
                $content['input_names'][$i]='"'.htmlentities($content['input_names'][$i]).'"';
            }
            echo "const cf_global_page_inputs=[".implode(",",$content['input_names'])."];";
        ?>

    const cf_global_img_upload_info = {
        imgstore: 1,
        upload_location: "<?php echo str_replace('@folder@',$_GET['folder'],$content['img_dir']); ?>",
        img_base_url: "<?php echo str_replace('@folder@',$_GET['folder'],$content['img_url']);?>"
    };
    </script>

    <script src="<?= plugins_url("../assets/editor/dist/builder.js", __FILE__) ?>"></script>

    <?php
            if (isset($widgets["scripts"]) && is_array($widgets["scripts"])) {
                echo implode('', $widgets["scripts"]);
            }
        ?>
    <script>
    var isEasyEditorTemplate = false;
    var easyEditorCurrentTemplateURL = false;

    (function() {
        let url = new URL(window.location.href);
        if (url.searchParams.has('load_easy_editor_template')) {
            isEasyEditorTemplate = true;
            easyEditorCurrentTemplateURL = `<?= $url ?>`;
            url.searchParams.delete('load_easy_editor_template');
            window.history.pushState(null, null, url.href);
        }
    })()
    </script>
    <script>
    var editor;
    var params = new URLSearchParams(window.location.search);
    var templates =
        <?= (is_array($templates))? str_replace("@server-request-uri@", $_SERVER['REQUEST_URI'], json_encode($templates)) : "[]" ?>;

    var tags = [{
            type: 'label',
            tag: '{name}'
        },
        {
            type: 'label',
            tag: '{first_name}'
        },
        {
            type: 'label',
            tag: '{last_name}'
        },
        {
            type: 'label',
            tag: '{email}'
        }
    ];

    $(document).ready(function() {
        var strict = false;
        let assetUploadURL = "<?= $this->editor_asset_save_url ?>";

        try {
            let u = new URL(assetUploadURL);
            u.searchParams.append(`cf_global_img_upload_info`, btoa(JSON.stringify(cf_global_img_upload_info)));
            u.searchParams.append('save_easyeditor_assets', 1);
            assetUploadURL = u.href;
        } catch (err) {
            console.log(err);
        }

        editor = new Editor({
            strict: strict, // default == true
            showInlineToolbar: true, // default == true
            root: '<?= $editor_path ?>dist/',
            url: '<?= $url ?>',
            urlBack: window.location.origin,
            uploadAssetUrl: assetUploadURL,
            uploadAssetMethod: 'POST',
            uploadTemplateUrl: 'upload.php',
            uploadTemplateCallback: function(response) {
                window.location = response.url;
            },
            saveUrl: '<?= $this->editor_data_save_url ?>',
            saveMethod: 'POST',
            data: {
                _token: 'CSRF_TOKEN',
                type: 'default',
                template_id: '<?php cf_enc(get_option('site_token')); ?>'
            },
            templates: templates,
            tags: tags,
            changeTemplateCallback: function(url) {
                window.location = url;
            },

            /*
                Disable features: 
                change_template|export|save_close|footer_exit|help
            */
            // disableFeatures: [ 'change_template', 'export', 'save_close', 'footer_exit', 'help' ], 

            // disableWidgets: [ 'HeaderBlockWidget' ], // disable widgets
            export: {
                url: 'export.php'
            },
            backgrounds: [
                <?php $max_img=17; for($img=1; $img<=$max_img; $img++): ?> '<?= $editor_path ?>assets/image/backgrounds/images<?= $img ?>.jpg'
                <?= ($img < $max_img)? ",":"" ?>
                <?php endfor ?>
            ],
            loaded: function() {
                var thisEditor = this;
                // Load custom widgets
                <?php 
                                if(isset($widgets["widget_objects"]) && is_array($widgets["widget_objects"]) && count($widgets["widget_objects"])>0)
                                {
                                    foreach($widgets["widget_objects"] as $widget){
                                        echo "try{thisEditor.addContentWidget($widget, 0, 'Custom widget');}catch(err){console.log(err);}";
                                    }
                                }
                            ?>

                    //thisEditor.addContentWidget(new Video_widget, 0, 'Template Content');

                    // if (typeof(WidgetManager) !== 'undefined') {
                    //     var widgets = WidgetManager.init();

                    //     widgets.forEach(function(widget) {
                    //         thisEditor.addContentWidget(widget, 0, 'Template Content');
                    //     });
                    // }

                    (function() {
                        try {
                            document.querySelector(".toggle-to-default-editor").onclick =
                                function() {
                                    let conf= confirm("Are you sure about leaving?\nChanges you made may not be saved. Please save them before leaving!");
                                    if(conf){
                                        let u = new URL(window.location.href);
                                        u.searchParams.set('builder_type', 'default');
                                        u.searchParams.append('easy_editor_remember_choice', '1');
                                        window.location = u.href;
                                    }
                                }
                        } catch (err) {
                            console.log(err);
                        }
                    })()

            }
        });

        editor.init();
    });
    </script>
    <script>
        (function(){
            window.onbeforeunload= function(){
                let stat= false;
                let conf= confirm(`Are you sure about leaving?\nChanges you made may not be saved. Please save them before leaving!`);
                if(conf){stat= true;}
                return stat;
            };
        })()
    </script>

    <style>
    .lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
    }

    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 30px;
        height: 30px;
        margin: 4px;
        border-radius: 80%;
        border: 2px solid #aaa;
        border-color: #007bff transparent #007bff transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }

    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>
</head>

<body class="overflow-hidden">
    <div id="editorInitialLoader" style="text-align: center;
            height: 100vh;
            vertical-align: middle;
            padding: auto;
            display: flex;">
        <div style="margin:auto" class="lds-dual-ring"></div>
    </div>

    <script>
    switch (window.location.protocol) {
        case 'http:':
        case 'https:':
            //remote file over http or https
            break;
        case 'file:':
            alert('Please put the builderjs/ folder into your document root and open it through a web URL');
            window.location.href = "./index.php";
            break;
        default:
            //some other protocol
    }
    </script>

    <?php cf_media(true); ?>
    <?php echo $footer; ?>

    <style>
    .side-panel-container .content-right nav #nav-tab a {
        width: 33.3%;
        border-radius: 0px !important;
    }

    ._1content.widget-video {
        display: none !important;
    }

    .placeholder-input-control {
        display: none !important;
    }

    .top .top-right ul.icons {
        display: none;
    }

    .container.modules.widgets-sections {
        display: flex;
        flex-direction: column;
    }

    .container.modules.widgets-sections>div:nth-child(1) {
        order: 2;
        margin-top: 100px;
    }

    /*These functionalities needs to implement later*/
    .btn-save-and-close.menu-bar-action {
        display: none !important;
    }

    .btn-export.menu-bar-action {
        display: none !important;
    }

    .footer-exit-without-save {
        display: none !important;
    }

    .design.display.display-menu li:nth-child(4),
    .design.display.display-menu li:nth-child(6) {
        display: none !important;
    }

    .btn-close.menu-bar-action 
    {
        display: none !important;
    }

    /* End these functionalities needs to implement later*/

    .toggle-to-default-editor {
        margin-top: 2px;
    }

    .widget-row .layout-group.layout-group-2,
    .widget-row .layout-group.layout-group-3 {
        display: none !important;
    }
    </style>
</body>

</html>
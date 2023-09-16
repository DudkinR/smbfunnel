<?php
    if(isset($_GET['save_easyeditor_assets'])){
        $file_url= null;
        $loader= $GLOBALS['loader'];
        $cf_global_img_upload_info= json_decode(base64_decode($_GET['cf_global_img_upload_info']), true);
        $user_ob= $loader->loadUser();
        if(!$user_ob->isLoggedin()){die('@not-logged-in@');}


        if ($_POST['assetType'] == 'upload') {
            $funnel= $loader->loadFunnel();
            $img_data= $funnel->uploadAssets($_FILES['file'], $cf_global_img_upload_info['upload_location'], $cf_global_img_upload_info['img_base_url'], 'image');

            if($img_data){
                $file_url= $img_data['src'];
            }
        } elseif ($_POST['assetType'] == 'url') {
            $file_url = $_POST['url'];
        } elseif ($_POST['assetType'] == 'base64') {
            $file_url= $_POST['url_base64'];
        }

        if($file_url !==null){
            header('Content-Type: application/json');
            header("HTTP/1.1 200");
            echo json_encode([ 'url' => $file_url ]);
        }
    }
?>
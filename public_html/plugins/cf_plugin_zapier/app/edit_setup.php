<div class="container-fluid">
<div class="row page-titles mb-4">
                    <div class="col-md-5 align-self-center">
                        <h4 class="text-themecolor" id="commoncontainerid">Zapier</h4>
                    </div>
                    <div class="col-md-7 align-self-center text-right">
                        <div class="d-flex justify-content-end align-items-center">Setup and manage Zapier integration requirements</div>
                    </div>
</div>
<div class="row">
<div class="col-sm-4 offset-sm-4">

<div class="card shadow">
<div class="card-header  theme-text bg-white border-bottom-0"><?php w("Zapier  Credentials") ?></div>
<div class="card-body">
<form action="" method="post">

<?php if(get_option('zapier_auth_id')){ 
   // echo get_option('zapier_auth_id');
    ?>
<div class="form-group" data-toggle="tooltip" title="Copy To Clipboard" onclick="copyText('<?php echo get_option('zapier_auth_id'); ?>')" style='cursor:pointer;'>
<input type="text" style="cursor:pointer;" class="form-control" value="<?php echo get_option('zapier_auth_id'); ?>" placeholder="<?php w("Zapier Integration Token"); ?>" data-toggle="tooltip" title="<?php w("Copy to clipboard"); ?>" disabled=true>
</div>
<?php } ?>

<div class="row">
<?php if(get_option('zapier_auth_id')){ ?>
<div class="col" style="margin-top:5px !important;"><a href="https://zapier.com/developer/public-invite/64160/75f39b81fb61f77443aabdb8ac4f7fca/" target="_BLANK"><?php w("Create Zap"); ?></a></div>
<?php } ?>
<div class="col"><button type="submit" class="btn theme-button btn-block" name="createzapapintid"><?php if(get_option('zapier_auth_id')){echo t("Change Auth Token");}else{echo t("Create Auth Token");} ?></button></div>
</div>
</form>


</div>
</div>


</div>
</div>
</div>
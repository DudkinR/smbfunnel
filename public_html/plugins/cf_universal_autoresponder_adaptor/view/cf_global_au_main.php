<?php
global $mysqli;
global $dbpref;
$table= $dbpref.'quick_autoresponders';
$autores= 'cfglobalautoresponder';

$qry=$mysqli->query("SELECT * FROM `".$table."`  WHERE `autoresponder_name`='".$autores."' ORDER BY `id` DESC");
?>

<div class="container-fluid bg-white px-lg-4">

    <!--
        ================================
        CF Global AutoResponder Settings
                  Heading
        ================================
    -->
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="d-sm-flex global-top-border align-items-center justify-content-between mb-4 shadow p-3 rounded">
                <h6 class="mb-0">CF Universal Autoresponder Adaptor Settings</h6>
                <div>Create, edit, manage forms</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="mb-4 rounded">
                <form method="post" id="global_au_settings_main_form">
                    <input type="hidden" id="cfglobalau_ajax" name="cfglobalau_cfglobalau_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
                    <table class="cfglobal_styled_table">
                        <thead>
                            <tr class="cfglobal_bg_color">
                                <th>#</th>
                                <th>Form Name</th>
                                <th>Addded On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(mysqli_num_rows($qry) > 0) {
                                $i=0;
                                while($row = mysqli_fetch_array($qry)) 
                                {
                                    ?>
                                    <tr>
                                        <td><?= ++$i; ?></td>
                                        <td><?= $row['autoresponder'];?></td>
                                        <td><?= date('d-m-Y h:ia',$row['date_created']);?></td>
                                        <td>
                                            <a data-bs-toggle="tooltip" data-placement="top" title="Edit" href="index.php?page=cf_global_au_settings&cfglobal_au_id=<?= $row['id']; ?>">
                                                <i class="fas fa-edit text-warning"></i>
                                            </a>&nbsp;
                                            <button data-bs-toggle="tooltip" data-placement="top" title="Delete" type="button" class="border-0 bg-white" onclick="cfGlobalDelFunction(this.id)" id="<?= cf_enc($row['id'], 'encrypt'); ?>">
                                                <i class="fas fa-trash-alt text-danger"></i>
                                            </button>&nbsp;
                                        </td>
                                    </tr>
                                <?php }
                            }

                            else 
                            {
                                ?>
                                <td colspan="6" class="text-center">No any autoresponder created yet.</td>
                            <?php } ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <div class="container-fluid text-end">
        <a href="index.php?page=cf_global_au_settings&cfglobal_au_new" class="btn btn-primary global-bottom-border  border-0 mb-3 theme-button">Create New</a>
    </div>    
</div>
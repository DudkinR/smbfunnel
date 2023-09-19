<?php
global $mysqli;
global $dbpref;
global $app_variant;
$table = $dbpref.'cfmenu';
$user_id=$_SESSION['user' . get_option('site_token')]; 
$access= $_SESSION['access' . get_option('site_token')];
if($access=='admin'){
    $sql = $mysqli->query("SELECT * FROM `".$table."` ORDER BY `id` DESC");
}else{
$sql = $mysqli->query("SELECT * FROM `".$table."` WHERE `user_id`= ".$user_id." ORDER BY `id` DESC");
}
$num_rows = mysqli_num_rows($sql);
?>

<div class="container-fluid bg-white">
    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="d-sm-flex align-items-center justify-content-between mb-4 shadow p-3 rounded">
                <h6 class="mb-0"><?php w('CF Menu'); ?></h6>
                <div><?php w('Create, edit, and manage forms'); ?></div>
            </div>
        </div>
    </div>
    <input type="hidden" id="cfmenu_ajax" value="<?php echo get_option('install_url')."/index.php?page=ajax"; ?>" />

    <div class="row mt-5">
        <div class="container cfmenu-container">
            <ul class="cfmenu-responsive-table">
                <li class="cfmenu-table-header">
                    <div class="cfmenu-col cfmenu-col-1">#</div>
                    <div class="cfmenu-col cfmenu-col-2"><?php w('Title'); ?></div>
                    <div class="cfmenu-col cfmenu-col-3"><?php w('Shortcode'); ?></div>
                    <div class="cfmenu-col cfmenu-col-4"><?php w('Created Date'); ?></div>
                    <div class="cfmenu-col cfmenu-col-5"><?php w('Action'); ?></div>
                </li>

                <?php
                if($num_rows>0) {
                    $i=1;
                    while($row = mysqli_fetch_array($sql))
                    {
                ?>
                <li class="cfmenu-table-row" data-toggle="tooltip" data-placement="top" title="<?=$row['form_desc']?>">
                    <div class="cfmenu-col cfmenu-col-1" data-label="#"><?= $i ?></div>
                    <div class="cfmenu-col cfmenu-col-2" data-label="Title"><?= $row['form_name'] ?></div>
                    <div class="cfmenu-col cfmenu-col-3" data-label="Shortcode"><span class="text-success"><strong data-toggle="tooltip" data-placement="top" title="<?php w('Click to copy'); ?>" style="cursor:pointer;" onclick="cfmenuCopyText(`[cfmenu id=<?=$row['id']?>]`)">[cfmenu id=<?=$row['id']?>]</strong></span></div>
                    <div class="cfmenu-col cfmenu-col-4" data-label="Created Date"><?= $row['created_at'] ?></div>
                    <div class="cfmenu-col cfmenu-col-5" data-label="Action">
                        <a data-toggle="tooltip" data-placement="top" title="<?php w('Edit'); ?>" href="index.php?page=cfmenu_form_details&cfmenu_id=<?= $row['id'] ?>">
                            <i class="fas fa-edit text-warning"></i>
                        </a>&nbsp;
                        <button type="button" data-toggle="tooltip" data-placement="top" title="<?php w('Delete'); ?>" class="border-0 bg-transparent" onclick="cfmenuDelFunction(this.id)" id="<?= $row['id']; ?>">
                            <i class="fas fa-trash-alt text-danger"></i>
                        </button>&nbsp;
                    </div>
                </li>
                <?php
                ++$i;
                    }
                } else {
                ?>
                <li class="cfmenu-table-row bg-danger text-white">
                    <div class="mx-auto"><?php w('No any menubar created.'); ?></div>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="row my-5">
        <div class="container" style="max-width: 1000px;">
            <div class="text-right">
                <a href="index.php?page=cfmenu_form_details" class="cfmenu-buttons btn border-primary"><?php w('Create Navbar'); ?></a>
            </div>
        </div>
    </div>
</div>
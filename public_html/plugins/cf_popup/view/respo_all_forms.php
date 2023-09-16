<?php
global $mysqli;
global $dbpref;
$form_table = $dbpref.'respo_popup_form';
$form_extra_settings = $dbpref.'respo_extra_settings';

$query = $mysqli -> query( "SELECT * from `$form_table` INNER JOIN `$form_extra_settings` on $form_table.formId=$form_extra_settings.formId ORDER BY `id` desc" );
$num_row = mysqli_num_rows($query);
?>

<div class="container-fluid">
    <table id="table" class="styled-table">
        <thead>
            <tr class="bg-info">
                <th>Popup Settings</th>
                <th class="text-right">Manage Forms</th>
            </tr>
        </thead>
    </table>

    <div style="overflow-x: auto;">
        <form method="post" id="tableForm">
            <input type="hidden" id="cfrespo_ajax" name="cfrespo_cfrespo_ajax" value="<?php echo  get_option('install_url')."/index.php?page=ajax"; ?>" />
            <table id="table" class="styled-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Form Name</th>
                        <th>Shortcode</th>
                        <th>On click button shortcode</th>
                        <th>Subscribers</th>
                        <th>Addded On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                <?php
                    if($num_row > 0) {
                        $i=0;
                        while($row = mysqli_fetch_array($query)) {
                        ?>
                            <tr>
                                <td><?php echo ++$i; ?></td>
                                <td><?php echo $row['form_name']; ?></td>
                                <td>
                                    <div class="linkCopy">
                                        <strong id="id<?php echo $i; ?>" onclick="copyText(`[cf_popup id=<?php echo $row['formId']; ?>]`)" data-toggle="tooltip" title="Copy to clipboard" style="cursor:pointer;">
                                            [cf_popup id=<?php echo $row['formId']; ?>]
                                        </strong>
                                    </div>
                                </td>
                                <?php if($row['on_btn_click'] == 1) { ?>
                                    <td>
                                        <div class="linkCopy">
                                            <strong onclick="copyText(`#cf_popup_btn_<?php echo $row['formId']; ?>`)" data-toggle="tooltip" title="Copy to clipboard" style="cursor:pointer;">
                                                #cf_popup_btn_<?php echo $row['formId']; ?>
                                            </strong>
                                        </div>
                                    </td>
                                <?php } else { ?>
                                    <td>
                                        NA
                                    </td>
                                <?php } ?>
                                <td>
                                    <a href="index.php?page=cf_popup_created_forms&cfpopup_form_id=<?= $row['formId'] ?>">
                                    <?php
                                    $optin_ob = $this -> load('optin_control');
                                    echo $optin_ob->getLeadsCount( $row['formId'] );
                                    ?>
                                    </a>
                                </td>
                                <td><?php echo $row['created_at']; ?></td>
                                <td>
                                    <a href="index.php?page=cf_new_form&cf_popup_id=<?= $row['formId']; ?>">
                                        <i class="fas fa-edit text-warning"></i>
                                    </a>&nbsp;
                                    <a href="index.php?page=cf_popup_created_forms&cfpopup_form_id=<?= $row['formId'] ?>">
                                        <i class="fas fa-eye text-success"></i>
                                    </a>
                                    <button type="button" class="border-0" onclick="delFunction(this.id)" id="<?= $row['formId']; ?>">
                                        <i class="fas fa-trash-alt text-danger"></i>
                                    </button>&nbsp;
                                </td>
                            </tr>
                            <?php
                        }
                    }

                    else { ?>
                            <tr>
                                <td colspan="6" class="text-center">No any shortcode created yet.</td>
                            </tr>
                    <?php } ?>
                </tbody>
            </table>
        </form>
        <a href="index.php?page=cf_new_form" class="float-right"><button class="theme-btn"><i class="fas fa-pencil-alt"></i> Create New</button></a>
    </div>
</div>
<div class="cfrespo_message" style="position: absolute; right: -20px; top: 90px;">
</div>
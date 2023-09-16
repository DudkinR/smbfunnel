<?php
$has_form_id = false;
$optins_data = array();
$total_leads = 0;
$optin_data_ob = $this -> load('optin_control');

if(isset($_POST['cfpopup_del_optin'])) {
    $optin_data_ob -> deleteLeads($_POST['cfpopup_del_optin']);
}

if( isset($_GET['cfpopup_form_id']) ) {
    $has_form_id = true;
    $optins_data = $optin_data_ob -> getLeads( $_GET['cfpopup_form_id'] );
    $total_leads = $optin_data_ob -> getLeadsCount( $_GET['cfpopup_form_id'] );
}
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
        <table id="table" class="styled-table">
            <?php if( count($optins_data) > 0) { ?>
                <thead>
                    <tr>
                        <?php
                        for( $i=0; $i<count( $optins_data[0] ); $i++ ) {
                            echo "<th>".htmlentities($optins_data[0][$i])."</th>";
                        }
                        unset($optins_data[0]);
                        ?>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    <br><br><h1 class="text-info blockquote-footer">Leads Management</h1>
                    <?php
                    foreach( $optins_data as $index => $val ) {
                        echo "<tr>";
                        for( $i=0; $i<count($val); $i++) {
                            echo "<td>".htmlentities($val[$i])."</td>";
                        }

                        echo "
                            <td>
                                <form method='post'>
                                    <button class='border-0' name='cfpopup_del_optin' value='".$index."'>
                                        <i class='fas fa-trash text-danger'></i>
                                    </button>
                                </form>
                            </td>
                            ";
                        echo "</tr>";
                    }
                    ?>
                    <tr>
                        <td colspan="8" class="text-center text-info"> Total Leads: <?php echo $total_leads; ?> </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="mt-3 text-right">
                        <th colspan="7">
                            <form target="_BLANK" method="post">
                                <button type="submit" value="<?php echo $_GET['cfpopup_form_id']; ?>" name="cfpopup_export_csv" class="p-2 btn btn-primary text-white border-0">Export CSV</button>
                            </form>
                        </th>
                    </tr>
                </tfoot>
            <?php 
            } else if(isset( $_GET['cfpopup_form_id']) ) {
                echo '
                    <tbody>
                        <tr>
                            <td class="text-center">No Leads Found, <span class="text-primary" onclick="window.history.back()" style="cursor:pointer">Go Back</span></td>
                        </tr>
                    </tbody>
                ';
            } else { ?>
                <tbody>
                    <tr class="bg-info text-white">
                        <td class="text-center">Select Form</td>
                    </tr>
                    <tr>
                        <?php
                            $forms_ob = $this -> load('forms_control');
                            $forms = $forms_ob -> getMiniForms();
                            foreach($forms as $id => $form_name) {
                                echo '
                                    <tr>
                                        <td class="text-center">
                                            <a href="index.php?page=cf_popup_created_forms&cfpopup_form_id='.$id.'">
                                                <button class="p-2 border-primary mb-2"> '.$form_name.' </button>
                                            </a>
                                        </td>
                                    </tr>
                                ';
                            }
                        ?>
                    </tr>
                </tbody>
            <?php } ?>
        </table>
    </div>
</div>
<div class="cfrespo_message" style="position: absolute; right: -20px; top: 90px;">
</div>
<?php
$mysqli = $info['mysqli'];
$dbpref = $info['dbpref'];
?>
<style>
    /* Style the modal */
.modal {
    display: none; /* Initially hidden */
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
}

/* Style the modal content */
.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
}

/* Style the close button (X) */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

</style>
<div class="container-fluid">
    <?php

		$table = $dbpref . "subdomians";
        $query = "SELECT * FROM $table";
        $result = $mysqli->query($query);
        $trans_install_url = $_SERVER['HTTP_HOST'];
    ?> 
    <div class="row">
			<div class="col-sm-12 nopadding">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<th>#</th>
							<th><?php w("Name"); ?></th>
                            <th><?php w("URL"); ?></th>
							<th><?php w("Type"); ?></th>
							<th><?php w("User") ?></th>
						</thead>
                        <tbody>
                            <?php
                                $count = 0;
                                while ($row = $result->fetch_assoc()) {
                                    $count++;
                                    if($row['type']==0) $bg="bg-danger";
                                    else if($row['type']==1) $bg="bg-success";
                                    else if($row['type']==2) $bg="bg-warning";
                                    else $bg="bg-info";
                                    echo "<tr class='".$bg."'>";
                                    echo "<td>" . $count . "</td>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td>" . $row['url'] . "</td>";
                                    echo "<td>" . $row['type'] . "</td>";
                                    echo "<td>" . $row['user_id'] . "</td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>                                                
                </div>
            </div>
    </div>



    <!-- button for creating new subdomain -->
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary btn-sm" onclick="openModal()">Create Subdomain</button>
        </div>
    </div>

    <!-- Hidden Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <!-- Content for creating a new subdomain -->
            <!-- Add form elements and controls here -->
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Create New Subdomain</h2>
            <!-- Add form elements here -->
            <form action="?page=new_subdomain" method="post">
                <input type="hidden" name="createsubdomain" value="1">
                <div class="form-group">
                    <label for="subdomain_name">Subdomain Name:</label>
                    <input type="text" class="form-control" id="subdomain_name" name="subdomain_name" required>
                </div>
                <label for="subdomain_url">Subdomain URL:</label>
                <div class="input-group">
                    <input type="hidden" id="subdomain_url" name="subdomain_url" />
                    <div class="input-group-prepend" title="<?php w('Sub name'); ?>">
                        <span class="input-group-text" id="sub_url"></span>
                    </div>
                    <div class="input-group-prepend" title="<?php w('Base URL'); ?>">
                        <span class="input-group-text" id ="main_url">.<?= $trans_install_url?>/</span>
                    </div>
                    
                </div>

                <div class="form-group">
                    <label for="subdomain_type">Subdomain Type:</label>
                    <select class="form-control" id="subdomain_type" name="subdomain_type" required>
                        <option value="0">Type 0</option>
                        <option value="1">Type 1</option>
                        <option value="2">Type 2</option>
                    </select>
                </div>
                <button type="submit" onclick="AJAX_submit()" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
<script>
    // Function to open the modal
function openModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "block";
}

// Function to close the modal
function closeModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}
//проверяем что поля заполнены
function check_fields() {
    var subdomain_name = document.getElementById("subdomain_name").value;
    var subdomain_url = document.getElementById("subdomain_url").value;
    var subdomain_type = document.getElementById("subdomain_type").value;
    if (subdomain_name == "" || subdomain_url == "" || subdomain_type == "") {
        alert("Please fill all fields");
        return false;
    }
    return true;
}
//AJAX submit
function AJAX_submit() {
    if (check_fields()) {
        var subdomain_name = document.getElementById("subdomain_name").value;
        var subdomain_url = document.getElementById("subdomain_url").value;
        var subdomain_type = document.getElementById("subdomain_type").value;
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                alert("Subdomain created successfully");
                closeModal();
            }
        };
        var url = "/req.php";
        // post data ++ 
        var params = "subdomain_name=" + subdomain_name + "&subdomain_url=" + subdomain_url + "&subdomain_type=" + subdomain_type+ "&createsubdomain =1";
        xmlhttp.open("POST", url, true);
        xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlhttp.send(params);
    }
    
}
//при изменениии subdomain_name меняем  sub_url зеркально
document.getElementById("subdomain_name").addEventListener("input", function() {
    var subdomain_name = document.getElementById("subdomain_name").value;
    var main_url = document.getElementById("main_url").innerHTML;
    document.getElementById("sub_url").innerHTML = subdomain_name;
    document.getElementById("subdomain_url").value = subdomain_name+main_url;
});
</script>
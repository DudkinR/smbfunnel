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
    function check_gethostbyname($url) {
        $mass_sub=explode(".",$url);
        // delete https://
        $name_sub=str_replace("https://","",$mass_sub[0]);
        $ip = gethostbyname($url);
        if ($ip == $url) {
            return 0;
        } else {
            $ch = curl_init($url);
             // Устанавливаем опции cURL
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
            // Включаем следование по редиректам
            curl_setopt($ch, CURLOPT_HEADER, true);
            // Выполняем запрос
            $response = curl_exec($ch);
           

            // Закрываем cURL сеанс
            list($headers, $body) = explode("\r\n\r\n", $response, 2);
            // Закрываем cURL сеанс
            curl_close($ch);
            // Теперь $headers содержит заголовки ответа
            $mass_headers=explode("\r\n",$headers);
            $thead=explode(" ",$mass_headers[0]);
             // Получаем HTTP-код состояния
             $httpCode= $thead[1];
            if ($httpCode == 301 || $httpCode == 302) {
                // Если получен HTTP-код 301 или 302, это указывает на перенаправление
                return 1;
            } elseif ($httpCode == 200) {
                // Если получен HTTP-код 200, то перенаправления нет
                return 2;
            } else {
                // В случае другого HTTP-кода можно вернуть 0 или другое значение по вашему выбору
                return 0;
            }
        }
    }
		$table = $dbpref . "subdomians";
        $query = "SELECT * FROM $table";
        $result = $mysqli->query($query);
        
        $trans_install_url = $_SERVER['HTTP_HOST'];
    ?> 
    <div class="row">
        <div class="col-md-12">
            <h2><?=gethostbyname($trans_install_url)?></h2>
        </div>
    </div>
    <div class="row">
			<div class="col-sm-12 nopadding">
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<th>#</th>
							<th><?php w("Name"); ?></th>
                            <th><?php w("URL"); ?></th>
      						<th><?php w("Type"); ?></th>
                            <th><?php w("htaccess"); ?></th>
							<th><?php w("User") ?></th>
                            <th><?php w("Action") ?></th>
						</thead>
                        <tbody>
                            <?php
                                $count = 0;
                                while ($row = $result->fetch_assoc()) {
                                    $count++;
                                    if(check_gethostbyname(str_replace("/","",$row['url']) )==0)
                                    { $type=0;
                                     $bg="bg-danger";
                                     $btn_bg="text-warning";
                                     $text_htaccess= "You need to add CNAME on server";
                                    }
                                    else if(check_gethostbyname(str_replace("/","",$row['url']) )==1) {
                                        $type=1;
                                        $bg="bg-warning";
                                        $btn_bg="text-danger";
                                         $text_htaccess= "You have CNAME on server and  htaccess";
                                    }
                                    else if(check_gethostbyname(str_replace("/","",$row['url']) )==2){
                                        $type=2;
                                        $bg="bg-success";
                                        $btn_bg="text-danger";
                                        $text_htaccess= "
                                        <h6>You have CNAME on server , but  htaccess is not configured </h6>
                                        <br>
                                        <p class=\"bg-light\">
                                        RewriteEngine On
                                        <br>
                                        RewriteRule ^(.*)$ https://". $trans_install_url."/" . $row['name'] . "/$1 [R=301,L]
                                        </p>
                                         <h6>Or add index.php with code: </h6>
                                        <br>
                                        <p class=\"bg-light\">
                                        &lt;?php
                                        <br>
                                        header(\"Location: https://". $trans_install_url."/" . $row['name'] . "/\");
                                        <br>
                                        exit;
                                        <br>
                                        ?&gt;
                                        </p>
                                        ";
                                    }
                                    else{
                                        $type=0;
                                        $btn_bg="text-warning";
                                        $bg="bg-info"; $text_htaccess="error";
                                    }
                                    $query = "UPDATE $table SET type='$type' WHERE id=".$row['id'];
                                    $mysqli->query($query);
                                    echo "<tr class='".$bg."'>";
                                    echo "<td>" . $count . "</td>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td> <a href='https://".$row['url']."' target='_blank' >
                                    " . $row['url'] . "
                                    </a>
                                    </td>";
                                    echo "<td>" . $type . "</td>";
                                    echo "<td>" . $text_htaccess. "</td>";
                                    echo "<td>" . $row['user_id'] . "</td>";
                                    echo "<td>
                                    <form action=\"?page=delete_subdomain\" method=\"post\" >
                                    <input type=\"hidden\" name=\"delcname\" value=\"".$row['id']."\">
                                    <input type=\"hidden\" name=\"delete_cname\" value=\"".$row['name']."\">
                                    <button type=\"submit\" class=\"btn unstyled-button\" data-bs-toggle=\"tooltip\" title=\"\" data-bs-original-title=\"Delete CNAME\" aria-label=\"Delete CNAME\">
                                    <i class=\"fas fa-trash ".$btn_bg."\"></i>
                                    </button>
                                    </form>
                                    </td>";
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
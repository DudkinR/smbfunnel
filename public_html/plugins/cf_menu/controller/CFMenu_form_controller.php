<?php
if (!class_exists('CFMenu_form_controller')) {
    class CFMenu_form_controller
    {
        function __construct($arr)
        {
            $this->loader = $arr['loader'];
        }

        function createUpdateForm($post_data)
        {
            if ($post_data['cfmenu_update_insert'] == "DELETE") {
                echo $this->deleteFunction($post_data);
                return;
            }

            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfmenu";

            $form_name = $mysqli->real_escape_string($post_data['cfmenu_form_name']);
            $form_desc = $mysqli->real_escape_string($post_data['cfmenu_form_desc']);
            $id = $mysqli->real_escape_string($post_data['cfmenu_form_id']);

            $stringifyData = $mysqli->real_escape_string($post_data['stringifyData']);
            $custom_url = $mysqli->real_escape_string($post_data['custom_url']);
            $cfmenu_update_insert = $mysqli->real_escape_string($post_data['cfmenu_update_insert']);
            $cfmenu_ajax_insertUrl = $mysqli->real_escape_string($post_data['cfmenu_ajax_insertUrl']);
            $logo_details = json_encode(array(
                'cfmenu_logo_type' => $mysqli->real_escape_string($post_data['cfmenu_logo_type']),
                'cfmenu_logo_title' => $mysqli->real_escape_string($post_data['cfmenu_logo_title']),
                'cfmenu_logo_click_url' => $mysqli->real_escape_string($post_data['cfmenu_logo_click_url']),
                'cfmenu_logo_color' => $mysqli->real_escape_string($post_data['cfmenu_logo_color']),
                'cfmenu_logo_font_size' => $mysqli->real_escape_string($post_data['cfmenu_logo_font_size']),
                'cfmenu_logo_height' => $mysqli->real_escape_string($post_data['cfmenu_logo_height']),
                'cfmenu_logo_width' => $mysqli->real_escape_string($post_data['cfmenu_logo_width']),
                'cfmenu_logo_padding' => $mysqli->real_escape_string($post_data['cfmenu_logo_padding']),
                'cfmenu_logo_margin' => $mysqli->real_escape_string($post_data['cfmenu_logo_margin']),
                'cfmenu_logo_img_source' => $mysqli->real_escape_string($post_data['cfmenu_logo_img_source']),
                'cfmenu_logo_img_height' => $mysqli->real_escape_string($post_data['cfmenu_logo_img_height']),
                'cfmenu_logo_img_width' => $mysqli->real_escape_string($post_data['cfmenu_logo_img_width']),
                'cfmenu_logo_img_padding' => $mysqli->real_escape_string($post_data['cfmenu_logo_img_padding']),
                'cfmenu_logo_img_margin' => $mysqli->real_escape_string($post_data['cfmenu_logo_img_margin']),
                'cfmenu_logo_img_border_radius' => $mysqli->real_escape_string($post_data['cfmenu_logo_img_border_radius'])
            ));

            $manage_styles = json_encode(array(
                'cfmenu_navbar_sticky' => $mysqli->real_escape_string($post_data['cfmenu_navbar_sticky']),
                'cfmenu_navbar_gradient_background_drop' => $mysqli->real_escape_string($post_data['cfmenu_navbar_gradient_background_drop']),
                'cfmenu_navbar_gradient_background1' => $mysqli->real_escape_string($post_data['cfmenu_navbar_gradient_background1']),
                'cfmenu_navbar_gradient_background2' => $mysqli->real_escape_string($post_data['cfmenu_navbar_gradient_background2']),
                'cfmenu_navbar_gradient_background_combination_drop' => $mysqli->real_escape_string($post_data['cfmenu_navbar_gradient_background_combination_drop']),
                'cfmenu_manage_nav_textcolor' => $mysqli->real_escape_string($post_data['cfmenu_manage_nav_textcolor']),
                'cfmenu_manage_nav_hovercolor' => $mysqli->real_escape_string($post_data['cfmenu_manage_nav_hovercolor']),
                'cfmenu_manage_nav_border_radius' => $mysqli->real_escape_string($post_data['cfmenu_manage_nav_border_radius']),
                'cfmenu_manage_nav_border_radius_drop' => $mysqli->real_escape_string($post_data['cfmenu_manage_nav_border_radius_drop']),
                'cfmenu_manage_nav_hoverbackcolor' => $mysqli->real_escape_string($post_data['cfmenu_manage_nav_hoverbackcolor']),
                'cfmenu_navbar_slogan_drop' => $mysqli->real_escape_string($post_data['cfmenu_navbar_slogan_drop']),
                'cfmenu_manage_slogan_text' => $mysqli->real_escape_string($post_data['cfmenu_manage_slogan_text']),
                'cfmenu_manage_slogan_color' => $mysqli->real_escape_string($post_data['cfmenu_manage_slogan_color']),
                'cfmenu_manage_slogan_font_size' => $mysqli->real_escape_string($post_data['cfmenu_manage_slogan_font_size']),
                'cfmenu_customCSS' => $mysqli->real_escape_string($post_data['cfmenu_customCSS']),
                'cfmenu_manage_html_icon' => $mysqli->real_escape_string(htmlspecialchars($post_data['cfmenu_manage_html_icon'])),
                'cfmenu_manage_icon_color' => $mysqli->real_escape_string($post_data['cfmenu_manage_icon_color']),
                'cfmenu_manage_icon_back' => $mysqli->real_escape_string($post_data['cfmenu_manage_icon_back']),
                'cfmenu_manage_icon_padding' => $mysqli->real_escape_string($post_data['cfmenu_manage_icon_padding']),
                'cfmenu_manage_nav_items_font_size' => $mysqli->real_escape_string($post_data['cfmenu_manage_nav_items_font_size']),
            ));

            $extra_buttons = json_encode(array(
                'cfmenu_navbar_search_drop' => $mysqli->real_escape_string(isset($post_data['cfmenu_navbar_search_drop']) ? $post_data['cfmenu_navbar_search_drop'] : 0),
                'cfmenu_manage_search_icon' => $mysqli->real_escape_string(isset($post_data['cfmenu_manage_search_icon']) ? $post_data['cfmenu_manage_search_icon'] : ''),
                'cfmenu_manage_search_text' => $mysqli->real_escape_string(isset($post_data['cfmenu_manage_search_text']) ? $post_data['cfmenu_manage_search_text'] : ''),
                'cfmenu_navbar_cart_drop' => $mysqli->real_escape_string(isset($post_data['cfmenu_navbar_cart_drop']) ? $post_data['cfmenu_navbar_cart_drop'] : 0),
                'cfmenu_manage_cart_icon' => $mysqli->real_escape_string(isset($post_data['cfmenu_manage_cart_icon']) ? $post_data['cfmenu_manage_cart_icon'] : ''),
                'cfmenu_manage_cart_text' => $mysqli->real_escape_string(isset($post_data['cfmenu_manage_cart_text']) ? $post_data['cfmenu_manage_cart_text'] : ''),
                'cfmenu_navbar_admin_drop' => $mysqli->real_escape_string(isset($post_data['cfmenu_navbar_admin_drop']) ? $post_data['cfmenu_navbar_admin_drop'] : 0),
                'cfmenu_manage_admin_icon' => $mysqli->real_escape_string(isset($post_data['cfmenu_manage_admin_icon']) ? $post_data['cfmenu_manage_admin_icon'] : ''),
                'cfmenu_manage_admin_text' => $mysqli->real_escape_string(isset($post_data['cfmenu_manage_admin_text']) ? $post_data['cfmenu_manage_admin_text'] : ''),
            ));

            $choose_theme = $post_data['cfmenu_choose_theme'];

            if ($cfmenu_update_insert == 'create') {
                $user_id=$_SESSION['user' . get_option('site_token')];
                $sql_text="INSERT INTO `" . $table . "` 
                (`form_name`,
                 `form_desc`,
                  `custom_url`, 
                  `dropndown`, 
                  `logo_details`, 
                  `manage_styles`, 
                  `extra_buttons`, 
                  `choose_theme`, 
                  `created_at`, 
                  `updated_at`,
                  `user_id`
                  ) VALUES (
                  '" . $form_name . "','" . $form_desc . "','" . $custom_url . "', '" . $stringifyData . "','" . $logo_details . "','" . $manage_styles . "', '" . $extra_buttons . "', '" . $choose_theme . "', now(), 
                  now(),
                  '".$user_id."'
                  )";
                $sql_status = ($mysqli->query($sql_text)) ? 1 : 0;
                $insert_id = $mysqli->insert_id;
            } else {
                $sql_status = ($mysqli->query("UPDATE `" . $table . "` set `form_name`='" . $form_name . "',`form_desc`='" . $form_desc . "', `custom_url`='" . $custom_url . "', `dropndown`='" . $stringifyData . "', `logo_details`='" . $logo_details . "', `manage_styles`='" . $manage_styles . "', `extra_buttons`='" . $extra_buttons . "', `choose_theme`='" . $choose_theme . "', `updated_at`=now() where `id`='" . $id . "'")) ? 1 : 0;
                $insert_id = $id;
            }

            if ($sql_status) $msg = "Form saved successfully.";
            else $msg = "Something went wrong! Please try again";

            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status,
                'location' => $cfmenu_ajax_insertUrl . (isset($insert_id) ? $insert_id : 0),
                'action' => $cfmenu_update_insert
            ));
        }

        function deleteFunction($data)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . "cfmenu";
            $form_id = $data['cfmenu_form_id'];
            $sql_status = ($mysqli->query("DELETE FROM `" . $table . "` WHERE `id`=" . $form_id)) ? 1 : 0;
            if ($sql_status) $msg = "Form deleted successfully";
            else $msg = "Something went wrong! Please try again.";
            echo json_encode(array(
                'msg' => $msg,
                'status' => $sql_status
            ));
        }

        function exatractNavChildren($data, $i)
        {
            if (isset($data[$i])) {
                echo '<li class="' . $data[$i]['class'] . '">
                <div id="alink_' . -1 * (int) filter_var($data[$i]['class'], FILTER_SANITIZE_NUMBER_INT) . '" class="mb-2" a-icon="' . $data[$i]['icon'] . '" a-source="' . $data[$i]['url'] . '" direct_id="' . $data[$i]['direct_id'] . '" a-extraIds="' . $data[$i]['a-extraIds'] . '">' . $data[$i]['name'] . '</div>
                ';
                if ((isset($data[$i]['children']))) {
                    for ($k = 0; $k < count($data[$i]); $k++) {
                        echo '<ul class="sortable_container"><li class="' . $data[$i]['class'] . '">';
                        $this->exatractNavChildren($data[$i]['children'], $k);
                        echo '</ul>';
                    }
                }
                echo '</li>';
            }
        }

        function getFunnelsAjaxRequest($post_data, $pData = array())
        {
            global $app_variant;
            if (!empty($pData)) {
                $check_id = explode('_', $post_data['selectValue'])[1];
                $check_value = explode('_', $post_data['selectValue'])[0];
                if ($check_value == 'products' || $check_value == 'collections' || $check_value == "pages") {
?><label class="col-form-label mt-3 mb-1"><?php w('Select your'); ?> </label>
                    <select id="cfmenu_show_funnels_data_id" onchange="cfmenu_show_funnels_data(this.value)">
                        <option <?= ($post_data['selectValue'] == "pages_$check_id") ? 'selected' : null ?> value="pages_<?= $check_id ?>"><?php w('Page'); ?></option>
                        <option <?= ($post_data['selectValue'] == "collections_$check_id") ? 'selected' : null ?> value="collections_<?= $check_id ?>"><?php w('Collection'); ?></option>
                        <option <?= ($post_data['selectValue'] == "products_$check_id") ? 'selected' : null ?> value="products_<?= $check_id ?>"><?php w('Product'); ?></option>
                    </select>
                    <?php
                    if ($check_value == 'products') {
                        foreach ($pData as $key => $value) { ?>
                            <div class="form-check">
                                <label class="form-check-label" for="get_funnel_page_<?= $key ?>">
                                    <input type="checkbox" id="get_funnel_page_<?= $key ?>" direct_id="<?= $value['productid'] ?>" onclick="getNavDetails(`get_funnel_page_<?= $value['def_product_page'] ?>`, `<?= $value['productid'] ?>`, `get_funnel_page_<?= $key ?>`, true, `<?= $value['title'] ?>`)" class="form-check-input" name="get_funnel_page" extraids="product_<?= $value['productid'] ?>" value="<?= $value['title'] ?>">
                                    <?= ucwords(str_replace(array('-'), ' ', $value['title'])) ?>
                                </label>
                            </div>
                        <?php }
                    } elseif ($check_value == 'collections') {
                        foreach ($pData as $key => $value) { ?>
                            <div class="form-check">
                                <label class="form-check-label" for="get_funnel_page_<?= $key ?>">
                                    <input type="checkbox" id="get_funnel_page_<?= $key ?>" direct_id="<?= $value['id'] ?>" onclick="getNavDetails(`get_funnel_page_<?= $value['page_id'] ?>`, `<?= $value['title'] ?>`, `get_funnel_page_<?= $key ?>`)" class="form-check-input" extraids="collection_<?= $value['id'] ?>" name="get_funnel_page" value="<?= $value['title'] ?>">
                                    <?= ucwords(str_replace(array('-'), ' ', $value['title'])) ?>
                                </label>
                            </div>
                        <?php }
                    } else {
                        foreach ($pData as $key => $value) {
                            if (isset($value['page_id'])) $page_id = $value['page_id'];
                            else $page_id = $value['id'];
                        ?>
                            <div class="form-check">
                                <label class="form-check-label" for="get_funnel_page_<?= $page_id ?>">
                                    <input type="checkbox" id="get_funnel_page_<?= $page_id ?>" direct_id="<?= $value['id'] ?>" onclick="getNavDetails(this.id)" class="form-check-input" extraids="collection_<?= $value['id'] ?>" name="get_funnel_page" value="<?= $value['file_name'] ?>">
                                    <?= ucwords(str_replace(array('-'), ' ', $value['file_name'])) ?>
                                </label>
                            </div>
                <?php }
                    }
                }
            } else {
                $radio_val = (isset($post_data['radioId'])) ? $post_data['radioId'] : $post_data['selectValue'];
                $radioId = (int) filter_var($radio_val, FILTER_SANITIZE_NUMBER_INT);
                ?>
                <label class="col-form-label mt-3 mb-1"><?php w('Select your'); ?> </label>
                <select id="cfmenu_show_funnels_data_id" onchange="cfmenu_show_funnels_data(this.value)">
                    <option value="pages_<?= $radioId ?>"><?php w('Page'); ?></option>
                    <?php if ($app_variant == 'shopfunnels') { ?>
                        <option value="collections_<?= $radioId ?>"><?php w('Collection'); ?></option>
                        <option value="products_<?= $radioId ?>"><?php w('Product'); ?></option>
                    <?php } ?>
                </select>
                <?php
                $get_page_arr = get_funnel_pages($radioId);
                foreach ($get_page_arr as $key => $value) {
                    if (isset($value['page_id'])) $page_id = $value['page_id'];
                    else $page_id = $value['id']; ?>
                    <div class="form-check">
                        <label class="form-check-label" for="get_funnel_page_<?= $page_id ?>">
                            <input type="checkbox" id="get_funnel_page_<?= $page_id ?>" onclick="getNavDetails(this.id)" direct_id="<?= $value['id'] ?>" extraids="page_<?= $value['id'] ?>" class="form-check-input" name="get_funnel_page" value="<?= $value['file_name'] ?>">
                            <?= ucwords(str_replace(array('-'), ' ', $value['file_name'])) ?>
                        </label>
                    </div>
            <?php }
            } ?>
            <?php
            return;
        }

        function getPagesNavDetails($post_data)
        {
            $radioId = explode('get_funnel_page_', $post_data['radioId'])[1];
            if ($radioId != "" && $radioId != 0) {
                $get_page_url = str_replace('@@qfnl_install_url@@', get_option('install_url'), get_page_by_id($radioId)['url']);
                if (strpos($post_data['getDropVal'], "products") !== false) {
                    echo $get_page_url . '/?product=' . strtolower(strtolower($post_data['title']));
                    return;
                } else if (strpos($post_data['getDropVal'], "collections") !== false) {
                    echo $get_page_url . '/?sf_collection_name=' . strtolower(str_replace(' ', '-', strtolower($post_data['title'])));
                    return;
                } else {
                    echo $get_page_url;
                    return;
                }
            } else {
                $get_page_url = '#';
                echo $get_page_url;
                return;
            }
        }

        function getFunnelsData($post_data)
        {
            global $mysqli;
            global $dbpref;
            $select_value = explode('_', $mysqli->real_escape_string($post_data['selectValue']));

            if ($select_value[0] == 'funnel' || $select_value[0] == "store") {
                $get_all_funnels = get_funnels();
                  foreach ($get_all_funnels as $key => $value) { ?>
                    <div class="form-check">
                        <label class="form-check-label" for="get_funnel_id_<?= $value['funnel_id'] ?>">
                            <input type="radio" id="get_funnel_id_<?= $value['funnel_id'] ?>" class="form-check-input" onclick="funnelpostData(this.id)" name="get_funnel_option" value="<?= $value['name'] ?>">
                            <?= $value['name'] ?>
                        </label>
                    </div>
            <?php }
            } elseif ($select_value[0] == 'collections') {
                $get_all_funnels = self::getShopData($dbpref . 'product_collections', 'id', 'title', 'page_id', "WHERE `funnel_id`=$select_value[1]");
                self::getFunnelsAjaxRequest($post_data, $get_all_funnels);
            } elseif ($select_value[0] == 'products') {
                $get_all_funnels = self::getShopData($dbpref . 'all_products', 'title', 'productid', 'def_product_page', "WHERE `funnelid`=$select_value[1] AND `parent_product` = '0'");
                self::getFunnelsAjaxRequest($post_data, $get_all_funnels);
            } elseif ($select_value[0] == 'pages') {
                $get_all_funnels = get_funnel_pages($select_value[1]);
                self::getFunnelsAjaxRequest($post_data, $get_all_funnels);
            }
        }

        function getShopData($table, $id = 'id', $title = 'title', $opt = 'page_id', $where = null)
        {
            global $mysqli;
            $qry = $mysqli->query("SELECT `" . $id . "`, `" . $title . "`, `" . $opt . "` FROM `" . $table . "` " . $where . " ORDER BY `id` DESC");
            $arr = [];
            if ($qry->num_rows > 0) {
                while ($data = $qry->fetch_assoc()) {
                    $arr[] = $data;
                }
            }
            return $arr;
        }

        function getFormUI($id = null, $config_version = 0)
        {
            $form_data = self::getFormSetup($id);
            if ($form_data) {
                echo "
                <!-- CF Menu starts here -->";
                require plugin_dir_path(dirname(__FILE__, 1)) . "/view/navbarForm.php";
                echo "
                <!-- CF Menu ends here -->
                ";
            } else {
                echo "";
            }
        }

        function loadGlobalForms($config_version = 0)
        {
            global $mysqli;
            global $dbpref;
            $form_extra_settings = $dbpref . 'cfmenu';
            $qry = $mysqli->query("SELECT * FROM $form_extra_settings WHERE `settings`!='[]'");
            while ($r = mysqli_fetch_object($qry)) {
                $json_data = json_decode($r->settings, true);
                if (!empty($json_data)) {
                    $base_url = explode('?', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]")[0];
                    $extracted_array = self::getExtractedArrayOfUrl($json_data);
                    $json_data = self::getExtractedArrayOfUrl($json_data, true);
                    if (in_array($base_url, $json_data) || in_array($base_url, $extracted_array)) {
                        self::getFormUI($r->id, $config_version);
                    }
                }
            }
        }

        function getExtractedArrayOfUrl($json_data, $json = false)
        {
            $extracted_array = array();
            foreach ($json_data as $id => $value) {
                if ($json) {
                    $extracted_array[] = $value . '/';
                } else {
                    $a = explode('://', $value);
                    $b = str_replace('//', '/', $a[1]);
                    $extracted_array[] = $a[0] . '://' . $b . '/';
                }
            }
            return $extracted_array;
        }

        function getFormSetup($form_id = null)
        {
            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'cfmenu';
            $form_id = trim($mysqli->real_escape_string($form_id));

            $r = $mysqli->query("SELECT * FROM `" . $table . "` WHERE `id`=" . $form_id);

            if ($r->num_rows > 0) {
                $data = $r->fetch_assoc();
                return $data;
            }
            return 0;
        }
        function getCollections($id)
        {

            global $mysqli;
            global $dbpref;
            $table = $dbpref . 'product_collections';
            $run  = $mysqli->query("SELECT `title`,`search_title`,`page_id`,`media` FROM `$table` WHERE `id`=$id");

            if ($run->num_rows > 0) {
                $collection = $run->fetch_assoc();
                $page_data = get_page_by_id($collection['page_id']);
                $page_url = str_replace('@@qfnl_install_url@@', get_option('install_url'), $page_data['url']);
                $page_name = $collection['search_title'];
                $page_url = $page_url . "?sf_collection_name=" . strtolower($page_name);
                $collection['curl'] = $page_url;
                return $collection;
            } else {
                return false;
            }
        }
        function setCSSToShortcode($logo_details, $manage_styles, $cfmenu_nav_id)
        {
            ?>
            <style>
                <?php
                if (strlen($manage_styles['cfmenu_customCSS'])) {
                    $css_data = $manage_styles['cfmenu_customCSS'];
                    $css_data = str_replace('.this-form', '.cfmenu_main', $css_data);
                    echo $css_data;
                }
                ?>
            </style> <?php
                    }

                    function setNavbarLogo($logo_details, $form_desc, $is_slogan, $extra_details=array())
                    {
                        if ($logo_details['cfmenu_logo_type'] == 1) { ?>
                <div class="navbar-header">
                    <div class="d-flex flex-row cfmenu_navbar_brand">
                        <button class="navbar-toggler mr-2" type="button" onclick="showRelative(`<?= $extra_details['cfmenu_nav_id'] ?>`)" data-toggle="collapse" data-parent="#cfmenu_nav<?= $extra_details['cfmenu_nav_id'] ?>" data-target="#cfmenuNavbarSupportedContent<?= $extra_details['cfmenu_nav_id'] ?>" aria-controls="cfmenuNavbarSupportedContent<?= $extra_details['cfmenu_nav_id'] ?>" aria-expanded="false" aria-label="Toggle navigation">
                            <?php if ($extra_details['manage_styles'] != "") {
                                echo htmlspecialchars_decode($extra_details['manage_styles']);
                            } else echo '<i class="fas fa-bars"></i>';
                            ?>
                        </button>
                        <a class="text-center navbar-brand align-items-center h-100" href="<?= $logo_details['cfmenu_logo_click_url'] ?>"><span><?= $logo_details['cfmenu_logo_title'] ?></span><br>
                            <?php if ($is_slogan) { ?> <h2 class="tagline navbar-text" style="margin: 0px; letter-spacing: 3px;"><?= $form_desc ?></h2> <?php } ?>
                        </a>
                    </div>
                </div>
            <?php
                        } elseif ($logo_details['cfmenu_logo_type'] == 2) { ?>
                <div class="navbar-header">
                    <div class="d-flex flex-column cfmenu_navbar_brand">
                        <a class="text-center navbar-brand justify-content-center mx-auto align-items-center h-100" href="<?= $logo_details['cfmenu_logo_click_url'] ?>"><img src="<?= $logo_details['cfmenu_logo_img_source'] ?>" class="" alt=""><br>
                            <?php if ($is_slogan) { ?> <h2 class="tagline navbar-text" style="margin: 0px; letter-spacing: 3px;"><?= $form_desc ?></h2> <?php } ?>
                        </a>
                    </div>
                </div>
<?php
                        }
                    }

                    function extractShortcodeNavChildren($data, $manage_styles, $theme_id)
                    {
                        $call_function = $theme_id . '_extract_function';
                        return $this->$call_function($data, $manage_styles);
                    }

                    function theme5_extract_function($data, $manage_styles, $is_sub = FALSE)
                    {
                        $menu = $is_sub ? '<div class="dropdown-menu">' : '<ul class="navbar-nav ml-auto nav-fill">';

                        foreach ($data as $id => $attrs) {
                            $sub = isset($attrs['children']) ? $this->theme5_extract_function($attrs['children'], $manage_styles, TRUE) : null;
                            $a_attrs = $sub ? 'class="nav-link dropdown-toggle" data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false"' : 'href="' . $attrs['url'] . '"';
                            $caret = $sub ? '<span class = "drop-icon"><i class = "fas fa-chevron-down"></i></span>' : null;
                            $a_attrs = $is_sub ? 'class="dropdown-item" href="' . $attrs['url'] . '"' : $a_attrs;
                            $extra_attrs = $is_sub ? '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="servicesDropdown"><div class="d-md-flex align-items-start justify-content-start">' : null;

                            $menu .= '<li class"nav-item pl-4 pl-md-0 ml-0 ml-md-4">';
                            $menu .= $attrs['icon'] . "&nbsp;<a $a_attrs>" . $attrs['icon'] . ' ' . $attrs['name'] . "</a>";
                            // $menu .= $extra_attrs;
                            $menu .= $sub;
                            $menu .= '</li>';
                        }

                        return $menu .= $is_sub ? "</div>" : "</ul>";
                    }

                    function theme4_extract_function($data, $manage_styles, $is_sub = FALSE)
                    {
                        $ul_attrs = $is_sub ? null : ' class="links"';
                        $menu = "<ul $ul_attrs>";

                        foreach ($data as $id => $attrs) {
                            $sub = isset($attrs['children']) ? $this->theme4_extract_function($attrs['children'], $manage_styles, TRUE) : null;
                            $rand = rand(0, 500);
                            $a_attrs = $sub ? 'href="javascript:void(0)" class="desktop-link"' : 'href="' . $attrs['url'] . '"';
                            $caret = $sub ? '&nbsp;&nbsp;<i class="fa fa-caret-down"></i>' : null;
                            $extra_attrs = $sub ? '<input type="checkbox" class="show-features" id="show-' . $rand . '"><label for="show-' . $rand . '">' . $attrs['name'] . $caret . '</label>' : null;

                            $menu .= '<li>';
                            $menu .= "<a $a_attrs>" . $attrs['icon'] . ' ' . $attrs['name'] . $caret . "</a>";
                            $menu .= $extra_attrs;
                            $menu .= $sub;
                            $menu .= '</li>';
                        }

                        return $menu .= "</ul>";
                    }

                    function theme3_extract_function($data, $manage_styles, $is_sub = FALSE, $i = 0)
                    {
                        if ($is_sub) {
                            ++$i;
                            $div_attrs = ($i > 1) ? 'class="dropdown second"' : 'class="dropdown"';
                        } else {
                            $div_attrs = 'class="nav-links"';
                            $i = 0;
                        }
                        $menu = "<div " . $div_attrs . "><ul>";

                        foreach ($data as $id => $attrs) {
                            $sub = isset($attrs['children']) ? $this->theme3_extract_function($attrs['children'], $manage_styles, TRUE, $i) : null;
                            $li_attrs = $is_sub ? 'class="dropdown-link"' : 'class="nav-link" style="--i: .85s;"';
                            $caret = $sub ? '<i class="fas fa-caret-down"></i>' : null;
                            $a_attrs = $sub ? 'href="javascript:void(0)"' : 'href="' . $attrs['url'] . '"';
                            $menu .= "<li $li_attrs>";
                            $menu .= "<a $a_attrs>" . $attrs['icon'] . ' ' . $attrs['name'] . $caret . "</a>";
                            if ($sub) $menu .= $sub;
                            $menu .= "</li>";
                            if ($i > 1) $menu .= '<div class="arrow"></div>';
                        }

                        return $menu .= "</ul></div>";
                    }

                    function theme2_extract_function($data, $manage_styles, $is_sub = FALSE)
                    {
                        $ul_attrs = $is_sub ? 'class="dropdown-menu" style="width: 256px;"' : 'class="navbar-nav" style="width: 44%;" id="cfmenu_theme2-desktop"';
                        if ($is_sub && count($data) > 5) $ul_attrs = 'class="dropdown-menu"';
                        $menu = "<ul $ul_attrs>";
                        $li_attrs = $is_sub ? '' : 'nav-item';

                        if (count($data) > 5) {
                            $menu .= '<div class="no-gutters row">';
                            $i = 0;
                        }
                        foreach ($data as $id => $attrs) {
                            $sub = isset($attrs['children']) ? $this->theme2_extract_function($attrs['children'], $manage_styles, TRUE) : null;
                            if (count($data) > 5) {
                                ++$i;
                                if ($i == 1) $menu .= '<div class="col-md-4">';
                                $menu .= '<li><a class="dropdown-item" href="' . $attrs['url'] . '">' . $attrs['icon'] . ' ' . $attrs['name'] . '</a></li>';
                                if ($i > 4) {
                                    $menu .= '</div>';
                                    $i = 0;
                                }
                            } else {
                                $a_attrs = $sub ? 'class="dropdown-toggle nav-link" data-toggle="dropdown"' : 'class="dropdown-item"';
                                if (count($data[$id]) == 1) $a_attrs = 'class="nav-item"';
                                $carat = $sub ? '<span class="caret"></span>' : null;
                                $a_url = $sub ? 'javascript:void(0)' : $attrs['url'];
                                $li_inner = $sub ? ' position-relative' : null;
                                // echo count($data).'<br>'.count($attrs).'<br>';
                                if (count($attrs) > 4 && $li_inner == ' position-relative') $li_inner = null;
                                // if(count($data)>5 && $a_attrs=='class="dropdown-toggle nav-link" data-toggle="dropdown"') $li_inner=null;

                                if ($li_inner == null && $a_attrs == 'class="dropdown-item"') $a_attrs = 'class="nav-link"';
                                $menu .= '<li class="' . $li_attrs . $li_inner . '">';
                                $menu .= "<a $a_attrs href='" . $a_url . "' $a_attrs>${attrs['icon']} ${attrs['name']}</a>$sub";
                                $menu .= "</li>";
                            }
                        }

                        if (count($data) > 5 && $sub == null) $menu .= '</div>';
                        return $menu .= "</ul>";
                    }

                    function theme2_extract_mobile($data, $is_sub = FALSE)
                    {
                        $ul_attrs = $is_sub ? 'class="menu-child"' : 'class="menu"';
                        $menu = "<ul $ul_attrs>";

                        foreach ($data as $id => $attrs) {
                            $sub = isset($attrs['children']) ? self::theme2_extract_mobile($attrs['children'], TRUE) : null;
                            $li_attrs = $sub ? 'class="menu-item cfmenu_theme2-collapsible"' : 'class="menu-item"';
                            $a_attrs = $sub ? 'class="d-flex justify-content-between"' : 'class="menu-link"';
                            $a_src = $sub ? 'javascript:void(0)' : $attrs['url'];

                            $menu .= '<li ' . $li_attrs . '>';
                            $menu .= '<a href="' . $a_src . '" ' . $a_attrs . '>';
                            if ($sub) {
                                $menu .= '<span>' . $attrs['icon'] . '&nbsp;' . $attrs['name'] . ' </span> <i class="fas fa-angle-down"></i>';
                                $menu .= $sub;
                            } else {
                                $menu .= $attrs['name'];
                            }
                            $menu .= "</a>";
                            $menu .= '</li>';
                        }

                        $menu .= '</ul>';
                        return $menu;
                    }

                    function theme1_extract_function($data, $manage_styles, $is_sub = FALSE)
                    {
                        $ul_attrs = $is_sub ? '' : 'class="nav navbar-nav"';
                        $menu = "<ul $ul_attrs>";

                        foreach ($data as $id => $attrs) {
                            $sub = isset($attrs['children']) ? self::theme1_extract_function($attrs['children'], $manage_styles, TRUE) : null;
                            $url = $sub ? 'javascript:void(0);' : $attrs['url'];
                            $carat = $sub ? '<i class="fas fa-caret-down"></i>' : null;
                            $icon = $attrs['icon'];
                            $menu .= "<li>";
                            $menu .= "<a href='$url'>${icon}&nbsp;${attrs['name']}$carat</a>$sub";
                            $menu .= "</li>";
                        }
                        return $menu . "</ul>";
                    }
                }
            }

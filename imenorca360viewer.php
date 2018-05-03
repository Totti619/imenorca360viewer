<?php
if (!defined('_PS_VERSION_'))
    exit;

require_once('config/database.php');

class Imenorca360Viewer extends Module
{
    /**
     * Imenorca360Viewer constructor.
     */
    public function __construct()
    {
        $this->name = 'imenorca360viewer';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Antonio Ortiz';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('iMenorca 360 Viewer');
        $this->description = $this->l('A module that displays any 360º image on your website.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('IMNK360V_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    /**
     * @return bool
     */
    public function install() // TODO expand install method.
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (parent::install() &&
            $this->registerHooks() &&
            $this->updateConfiguration()
        ) {
            $res = $this->createTables();
            $res &= $this->insertSamples();
            return (bool)$res;
        }

        return false;
    }

    protected function registerHooks()
    {
        return
            $this->registerHook('home') &&
            $this->registerHook('header');
    }

    protected function updateConfiguration()
    {
        return
            Configuration::updateValue('IMNK360V_NAME', 'iMenorca 360 Viewer') &&
            Configuration::updateValue('IMNK360V_IMAGE_HEIGHT', 475) &&
            Configuration::updateValue('IMNK360V_ANIM_SPEED', 1) &&

            /* add form values */
            Configuration::updateValue('image_uri', '');
    }

    protected function createTables()
    {
        $db = Db::getInstance();

        return (bool)$db->execute('
            CREATE TABLE IF NOT EXISTS `' . IMNK_DB_PREFIXED_TABLE_NAME . '` (
                `id_' . IMNK_DB_TABLE_NAME . '` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `image_uri` varchar(255) NOT NULL,
                PRIMARY KEY (`id_' . IMNK_DB_TABLE_NAME . '`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=UTF8;
        ');
    }

    protected function insertSamples()
    {
        $samples_url = Tools::getHttpHost(true) . __PS_BASE_URI__ . '/modules/imenorca360viewer/img/samples/';
        $return = true;
        $db = Db::getInstance();

        $img_urls = array(
            '1',
            '2',
            '3',
        );

        foreach ($img_urls AS $url) {
            $return &= (bool)$db->execute('
                    INSERT INTO ' . IMNK_DB_PREFIXED_TABLE_NAME . ' (id_' . IMNK_DB_TABLE_NAME . ', image_uri)
                    VALUES(NULL, \'' . $samples_url . $url . '.jpg\');
                ');
        }

        return $return;
    }

    /**
     * @return bool
     */
    public function uninstall() // TODO expand uninstall method.
    {
        if (parent::uninstall() &&
            $this->deleteConfiguration([
                'IMNK360V_NAME',
                'IMNK360V_IMAGE_HEIGHT',
                'IMNK360V_ANIM_SPEED',
            ])
        ) {
            $res = $this->deleteTables();
            return (bool)$res;
        }

        return true;
    }

    protected function deleteConfiguration($configs)
    {
        $res = true;
        foreach ($configs AS $config)
            $res &= Configuration::deleteByName($config);
        return $res;
    }

    protected function deleteTables()
    {
        return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `' . IMNK_DB_PREFIXED_TABLE_NAME . '`;
		');
    }

    /**
     * Make the configuration link appear.
     * @return string
     */
    public function getContent()
    {
        $html = null;
        $errors = null;

        if (Tools::isSubmit('submit' . $this->name)) // User clicks any 'Save' button
        {
            $conf_height = strval(Tools::getValue('IMNK360V_IMAGE_HEIGHT'));
            $conf_speed = strval(Tools::getValue('IMNK360V_ANIM_SPEED'));
            $conf_image = strval(Tools::getValue('image_uri'));

            if (!$conf_height
                || empty($conf_height)
            ) {
                $errors .= $this->displayError($this->l('Invalid Configuration value (\'Maximum image height\' is empty)'));
            } elseif (
            !Validate::isUnsignedInt($conf_height)
            ) {
                $errors .= $this->displayError($this->l('Invalid Configuration value (\'Maximum image height\' is not a unsigned number)'));
            } elseif (!$conf_speed
                || empty($conf_speed)
            ) {
                $errors .= $this->displayError($this->l('Invalid Configuration value (\'Animation speed\' is empty)'));
            } elseif (
            !Validate::isUnsignedInt($conf_speed)
            ) {
                $errors .= $this->displayError($this->l('Invalid Configuration value (\'Animation speed\' is not a unsigned number)'));
            } else {
                Configuration::updateValue('IMNK360V_IMAGE_HEIGHT', $conf_height);
                Configuration::updateValue('IMNK360V_ANIM_SPEED', $conf_speed);
                $errors .= $this->displayConfirmation($this->l('Settings updated'));
            }

            $html = $this->renderMainForm();

        } elseif (Tools::isSubmit('add')) { // User clicks 'Add' button

            $html = $this->renderAddForm();

        } elseif (Tools::isSubmit('modify')) { // User clicks 'Modify' button of any image

            $html = $this->renderModifyForm();

        } elseif (Tools::isSubmit('delete')) { // User clicks 'Delete' button  of any image

            (new Image((int)Tools::getValue('delete')))->delete();
            $errors .= $this->displayConfirmation($this->l('Image deleted.'));
            $html = $this->renderMainForm();

        } elseif (Tools::isSubmit('submitImage')) { // User adds an image

            Configuration::updateValue('image_uri', $conf_image);
            $html = $this->displayConfirmation(strval(Tools::getValue('image_uri')));
            $html .= $this->renderMainForm();

        } else {
            $html = $this->renderMainForm();
        }


        return $errors . $html;
    }

    /**
     * Loads and displays the form.
     * @return mixed
     */
    public function renderMainForm()
    {
        // Get default configuration
        $default_height = Configuration::get('IMNK360V_IMAGE_HEIGHT');
        $default_speed = Configuration::get('IMNK360V_ANIM_SPEED');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Maximum image height'),
                    'name' => 'IMNK360V_IMAGE_HEIGHT',
                    'suffix' => $this->l('pixels'),
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Animation speed'),
                    'name' => 'IMNK360V_ANIM_SPEED',
                    'suffix' => $this->l('RPM'),
                    'required' => true,
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );


        $helper = $this->getHelper();

        // Load current value
        $helper->fields_value['IMNK360V_NAME'] = Configuration::get('IMNK360V_NAME');
        $helper->fields_value['IMNK360V_IMAGE_HEIGHT'] = Configuration::get('IMNK360V_IMAGE_HEIGHT');
        $helper->fields_value['IMNK360V_ANIM_SPEED'] = Configuration::get('IMNK360V_ANIM_SPEED');


        return $helper->generateForm($fields_form) . $this->renderList();
    }

    protected function getHelper()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        return $helper;
    }

    public function renderList()
    {
        $images = $this->getImages();

        $this->context->smarty->assign(array(
            'link' => $this->context->link,
            'images' => $images,
        ));

        return $this->display(__FILE__, 'list.tpl');
    }

    public function getImages()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT * FROM ' . IMNK_DB_PREFIXED_TABLE_NAME . ';
        ');
    }

    protected function renderAddForm()
    {
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Upload an image'),
                'icon' => 'icon-upload',
            ),
            'input' => array(
                array(
                    'type' => 'file',
                    'label' => $this->l('URL'),
                    'name' => 'image_uri',
                    'display_image' => true,
                    'required' => true,
                    'desc' => sprintf($this->l('Maximum image size: %s.'), ini_get('upload_max_filesize'))
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = $this->getHelper();
        $helper->submit_action = 'submitImage';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        return $helper->generateForm($fields_form);
    }

    public function moduleUrl()
    {
        return Tools::getProtocol(Tools::usingSecureMode()) . $_SERVER['HTTP_HOST'] . $this->getPathUri();
    }

    /**
     * @param $params
     * @return mixed
     */
    public function hookDisplayHome($params)
    {
        $this->context->smarty->assign(
            array(
                'images' => $this->getImages(),
                'height' => Configuration::get('IMNK360V_IMAGE_HEIGHT'),
                'script' => '
                    <script type="text/javascript">
                        initPanorama({ // Documentation Photo Sphere Viewer (more info) : http://photo-sphere-viewer.js.org/
                            height : "' . Configuration::get('IMNK360V_IMAGE_HEIGHT') . 'px"
                            ,caption : null
                            ,markers : []
                            ,min_fov : 30
                            ,max_fov : 90
                            ,default_fov : this.max_fov
                            ,fisheye : false
                            ,default_long : 0.0
                            ,default_lat : 0.0
                            ,sphere_correction : {
                                pan : 0
                                ,tilt : 0
                                ,roll : 0
                            }
                            ,time_anim : 2000
                            ,anim_speed : "' . Configuration::get('IMNK360V_ANIM_SPEED') . 'rpm"
                            ,anim_lat : this.default_lat
                            ,navbar : true
                            ,lang: {
                                autorotate: "' . $this->l('Automatic rotation') . '",
                                zoom: "' . $this->l('Zoom') . '",
                                zoomOut: "' . $this->l('Zoom out') . '",
                                zoomIn: "' . $this->l('Zoom in') . '",
                                download: "' . $this->l('Download') . '",
                                fullscreen: "' . $this->l('Fullscreen') . '",
                                markers: "' . $this->l('Markers') . '",
                                gyroscope: "' . $this->l('Gyroscope') . '"
                            }
                            ,loading_txt : "' . $this->l('Loading... ') . '"
                            ,mousewheel : true
                            ,mousemove : true
                            ,keyboard : true
                            ,gyroscope : false
                            ,size : {
                                width : "100%"
                                ,height : "' . Configuration::get('IMNK360V_IMAGE_HEIGHT') . 'px"
                            }
                            ,transition: {
                                duration: 1500 // duration of transition in milliseconds
                                ,loader: true // should display the loader ?
                            }
                            
                            /* ADVANCED OPTIONS */
                            
                            ,move_speed : 1.0
                            ,usexmpdata : true
                            ,cache_texture : 0
                            ,tooltip : {
                                offset: 5
                                ,arrow_size: 7
                                ,delay: 100
                            }
                            ,move_inertia : true
                            ,click_event_on_marker : false
                            ,mousewheel_factor : 1.0
                        });
                    </script>
                '
            )
        );
        return $this->display(__FILE__, 'imenorca360viewer.tpl');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'libraries/materialize/css/materialize.min.css', 'all');
        $this->context->controller->addCSS($this->_path . 'css/imenorca360viewer.css', 'all');

        $this->context->controller->addJS($this->_path . 'libraries/materialize/js/materialize.min.js', 'all');
        $this->context->controller->addJS($this->_path . 'libraries/Photo-Sphere-Viewer/three.min.js', 'all');
        $this->context->controller->addJS($this->_path . 'libraries/Photo-Sphere-Viewer/photo-sphere-viewer.min.js', 'all');
        $this->context->controller->addJS($this->_path . 'js/imenorca360viewer.js', 'all');
    }


}

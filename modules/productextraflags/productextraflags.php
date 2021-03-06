<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

include(dirname(__FILE__) . '/classes/ProductFlags.php');

class Productextraflags extends Module
{
    protected $table_name = 'product_extra_flag';

    public function __construct()
    {
        $this->name = 'productextraflags';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Simul Digital';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();
        $this->registerHook('actionObjectProductFlagsAddBefore');
        $this->displayName = $this->l('Product Extra Flags');
        $this->description = $this->l('Add Extra Flags On Your Product.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /*
        Create custom tab for add flags.
    */
    public function installTab($parent_class, $class_name, $name)
    {
        $tab = new Tab();
        $tab->name[$this->context->language->id] = $name;
        $tab->class_name = $class_name;
        $tab->id_parent = (int)Tab::getIdFromClassName($parent_class);
        $tab->module = $this->name;
        return $tab->add();
    }


    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        if (!file_exists(_PS_ROOT_DIR_ . '/img/thumbnail/')) {
            mkdir(_PS_ROOT_DIR_ . '/img/thumbnail/');
        }

        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayAdminProductsExtra')
            && $this->registerHook("displayAdminProductsMainStepLeftColumnBottom")
            && $this->registerHook('actionProductAdd')
            && $this->registerHook('displayHeaderCategory')
            && $this->registerHook('displayProductListReviews')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('displayAfterProductThumbs')
            && $this->registerHook('actionPresentProductListing')
            && $this->registerHook('actionObjectProductFlagsAddAfter')
            && $this->registerHook('actionObjectProductFlagsAddBefore')
            && $this->registerHook('actionObjectProductFlagsUpdateAfter')
            && $this->registerHook('actionObjectProductFlagsDeleteAfter')
            && $this->installTab('AdminCatalog', 'AdminProductFlags', 'Product Flags');
    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');
        return parent::uninstall();
    }

    public function hookDisplayAdminProductsMainStepLeftColumnBottom($params)
    {
        $id_product = $params['id_product'];

        /*
         * Fetching Previous flag data for current product
         */
        $query = new DbQuery();
        $query->select('id_flag')
            ->from('product_flags_item')
            ->where('id_product=' . $id_product);
        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $previous_flag_data = [];

        foreach ($data as $id) {
            $previous_flag_data[] = $id['id_flag'];
        }

        /*
         Fetching data of all flags.
         */
        $query = new DbQuery();
        $query->select('pef.*, pefl.name_flag')
            ->from('product_extra_flags', 'pef')
            ->leftJoin('product_extra_flags_lang', 'pefl', 'pef.id_flag = pefl.id_flag')
            ->where('pef.display_type = 0')
            ->where('id_lang = ' . $this->context->language->id)
            ->where('id_shop = ' . $this->context->shop->id);

        $flags_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        $this->context->smarty->assign(
            [
                'id_product' => $id_product,
                'flags_data' => $flags_data,
                'title' => 'Product Flags',
                'previous_flag_data' => $previous_flag_data,
                'image_dir' => _PS_ROOT_DIR_ . '/img/thumbnail/'
            ]);

        return $this->fetch('module:productextraflags/views/templates/admin/view.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        Db::getInstance()->delete('product_flags_item', 'id_product=' . $params['id_product']);

        $thmbn = Tools::getValue('flags_item');
        if ($thmbn && is_array($thmbn) && count($thmbn)) {
            $data = [];

            foreach ($thmbn as $item) {
                $row = [
                    'id_product' => $params['id_product'],
                    'id_flag' => $item
                ];
                $data[] = $row;
            }
            Db::getInstance()->insert('product_flags_item', $data);
        }
    }

    public function hookActionObjectProductFlagsAddBefore($params)
    {
        if(Tools::getValue('img_status')=='1' && Tools::getValue('selectedthumbnailimage')=='')
        {
            $this->context->controller->errors[] = $this->trans(
                'Please select Flag Image.',
                [],
                'Shop.Notifications.Error'
            );
        }

        if(Tools::getValue('display_type')=='1' && !Tools::getValue('groupBox') )
        {
            $this->context->controller->errors[] = $this->trans(
                'Please select category.',
                [],
                'Shop.Notifications.Error'
            );
        }
    }

    public function hookActionObjectProductFlagsAddAfter($params)
    {
        ProductFlags::cateShopAddUpdateDb($params['object']->id, Tools::getValue('groupBox'), Tools::getValue('checkBoxShopAsso_product_extra_flags'));
    }


    public function hookActionObjectProductFlagsUpdateAfter($params)
    {
        /*Delete related data of flag in `product_flags_category` &`product_extra_flags_shop` tables for update */
        Db::getInstance()->delete('product_flags_category', 'id_flag=' . $params['object']->id);
        Db::getInstance()->delete('product_extra_flags_shop', 'id_flag=' . $params['object']->id);

        ProductFlags::cateShopAddUpdateDb($params['object']->id, Tools::getValue('groupBox'), Tools::getValue('checkBoxShopAsso_product_extra_flags'));
    }

    public function hookActionObjectProductFlagsDeleteAfter($params)
    {
        /*Delete related data of flag*/
        Db::getInstance()->delete('product_flags_category', 'id_flag=' . $params['object']->id);
        Db::getInstance()->delete('product_extra_flags_shop', 'id_flag=' . $params['object']->id);
        Db::getInstance()->delete('product_flags_item', 'id_flag=' . $params['object']->id);
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminProductFlags'));
    }

    public function hookDisplayAfterProductThumbs($params)
    {
        $id_product = Tools::getValue('id_product');

        /*Get Flags from Product */
        $flagsId = ProductFlags::getFlagsOfProduct($id_product);


        /*Get Flags from Category */
        $id_cate = $this->context->smarty->tpl_vars['category']->value->id_category;
        $catFlagId = ProductFlags::getFlagsOfCategory($id_cate);
        $flagsId=array_merge($catFlagId,$flagsId);
        $ids = [];
        foreach ($flagsId as $id) {
            $ids[] = $id['id_flag'];
        }

        $ids = array_unique($ids);
        $flags_data = [];

        foreach ($ids as $id) {
            $query = new DbQuery();
            $query->select('pef.*, pefl.name_flag')
                ->from('product_extra_flags', 'pef')
                ->leftJoin('product_extra_flags_lang', 'pefl', 'pef.id_flag = pefl.id_flag')
                ->leftJoin('product_flags_category', 'pfc', 'pef.id_flag = pfc.id_flag')
                ->where('pef.id_flag=' . $id)
            ->where('pefl.id_lang='.$this->context->language->id);

            if (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query)) {
                $flags_data[] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
            }
        }
        $this->context->smarty->assign(
            [
                'flags_data' => $flags_data
            ]
        );
        return $this->fetch('module:productextraflags/views/templates/front/product-flag.tpl');
    }


    public function hookDisplayProductListReviews($params)
    {
        if (isset($params['product']['id_product']) || isset($params['product']['id'])) {
            if ($params['product']['id']) {
                $id_product = $params['product']['id'];
            } else {
                $id_product = $params['product']['id_product'];
            }
            /*Get Flags from Product */
            $flagsId = ProductFlags::getFlagsOfProduct($id_product);

            /*Get Flags from Category */
            $catFlagId= ProductFlags::getFlagsOfCategory($params['product']['id_category_default']);
            $flagsId=array_merge($catFlagId,$flagsId);
            $ids = [];
            foreach ($flagsId as $id) {
                $ids[] = $id['id_flag'];
            }
            $ids = array_unique($ids);

            $flags_data = [];

            foreach ($ids as $id) {
                $query = new DbQuery();
                $query->select('pef.*, pefl.name_flag')
                    ->from('product_extra_flags', 'pef')
                    ->leftJoin('product_extra_flags_lang', 'pefl', 'pef.id_flag = pefl.id_flag')
                    ->leftJoin('product_flags_category', 'pfc', 'pef.id_flag = pfc.id_flag')
                    ->where('pef.id_flag=' . $id)
                    ->where('pefl.id_lang='.$this->context->language->id);


                if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query)) {
                    $flags_data[] = $row;
                }
            }

            $this->context->smarty->assign(
                [
                    'flags_data' => $flags_data
                ]
            );

            return $this->fetch('module:productextraflags/views/templates/front/product-flag.tpl');
        }
    }

    public function hookdisplayHeaderCategory($params)
    {
        $id_cate = $this->context->smarty->tpl_vars['category']->value['id'];

        $query_cate = new DbQuery();
        $query_cate->select('id_flag')
            ->from('product_flags_category')
            ->where('id_category=' . $id_cate);

        $flagsId = Db::getInstance()->executeS($query_cate);

        $flags_data = [];

        foreach ($flagsId as $id) {
            $query = new DbQuery();
            $query->select('pef.*, pefl.name_flag')
                ->from('product_extra_flags', 'pef')
                ->leftJoin('product_extra_flags_lang', 'pefl', 'pef.id_flag = pefl.id_flag')
                ->where('pefl.id_lang = ' . $this->context->language->id)
                ->where('pefl.id_shop = ' . $this->context->shop->id)
                ->where('pef.id_flag=' . $id['id_flag'])
                ->where('pef.display_type=1');

            if ($row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query)) {
                $flags_data[] = $row;
            }

        }
        $this->context->smarty->assign(
            [
                'flags_data' => $flags_data
            ]
        );
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS(($this->_path) . 'views/css/front.css');
    }

}

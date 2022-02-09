<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Productcoverthumbnails extends Module
{


    public function __construct()
    {
        $this->name = 'productcoverthumbnails';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Simul Digital';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Product Cover Thumbnails');
        $this->description = $this->l('Add Extra Thumbnails On Your Product.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /*
        Create custom tab for add thumbnails items.
    */
    public function installTab($parent_class, $class_name, $name)  {
        $tab = new Tab();
        $tab->name[$this->context->language->id] = $name;
        $tab->class_name = $class_name;
        $tab->id_parent = (int) Tab::getIdFromClassName($parent_class);
        $tab->module = $this->name;
        return $tab->add();
    }


    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        return parent::install()
            && $this->registerHook('header')
            && $this->registerHook('displayAdminProductsExtra')
            && $this->registerHook("displayAdminProductsMainStepLeftColumnBottom")
            && $this->registerHook('actionProductAdd')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('displayAfterProductThumbs')
            && $this->installTab('AdminCatalog', 'AdminProductThumbnails', 'Product Cover Thumbnails')
            && Configuration::updateValue('Productcoverthumbnails', "productcoverthumbnails");


    }

    public function uninstall()
    {
        include(dirname(__FILE__) . '/sql/uninstall.php');
        return parent::uninstall(); // TODO: Change the autogenerated stub
    }

    public function hookDisplayAdminProductsMainStepLeftColumnBottom($params)
    {
        $id_product = $params['id_product'];

        /*
         Fetching data of all thumbnails.
         */
        $query = new DbQuery();
        $query->select('*')->from('product_cover_thumbnails');
        $thumbnails_data= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        /*
          Fetching data of already selected thumbnails.
         */
        $query_name=new DbQuery();
        $query_name->select('thumbnails_name')->from('product_thumbnails_item')->where('product_id='.$id_product);
        $thumbnails_name=Db::getInstance()->executeS($query_name);

        $this->context->smarty->assign(
            [
                'thumbnails_name' => $thumbnails_name,
                'product_id'=> $id_product,
                'thumbnails_data'=>$thumbnails_data,
                'image_dir'=> _PS_ROOT_DIR_.'/img/thumbnail/'
            ]);

        return $this->fetch('module:productcoverthumbnails/views/templates/admin/view.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        $thmbn = Tools::getValue('thumbnails_item');

        Db::getInstance()->delete('product_thumbnails_item','product_id='.$params['id_product']);

        $data=[];
        foreach ($thmbn as $item)
        {
            $query= new DbQuery();
            $query->select('*')->from('product_cover_thumbnails')->where("thumbnails_name='".$item."'");
            $thmb_data=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            
            $row = [
                'product_id'=>$params['id_product'],
                'thumbnails_id'=>$thmb_data[0]['thumbnails_id']
            ];
            $data[] = $row;
        }

        Db::getInstance()->insert('product_thumbnails_item',$data);
        exit();
    }

    public function hookDisplayAfterProductThumbs($params)
    {
        $id_product =Tools::getValue('id_product');

        $query = new DbQuery();
        $query->select(_DB_PREFIX_.'product_cover_thumbnails.thumbnails_id, '._DB_PREFIX_.'product_cover_thumbnails.thumbnails_name, '._DB_PREFIX_.'product_cover_thumbnails.type, '._DB_PREFIX_.'product_cover_thumbnails.img_status')
            ->from('product_cover_thumbnails')
            ->leftJoin('product_thumbnails_item','',_DB_PREFIX_.'product_cover_thumbnails.thumbnails_id='._DB_PREFIX_.'product_thumbnails_item.thumbnails_id')
            ->where(_DB_PREFIX_.'product_thumbnails_item.product_id='.$id_product);

        $thumbnails_data= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $this->context->smarty->assign(
            [
                'thumbnails_data' => $thumbnails_data
            ]
        );

        return $this->fetch('module:productcoverthumbnails/views/templates/front/product-flag.tpl');
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS(($this->_path) . 'views/css/front.css');
    }
}

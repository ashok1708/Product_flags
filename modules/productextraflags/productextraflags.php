<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */


if (!defined('_PS_VERSION_')) {
    exit;
}

class Productextraflags extends Module
{
    protected $table_name='product_extra_flag';

    public function __construct()
    {
        $this->name = 'productextraflags';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Simul Digital';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Product Extra Flags');
        $this->description = $this->l('Add Extra Flags On Your Product.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /*
        Create custom tab for add flags.
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
         Fetching data of all flags.
         */
        $query = new DbQuery();
        $query->select('*')->from('product_extra_flags');
        $flags_data= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        $this->context->smarty->assign(
            [
                'id_product'=> $id_product,
                'flags_data'=>$flags_data,
                'title'=> 'Product Flags',
                'image_dir'=> _PS_ROOT_DIR_.'/img/thumbnail/'
            ]);

        return $this->fetch('module:productextraflags/views/templates/admin/view.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        $thmbn = Tools::getValue('flags_item');
        dump($thmbn);
        Db::getInstance()->delete('product_flags_item','id_product='.$params['id_product']);

        $data=[];
        foreach ($thmbn as $item)
        {
            $query= new DbQuery();
            $query->select('*')->from('product_extra_flags')->where("name_flag='".$item."'");
            $flag_data=Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            
            $row = [
                'id_product'=>$params['id_product'],
                'id_flag'=>$flag_data[0]['id_flag']
            ];
            $data[] = $row;
        }

        Db::getInstance()->insert('product_flags_item',$data);
        exit();
    }

    public function getContent()
    {
        $this->context->controller->addJqueryUI('ui.datepicker');
    }

    public function hookDisplayAfterProductThumbs($params)
    {
        $id_product =Tools::getValue('id_product');
        $categories_id= Product::getProductCategoriesFull($id_product);
        foreach ($categories_id as $id)
        {
            $query_cate = new DbQuery();
            $query_cate->select('id_flag')
                ->from('product_flags_category')
                ->where('id_category=' . $id['id_category']);
            $flagsId = Db::getInstance()->executeS($query_cate);
            dump($id['id_category']);
        }

        foreach ($flagsId as $id)
        {
            $query = new DbQuery();
            $query->select('*')
                ->from('product_extra_flags')
                ->leftJoin('product_flags_item','',_DB_PREFIX_.'product_extra_flags.id_flag='._DB_PREFIX_.'product_flags_item.id_flag')
                ->where(_DB_PREFIX_.'product_flags_item.id_product='.$id_product.' AND '._DB_PREFIX_.'product_flags_item.id_flag='.$id['id_flag']);

           if($row= Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query))
           {
               $flags_data[] =$row;
           }
        }

        $this->context->smarty->assign(
            [

                'flags_data' => $flags_data
            ]
        );

        return $this->fetch('module:productextraflags/views/templates/front/product-flag.tpl');
    }

    public function hookHeader()
    {
        $this->context->controller->addCSS(($this->_path) . 'views/css/front.css');
    }


}

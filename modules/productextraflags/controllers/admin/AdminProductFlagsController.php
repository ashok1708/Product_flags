<?php

class AdminProductFlagsController extends ModuleAdminController
{
    protected $position_identifier = 'id_flag';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product_extra_flags';
        $this->list_id = 'product_extra_flags';
        $this->className = 'ProductFlags';
        $this->lang = true;
        $this->identifier = 'id_flag';
        $this->_defaultOrderBy = 'position';
        parent::__construct();

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
        $this->_select = ' a.type ';
        $this->fields_list = array(
            'id_flag' => array(
                'title' => $this->trans('Flag Id'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name_flag' => array(
                'title' => $this->trans('Flag Name'),
                'filter_key' => 'b!title',
                'align' => 'center',
            ),
        );
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );


        $this->fieldImageSettings = array('name' => 'texture', 'dir' => 'thumbnail');

        $this->image_dir = 'thumbnail';
    }
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->context->smarty->clearAssign('help_link');
        $this->page_header_toolbar_btn['help_link'] = array(
            'href' => _MODULE_DIR_  .$this->module->name.  '/user_guide.pdf',
            'desc' => $this->trans('Help', [], false),
            'icon' => 'process-icon-help'
        );
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
        $recursivecategories = Category::getCategories(Context::getContext()->language->id);
        $processedCategories = [];
        $this->recurseCategory($processedCategories, $recursivecategories, Category::getRootCategory()->id);
        $groups = [];
        foreach ($processedCategories as $cat) {
            if ($cat['level_depth'] > 1) {
                $groups[] = ['id_group' => $cat['id_category'], 'name' => $cat['name']];
            }
        }
        $options = array(
            array(
                'id_option' => 'top-left',
                'name' => 'Top Left'
            ),
            array(
                'id_option' => 'top-right',
                'name' => 'Top Right'
            ),
            array(
                'id_option' => 'bottom-left',
                'name' => 'Bottom Left'
            ),
            array(
                'id_option' => 'bottom-right',
                'name' => 'Bottom Right'
            ),
        );
        $options_type = array(
            array(
                'id_option' => '0',
                'name' => 'Single Product'
            ),
            array(
                'id_option' => '1',
                'name' => 'Category'
            ),

        );

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Product Flags', array(), 'Admin.Notifications.Info')
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Flag title', array(), 'Admin.Notifications.Info'),
                    'name' => 'name_flag',
                    'lang' => true,
                    'required' => true
                ),
                array(
                    'type' => 'file',
                    'label' => $this->trans('Flag Icon', array(), 'Admin.Catalog.Feature'),
                    'name' => 'selectedthumbnailimage',
                    'display_image' => true,
                    'hint' => array(
                        $this->trans('Upload an image file containing the texture from your computer.', array(), 'Admin.Catalog.Help'),

                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Show Image Only'),
                    'name' => 'img_status',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->trans('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->trans('Disabled')
                        )
                    )
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Display On'),
                    'name' => 'display_type',
                    'required' => true,
                    'options' => array(
                        'query' => $options_type,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type'=>'datetime',
                    'label'=>$this->trans('From',[],'Admin.Global'),
                    'name'=>'time_from',
                ),
                array(
                    'type'=>'datetime',
                    'label'=>$this->trans('To',[],'Admin.Global'),
                    'name'=>'time_to',
                ),
                array(
                    'type' => 'group',
                    'label' => $this->l('Categories'),
                    'name' => 'groupBox',
                    'values' => $groups,
                    'col' => '6',
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Flag Position'),
                    'desc' => $this->l('Choose a position of flag. With respect to product cover thumbnail.'),
                    'name' => 'position',
                    'required' => true,
                    'options' => array(
                        'query' => $options,
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l(' Text Color'),
                    'name' => 'text_color',
                    'desc'=>$this->l('Default Text Color : White')
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l(' Flag Background Color'),
                    'name' => 'bg_color',
                    'desc'=>$this->l('Default Background Color : Pacific Blue (#24B9D7)')
                ),
            ),
            'submit' => array(
                'name' => 'submit' . $this->className ,
                'title' => $this->trans('Save', array(), 'Admin.Notifications.Info'),
            ),

        );
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->trans('Shop association', [], 'Admin.Global'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        if(Tools::getValue('id_flag'));
        {
            $selected_cate_ids= ProductFlags::getSelectedCateId(Tools::getValue('id_flag'));
            foreach ($groups as $cat)
            {
                $this->fields_value['groupBox_' . $cat['id_group']] =  Tools::getValue('groupBox_' . $cat['id_group'], in_array($cat['id_group'], $selected_cate_ids));
            }
        }

        return parent::renderForm();
    }


    protected function uploadImage($id, $name, $dir, $ext = false, $width = null, $height = null)
    {
        if (isset($_FILES[$name]['tmp_name']) && !empty($_FILES[$name]['tmp_name'])) {
            $type = Tools::strtolower(Tools::substr(strrchr($_FILES['selectedthumbnailimage']['name'], '.'), 1));

            if (isset($_FILES['selectedthumbnailimage']) &&
                isset($_FILES['selectedthumbnailimage']['tmp_name']) &&
                !empty($_FILES['selectedthumbnailimage']['tmp_name']) &&
                in_array($type, array('png', 'svg', 'svg+xml'))
            ) {
                if ( !file_exists( $dir ) && !is_dir( $dir ) ) {
                    mkdir( $dir );
                }
                if (move_uploaded_file($_FILES['selectedthumbnailimage']['tmp_name'],_PS_ROOT_DIR_.'/img/'.$dir.$id.'.'.$type)) {
                    ProductFlags::updateIconFiletype($id, $type);
                }
                else{
                    $this->errors[] = $this->trans('Error in uploading image.', [], 'Admin.Notifications.Error');
                    return false;
                }
            }
            else{
                $this->errors[] = $this->trans('Uploaded image is not valid. Please upload PNG or SVG images only', [], 'Admin.Notifications.Error');
                return false;
            }
        }
        return true;
    }

    public static function recurseCategory(&$actualCategories, $categories, $current, $id_category = null, $id_selected = 1)
    {
        if (!$id_category) {
            $id_category = (int)Configuration::get('PS_ROOT_CATEGORY');
        }

        $actualCategories[] = ['id_category' => $id_category, 'level_depth' => $current['infos']['level_depth'], 'name' => str_repeat('&nbsp;', $current['infos']['level_depth'] * 5) . stripslashes($current['infos']['name'])];
        if (isset($categories[$id_category])) {
            foreach (array_keys($categories[$id_category]) as $key) {
                self::recurseCategory($actualCategories, $categories, $categories[$id_category][$key], $key, $id_selected);
            }
        }
    }
}

<?php

include(dirname(__FILE__) . '/../../classes/ProductFlags.php');

class AdminProductFlagsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product_extra_flags';
        $this->list_id = 'product_extra_flags';
        $this->className = 'ProductFlags';
        $this->identifier = 'id_flag';

        parent::__construct();

        $this->fields_list = array(
            'id_flag' => array(
                'title' => $this->trans('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'name_flag' => array(
                'title' => $this->trans('title'),
                'align' => 'left',
            ),
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ),
        );

        $this->fieldImageSettings = array('name' => 'selectedthumbnailimage', 'dir' => 'thumbnail');
        $this->image_dir = 'thumbnail';
    }



    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
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


        $this->fields_form = array(
            'legend' => array(
                'title' => $this->trans('Product Flags', array(), 'Admin.Notifications.Info')
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Flag title', array(), 'Admin.Notifications.Info'),
                    'name' => 'name_flag',
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
                    'label' => $this->l('Flag Position'),
                    'desc' => $this->l('Choose a position of flag'),
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
                    'name' => 'text_color'
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l(' Flag Background Color'),
                    'name' => 'bg_color'
                ),
            ),
            'submit' => array(
                'name' => 'submit' . $this->className,
                'title' => $this->trans('Save', array(), 'Admin.Notifications.Info'),
            ),
        );

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
}
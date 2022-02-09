<?php

include(dirname(__FILE__) . '/../../classes/ProductThumbnailsItems.php');

class AdminProductThumbnailsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'product_cover_thumbnails';
        $this->list_id = 'product_cover_thumbnails';
        $this->className = 'ProductThumbnailsItems';
        $this->identifier = 'thumbnails_id';

        parent::__construct();

        $this->fields_list = array(
            'thumbnails_id' => array(
                'title' => $this->trans('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ),
            'thumbnails_name' => array(
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
                'title' => $this->trans('Product Thumbnails', array(), 'Admin.Notifications.Info')
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->trans('Thumbnail title', array(), 'Admin.Notifications.Info'),
                    'name' => 'thumbnails_name',
                    'required' => true
                ),
                array(
                    'type' => 'file',
                    'label' => $this->trans('Thumbnails Icon', array(), 'Admin.Catalog.Feature'),
                    'name' => 'selectedthumbnailimage',
                    'display_image' => true,
                    'hint' => array(
                        $this->trans('Upload an image file containing the color texture from your computer.', array(), 'Admin.Catalog.Help'),
                        $this->trans('This will override the HTML color!', array(), 'Admin.Catalog.Help'),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->trans('Show Image'),
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
                    ProductThumbnailsItems::updateIconFiletype($id, $type);
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


    public function ajaxProcessUpdatePositions()
    {
        if ($this->access('edit')) {
            $way = (int)Tools::getValue('way');
            $thumbnails_id = (int)Tools::getValue('id');
            $positions = Tools::getValue('product_cover_thumbnails');

            $new_positions = array();
            foreach ($positions as $v) {
                if (!empty($v)) {
                    $new_positions[] = $v;
                }
            }

            foreach ($new_positions as $position => $value) {
                $pos = explode('_', $value);

                if (isset($pos[2]) && (int)$pos[2] === $thumbnails_id) {
                    if ($product_thumbnails_items = new ProductThumbnailsItems((int)$pos[2])) {
                        if (isset($position) && $product_thumbnails_items->updatePosition($way, $position, $thumbnails_id)) {
                            echo 'ok position ' . (int)$position . ' for shipping labels ' . (int)$pos[1] . '\r\n';
                        } else {
                            echo '{"hasError" : true, "errors" : "Can not update shipping labels ' . (int)$thumbnails_id . ' to position ' . (int)$position . ' "}';
                        }
                    } else {
                        echo '{"hasError" : true, "errors" : "This shipping labels (' . (int)$thumbnails_id . ') cannot be loaded"}';
                    }

                    break;
                }
            }
        }
    }
}

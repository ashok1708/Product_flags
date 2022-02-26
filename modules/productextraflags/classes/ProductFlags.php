<?php

class ProductFlags extends ObjectModel
{
    public $name_flag;
    public $selectedthumbnailimage;
    public $type;
    public $img_status;
    public $position;
    public $text_color;
    public $bg_color;
    public $time_from;
    public $time_to;
    public $display_type;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_extra_flags',
        'primary' => 'id_flag',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(

            /* Lang field */
            'name_flag' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true),

            'selectedthumbnailimage' => array('type' => self::TYPE_STRING),
            'type' => array('type' => self::TYPE_STRING),
            'img_status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' => array('type' => self::TYPE_STRING),
            'text_color' => array('type' => self::TYPE_STRING),
            'bg_color' => array('type' => self::TYPE_STRING),
            'time_from' => array('type' => self::TYPE_STRING),
            'time_to' => array('type' => self::TYPE_STRING),
            'display_type' => array('type' => self::TYPE_STRING)
        ),
    );

    /** @var string $image_dir */
    protected $image_dir = _PS_IMG_DIR_ .'thumbnail' ;

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        $this->image_dir = _PS_IMG_DIR_ .'thumbnail';
    }

    public function updateIconFiletype($id_flag, $type)
    {
        if(!$id_flag){
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->update(
            'product_extra_flags',
            array('type' => $type),
            'id_flag = '.$id_flag);
    }

    public function getFlagsOfProduct($id_product)
    {
        $query_flag = new DbQuery();
        $query_flag->select('id_flag')
            ->from('product_flags_item')
            ->where('id_product=' . $id_product);

        return Db::getInstance()->executeS($query_flag);
    }

    public function getFlagsOfCategory($id_category)
    {
        $query_cate = new DbQuery();
        $query_cate->select('id_flag')
            ->from('product_flags_category')
            ->where('id_category=' . $id_category);
       return Db::getInstance()->executeS($query_cate);
    }

    public function getSelectedCateId($id_flag)
    {
        $selected_id=[];
        if($id_flag)
        {
            $query=new DbQuery();
            $query->select('id_category')
                ->from('product_flags_category')
                ->where('id_flag='.$id_flag);
            $data= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);


            foreach ($data as $id)
            {
                $selected_id[]=$id['id_category'];
            }
        }
        return $selected_id;
    }

    public function cateShopAddUpdateDb($id_flag,$cate_list,$shop_list)
    {
        $dataCate=[];
        $dataShop=[];

        if($cate_list)
        {
            foreach ($cate_list as $category)
            {
                $row = [
                    'id_flag'=>$id_flag,
                    'id_category'=>$category
                ];
                $dataCate[] = $row;
            }
        }

        if($shop_list)
        {
            foreach ($shop_list as $shop)
            {
                $row = [
                    'id_flag'=>$id_flag,
                    'id_shop'=>$shop
                ];
                $dataShop[] = $row;
            }
        }
        Db::getInstance()->insert('product_flags_category',$dataCate);
        Db::getInstance()->insert('product_extra_flags_shop',$dataShop);
    }
}
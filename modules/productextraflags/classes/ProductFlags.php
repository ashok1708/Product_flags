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


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_extra_flags',
        'primary' => 'id_flag',
        'multilang' => false,
        'fields' => array(
            'name_flag' => array('type' => self::TYPE_STRING,  'validate' => 'isGenericName', 'required' => true),
            'selectedthumbnailimage' => array('type' => self::TYPE_STRING),
            'type' => array('type' => self::TYPE_STRING),
            'img_status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' => array('type' => self::TYPE_STRING),
            'text_color' => array('type' => self::TYPE_STRING),
            'bg_color' => array('type' => self::TYPE_STRING),
            'time_from' => array('type' => self::TYPE_STRING),
            'time_to' => array('type' => self::TYPE_STRING)
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

}
<?php

class ProductThumbnailsItems extends ObjectModel
{

    public $thumbnails_name;
    public $selectedthumbnailimage;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_cover_thumbnails',
        'primary' => 'thumbnails_id',
        'multilang' => false,
        'fields' => array(
            'thumbnails_name' => array('type' => self::TYPE_STRING,  'validate' => 'isGenericName', 'required' => true),
            'selectedthumbnailimage' => array('type' => self::TYPE_STRING),
            'type' => array('type' => self::TYPE_STRING),
            'img_status' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'position' => array('type' => self::TYPE_STRING),
        ),
    );

    /** @var string $image_dir */
    protected $image_dir = _PS_IMG_DIR_ .'thumbnail' ;

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        $this->image_dir = _PS_IMG_DIR_ .'thumbnail';
    }

    public function updateIconFiletype($thumbnails_id, $type)
    {
        if(!$thumbnails_id){
            return false;
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->update(
            'product_cover_thumbnails',
            array('type' => $type),
            'thumbnails_id = '.$thumbnails_id);
    }

}
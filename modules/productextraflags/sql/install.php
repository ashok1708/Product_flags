<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2019 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
$sql = array();


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_extra_flags` (
 `id_flag` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `selectedthumbnailimage` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `img_status` int(11) NOT NULL,
  `position` varchar(128) NOT NULL,
  `text_color` varchar(128) NOT NULL,
  `bg_color` varchar(128) NOT NULL,
  `time_from` varchar(128) NOT NULL,
  `time_to` varchar(128) NOT NULL,
  `display_type` varchar(128) NOT NULL
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_flags_item` (
  `id_product` int(11) NOT NULL ,
  `id_flag` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_flags_category` (
  `id_flag` int(11) NOT NULL ,
  `id_category` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_extra_flags_lang` (
  `id_flag` int(11) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `id_shop` int(10) unsigned NOT NULL,
  `name_flag` varchar(255) NOT NULL,    
  PRIMARY KEY (`id_flag`,`id_lang`, `id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'product_extra_flags_shop` (
  `id_flag` int(11) NOT NULL ,
  `id_shop` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
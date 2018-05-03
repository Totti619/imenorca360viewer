<?php
/**
 * Created by PhpStorm.
 * User: Portatil
 * Date: 27/04/2018
 * Time: 10:44
 */

require_once('config/database.php');

class Image extends ObjectModel
{
    public static $definition = array(
        'table' => IMNK_DB_PREFIXED_TABLE_NAME,
        'primary' => 'id_' . IMNK_DB_TABLE_NAME,
        'multilang' => true,
        'fields' => array(
            'image_uri' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'required' => true, 'size' => 255),
        )
    );
    public $image_uri;

    public function __construct($id = null, $image_uri = null, Context $context = null)
    {
        parent::__construct($id);
        $this->image_uri = $image_uri;
    }

    public function add($autodate = true, $null_values = false)
    {
        $context = Context::getContext();

        $res = parent::add($autodate, $null_values);
        $res &= Db::getInstance()->execute('
			INSERT INTO `' . IMNK_DB_PREFIXED_TABLE_NAME . '` (`id_' . IMNK_DB_TABLE_NAME . '`, `image_uri`)
			VALUES(' . (int)$this->id . ', ' . $this->image_uri . ')'
        );
        return $res;
    }

    public function delete()
    {
        $res = true;

//        $images = $this->image;
//        foreach ($images as $image)
//        {
//                if ($image && file_exists(dirname(__FILE__).'/img/samples/'.$image))
//                    $res &= @unlink(dirname(__FILE__).'/img/samples/'.$image);
//        }

//        $res &= $this->reOrderPositions();

        $sql = '
			DELETE FROM `' . IMNK_DB_PREFIXED_TABLE_NAME . '`
			WHERE `id_' . IMNK_DB_TABLE_NAME . '` = ' . (int)$this->id;
        $res &= Db::getInstance()->execute($sql);

        return $res;
    }

//    public function reOrderPositions()
//    {
//        $id_slide = $this->id;
//        $context = Context::getContext();
//        $id_shop = $context->shop->id;
//
//        $max = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
//			SELECT MAX(hss.`position`) as position
//			FROM `'._DB_PREFIX_.'homeslider_slides` hss, `'._DB_PREFIX_.'homeslider` hs
//			WHERE hss.`id_homeslider_slides` = hs.`id_homeslider_slides` AND hs.`id_shop` = '.(int)$id_shop
//        );
//
//        if ((int)$max == (int)$id_slide)
//            return true;
//
//        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
//			SELECT hss.`position` as position, hss.`id_homeslider_slides` as id_slide
//			FROM `'._DB_PREFIX_.'homeslider_slides` hss
//			LEFT JOIN `'._DB_PREFIX_.'homeslider` hs ON (hss.`id_homeslider_slides` = hs.`id_homeslider_slides`)
//			WHERE hs.`id_shop` = '.(int)$id_shop.' AND hss.`position` > '.(int)$this->position
//        );
//
//        foreach ($rows as $row)
//        {
//            $current_slide = new HomeSlide($row['id_slide']);
//            --$current_slide->position;
//            $current_slide->update();
//            unset($current_slide);
//        }
//
//        return true;
//    }
}
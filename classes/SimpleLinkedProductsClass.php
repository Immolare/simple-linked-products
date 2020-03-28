<?php
/**
 * 2007-2020 PrestaShop
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
 *  @author    Pierre Viéville <contact@pierrevieville.fr>
 *  @copyright 2020 - Pierre Viéville
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  https://www.pierrevieville.fr
 */

class SimpleLinkedProductsClass extends ObjectModel
{
    const SIMPLE_LINKED_PRODUCT_TABLE = "simple_linked_products";

    public $id;

    public $id_product;

    public $id_product_linked;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => self::SIMPLE_LINKED_PRODUCT_TABLE,
        'primary' => 'id',
        'multilang' => false,
        'fields' => [
            //'id' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            //'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_product_linked' => ['type' => self::TYPE_INT, 'validate' => 'isInt']
        ]
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public static function install()
    {
        $sqlInstall = "CREATE TABLE " . _DB_PREFIX_ . self::SIMPLE_LINKED_PRODUCT_TABLE . " (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            id_product INT UNSIGNED NOT NULL,
            id_product_linked INT UNSIGNED NOT NULL,
            PRIMARY KEY (id),
            INDEX simple_linked_products_id_product (id_product),
            FOREIGN KEY (id_product)
            REFERENCES " . _DB_PREFIX_ . "product(id)
            ON DELETE CASCADE
        )";

        return Db::getInstance()->execute($sqlInstall);
    }

    public static function unInstall()
    {
        $sqlInstall = "DROP TABLE " . _DB_PREFIX_ . self::SIMPLE_LINKED_PRODUCT_TABLE;

        return Db::getInstance()->execute($sqlInstall);
    }

    public static function findByIdProduct(int $id_product)
    {
        $query = new DbQuery();
        $query->select('*');
        $query->from(self::SIMPLE_LINKED_PRODUCT_TABLE, 'slp');
        $query->where('slp.id_product = ' . (int) $id_product);
        $row = Db::getInstance()->getRow($query);

        $obj = new SimpleLinkedProductsClass();
        $obj->id = $row['id'] ?? null;
        $obj->id_product = $row['id_product'] ?? null;
        $obj->id_product_linked = $row['id_product_linked'] ?? null;

        return $obj;
    }

    public function addProduct()
    {
        return Db::getInstance()->insert(self::SIMPLE_LINKED_PRODUCT_TABLE, [
            'id_product' => (int) $this->id_product,
            'id_product_linked' => (int) $this->id_product_linked
        ]);
    }

    public function updateProduct()
    {
        return Db::getInstance()->update(self::SIMPLE_LINKED_PRODUCT_TABLE, [
            'id_product_linked' => (int) $this->id_product_linked
        ], 'id = ' . (int) $this->id);
    }

    public function saveProduct()
    {
        return (int) $this->id > 0 ? $this->updateProduct() : $this->addProduct();
    }
}

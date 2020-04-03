<?php
/**
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
            REFERENCES " . _DB_PREFIX_ . "product(id_product)
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

    public static function findProduct(int $id_product)
    {
        $query = new DbQuery();
        $query->select('p.*');
        $query->from('product', 'p');
        $query->where('p.id_product = ' . (int) $id_product);

        return Db::getInstance()->getRow($query);
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

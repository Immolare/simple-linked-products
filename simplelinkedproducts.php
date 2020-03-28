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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__) . '/classes/SimpleLinkedProductsClass.php';

class Simplelinkedproducts extends Module
{
    protected $config_form = false;

    const SIMPLE_LINKED_PRODUCT_FORM_FIELD = "id_product_linked";
    const SIMPLE_LINKED_PRODUCT_FORM_ID = "simple_linked_product";
    const SIMPLE_LINKED_PRODUCT_FORM_SEARCH_MAPPING_VALUE = "id";
    const SIMPLE_LINKED_PRODUCT_FORM_SEARCH_MAPPING_NAME = "name";

    public function __construct()
    {
        $this->name = 'simplelinkedproducts';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Pierre Viéville';
        $this->module_key = "0329030f432efdedeb57965fab33c6eb";
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Simple Linked Products');
        $this->description = $this->l('Link a physical product to their virtual version and display a front button.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module ?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->installSql() &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayProductButtons') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionProductUpdate');
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->unInstallSql();
    }

    /**
     * Modifications sql du module
     * @return boolean
     */
    protected function installSql()
    {
        return SimpleLinkedProductsClass::install();
    }

    /**
     * Suppression des modification sql du module
     * @return boolean
     */
    protected function unInstallSql()
    {
        return SimpleLinkedProductsClass::unInstall();
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader($params)
    {
        // Only on product page
        if ('AdminProducts' === $this->context->controller->php_self) {
            $this->context->controller->addCSS(
                $this->_path . '/views/css/' . $this->name . '-back.css'
            );

            $this->context->controller->addJS(
                $this->_path . '/views/js/' . $this->name . '-back.js'
            );
        }
    }

    /**
     * Build the Ajax remote url for querying productsLists and search a product easyly inside
     */
    protected function buildAjaxRemoteUrl()
    {
        // LegacyAdminLink required here as Prestashop not updated this route yet
        return $this->context->link->getLegacyAdminLink('AdminProducts', true, [
            'ajax' => 1,
            'action' => 'productsList',
            'forceJson' => 1,
            'disableCombination' => 1,
            'exclude_packs' => 0,
            'excludeVirtuals' => 0,
            'limit' => 20,
        ]) . '&q=%QUERY';
    }

    /**
     * Retrieve or not a linked product from the current product
     */
    protected function formatTheLinkedProduct($simpleLinkedProductObj = null)
    {
        $product = null;

        if ($simpleLinkedProductObj->id) {
            $language = $this->context->language->id;
            $product = new Product((int) $simpleLinkedProductObj->id_product_linked);
            $product->image_link_small = '//' . (new Link())->getImageLink(
                $product->link_rewrite[$language],
                Image::getCover((int) $product->id)['id_image'],
                ImageType::getFormatedName("small")
            );
        }

        return $product;
    }

    /**
     * Add in the Product page front, a button to a linked product if exists
     */
    public function hookDisplayProductButtons($params)
    {
        $smartyAssign = [];
        $smartyAssign['linkedProduct'] = null;
        $product = $params['product'];

        $simpleLinkedProductObj = SimpleLinkedProductsClass::findByIdProduct((int) $product->id);
        $linkedProduct = $this->formatTheLinkedProduct($simpleLinkedProductObj);

        if (!is_null($linkedProduct)) {
            $linkedProductLink = $this->context->link->getProductLink((int) $linkedProduct->id);

            if (!$product->is_virtual && $linkedProduct->is_virtual) {
                // Link a physical product to a virtual product
                $linkedProductButtonLabel = $this->l('Virtual version');
            } elseif ($product->is_virtual && !$linkedProduct->is_virtual) {
                // Link a virtual product to a physical product
                $linkedProductButtonLabel = $this->l('Physical version');
            } else {
                // Link a virtual product to a virtual product OR
                // Link a physical product to a physical product
                $linkedProductButtonLabel = $this->l('Other version');
            }

            $smartyAssign['linkedProduct'] = $linkedProduct;
            $smartyAssign['linkedProductLink'] = $linkedProductLink;
            $smartyAssign['linkedProductButtonLabel'] = $linkedProductButtonLabel;
        }

        $this->context->smarty->assign($smartyAssign);

        return $this->display(__FILE__, 'views/templates/front/product_buttons.tpl');
    }

    /**
     * Add in the Product tab "Modules" the simple linked product
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $simpleLinkedProductObj = SimpleLinkedProductsClass::findByIdProduct((int) $params['id_product']);
        $linkedProduct = $this->formatTheLinkedProduct($simpleLinkedProductObj);

        $remoteUrl = $this->buildAjaxRemoteUrl();
        $title = $this->l('Link a product');
        $placeholder = $this->l('Select and add a linked product');
        $helpblock = $this->l('Don\'t forget to press "Save"');

        $this->context->smarty->assign([
            'formid' => self::SIMPLE_LINKED_PRODUCT_FORM_ID,
            'fullname' => self::SIMPLE_LINKED_PRODUCT_FORM_FIELD,
            'linkedProduct' => $linkedProduct,
            'remote_url' => $remoteUrl,
            'mapping_name' => self::SIMPLE_LINKED_PRODUCT_FORM_SEARCH_MAPPING_NAME,
            'mapping_value' => self::SIMPLE_LINKED_PRODUCT_FORM_SEARCH_MAPPING_VALUE,
            'limit' => 1, // we allow only one linked item
            'title' => $title,
            'placeholder' => $placeholder,
            'helpblock' => $helpblock,
        ]);

        return $this->display(__FILE__, 'views/templates/admin/products_extra.tpl');
    }

    /**
     * Add or update the table depends of product and productLinked values
     */
    public function hookActionProductUpdate($params)
    {
        $id_product = (int) Tools::getValue('id_product');
        $simpleLinkedProductObj = SimpleLinkedProductsClass::findByIdProduct($id_product);
        $simpleLinkedProductObj->id_product = $id_product;
        $simpleLinkedProductObj->id_product_linked = (int) Tools::getValue(self::SIMPLE_LINKED_PRODUCT_FORM_FIELD);

        $simpleLinkedProductObj->saveProduct();
    }
}

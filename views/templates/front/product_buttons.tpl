{*
*  @author    Pierre Viéville <contact@pierrevieville.fr>
*  @copyright 2020 - Pierre Viéville
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  https://www.pierrevieville.fr
*}
{block name='simple_product_linked'}
{if $linkedProduct}
<hr/>
{include file='./_partials/miniatures/preview.tpl' product=$linkedProduct}
<hr/>
{/if}
{/block}
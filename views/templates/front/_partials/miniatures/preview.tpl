{*
*  @author    Pierre Viéville <contact@pierrevieville.fr>
*  @copyright 2020 - Pierre Viéville
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  https://www.pierrevieville.fr
*}
{block name='slp_miniature_item'}
<article class="slp-miniature product-miniature js-product-miniature" 
        data-id-product="{$product.id_product}" 
        data-id-product-attribute="{$product.id_product_attribute}">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-6">
          {if $product.cover}
            <a href="{$product.url}">
              <img
                src="{$product.cover.bySize.home_default.url}"
                alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                data-full-size-image-url="{$product.cover.large.url}"
              />
            </a>
          {else}
            <a href="{$product.url}">
              <img src="{$urls.no_picture_image.bySize.home_default.url}" />
            </a>
          {/if}
      </div>
      <div class="col-md-6">
        <p class="slp-version">{$linkedProductButtonSubLabel}</p>
        <h4 class="h4 slp-product-title"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></h4>
        <div class="product-prices {if $product->has_discount}has-discount{/if}">
            {if $product.has_discount}
              <div class="product-discount">
                <small class="regular-price">{$product.regular_price}</small>
              </div>
            {/if}
            <div class="h3 current-price">
              {$product.price}
            </div>
            {if $product.has_discount}
              <div class="current-economy">
                <span class="discount discount-amount">
                {if $product.discount_type === 'percentage'}
                  - {$product.discount_percentage_absolute}
                {else}
                  - {$product.discount_to_display}
                {/if}
                </span>
              </div>
            {/if}
            {if $displayUnitPrice}
              <p class="product-unit-price sub">{l s='(%unit_price%)' d='Shop.Theme.Catalog' sprintf=['%unit_price%' => $product.unit_price_full]}</p>
            {/if}
        </div>
        <div id="product-description-short-{$product.id}">{$product.description_short nofilter}</div>
        <hr/>
        <a class="quick-view btn btn-secondary btn-sm btn-block" href="#" data-link-action="quickview">
          <i class="material-icons search">&#xE8B6;</i> {l s='Quick view' d='Shop.Theme.Actions'}
        </a>
      </div>
    </div>
  </div>
</article>
{/block}

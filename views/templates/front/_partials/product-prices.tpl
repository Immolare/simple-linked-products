{*
*  @author    Pierre Viéville <contact@pierrevieville.fr>
*  @copyright 2020 - Pierre Viéville
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  https://www.pierrevieville.fr
*}
{if $linkedProduct->show_price}
  <div class="product-prices">
    {block name='product_price'}
      <div
        class="product-price h5 {if $linkedProduct->has_discount}has-discount{/if}"
        itemprop="offers"
        itemscope
        itemtype="https://schema.org/Offer"
      >
        <link itemprop="availability" href="{$linkedProduct->seo_availability}"/>
        <meta itemprop="priceCurrency" content="{$currency.iso_code}">

        <div class="current-price">
          <span itemprop="price" content="{$linkedProduct->price_amount}">{$linkedProduct->price}</span>

          {if $linkedProduct->has_discount}
            {if $linkedProduct->discount_type === 'percentage'}
              <span class="discount discount-percentage">{l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $linkedProduct->discount_percentage_absolute]}</span>
            {else}
              <span class="discount discount-amount">
                  {l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $linkedProduct->discount_to_display]}
              </span>
            {/if}
          {/if}
        </div>

        {block name='product_unit_price'}
          {if $displayUnitPrice}
            <p class="product-unit-price sub">{l s='(%unit_price%)' d='Shop.Theme.Catalog' sprintf=['%unit_price%' => $linkedProduct->unit_price_full]}</p>
          {/if}
        {/block}
      </div>
    {/block}

    {block name='product_without_taxes'}
      {if $priceDisplay == 2}
        <p class="product-without-taxes">{l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $linkedProduct->price_tax_exc]}</p>
      {/if}
    {/block}

    {block name='product_pack_price'}
      {if $displayPackPrice}
        <p class="product-pack-price"><span>{l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}</span></p>
      {/if}
    {/block}

    {block name='product_ecotax'}
      {if $linkedProduct->ecotax.amount > 0}
        <p class="price-ecotax">{l s='Including %amount% for ecotax' d='Shop.Theme.Catalog' sprintf=['%amount%' => $linkedProduct->ecotax.value]}
          {if $linkedProduct->has_discount}
            {l s='(not impacted by the discount)' d='Shop.Theme.Catalog'}
          {/if}
        </p>
      {/if}
    {/block}

    {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}

    <div class="tax-shipping-delivery-label">
      {if !$configuration.taxes_enabled}
        {l s='No tax' d='Shop.Theme.Catalog'}
      {elseif $configuration.display_taxes_label}
        {$linkedProduct->labels.tax_long}
      {/if}
      {hook h='displayProductPriceBlock' product=$product type="price"}
      {hook h='displayProductPriceBlock' product=$product type="after_price"}
      {if $linkedProduct->additional_delivery_times == 1}
        {if $linkedProduct->delivery_information}
          <span class="delivery-information">{$linkedProduct->delivery_information}</span>
        {/if}
      {elseif $linkedProduct->additional_delivery_times == 2}
        {if $linkedProduct->quantity > 0}
          <span class="delivery-information">{$linkedProduct->delivery_in_stock}</span>
        {* Out of stock message should not be displayed if customer can't order the product. *}
        {elseif $linkedProduct->quantity <= 0 && $linkedProduct->add_to_cart_url}
          <span class="delivery-information">{$linkedProduct->delivery_out_stock}</span>
        {/if}
      {/if}
    </div>
  </div>
{/if}

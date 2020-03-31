{*
*  @author    Pierre Viéville <contact@pierrevieville.fr>
*  @copyright 2020 - Pierre Viéville
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  https://www.pierrevieville.fr
*}
<div id="simple-linked-product" class="mb-3">
    <div id="simple-linked-content" class="row">
        <div class="col-md-12">
            <h2>{$title}</h2>
        </div>
        <div class="col-xl-12 col-lg-12">

            <p class="alert alert-info"><strong>Note :</strong>{$helpblock}</p>

            <fieldset class="form-group">
            <div
                class="simple-linked-product-autocomplete"
                data-formid="{$formid}"
                data-fullname="{$fullname}"
                data-mappingvalue="{$mapping_value}"
                data-mappingname="{$mapping_name}"
                data-remoteurl="{$remote_url}"
                data-limit="{$limit}"
                >
                <div class="search search-with-icon">
                    <input type="text" id="{$formid}" class="form-control search typeahead {$formid}" placeholder="{$placeholder}" autocomplete="off">
                </div>
                <small class="form-text text-muted text-right typeahead-hint"></small>

                <ul id="{$formid}-data" class="typeahead-list nostyle col-sm-12 product-list">
                    {if !is_null($linkedProduct->id)}
                    <li class="media">
                    <div class="media-left">
                        <img class="media-object image" src="{$linkedProduct->image_link_small}" />
                    </div>
                    <div class="media-body media-middle">
                        <span class="label">{$linkedProduct->getFieldByLang('name', Context::getContext()->language->id)} (ref: {$linkedProduct->reference})</span>
                        <i class="material-icons delete">clear</i>
                    </div>
                    <input type="hidden" name="{$fullname}" value="{$linkedProduct->id}" />
                    </li>
                    {/if}
                </ul>
            <div class="invisible" id="tplcollection-{$formid}">
                <span class="label">%s</span><i class="material-icons delete">clear</i>
                </div>
            </div>
            </fieldset>
        </div>
    </div>
</div>
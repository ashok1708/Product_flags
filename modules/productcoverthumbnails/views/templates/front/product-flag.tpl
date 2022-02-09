{block name='product_flags'}
     <ul class="product-flags js-product-flags product-flag-extra">
        {foreach $thumbnails_data as $flag}
            <li class="product-flag extra-flags">{$flag['thumbnails_name']}
                {if $flag['img_status']==1}
                    <img  class="extra-flag-img" src="{$urls.img_ps_url}thumbnail/{$flag['thumbnails_id']}.{$flag['type']}">
                {/if}
            </li>
        {/foreach}
     </ul>
{/block}


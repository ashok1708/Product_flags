{block name='product_flags'}
     <ul class="product-flags js-product-flags product-flag-extra" >
        {foreach $flags_data as $flag}
            <li class="product-flag extra-flags {$flag['position']}" style="color: {$flag['text_color']}; background-color:{$flag['bg_color']} ">
                {if $flag['img_status']==0}
                    <img  class="extra-flag-img" src="{$urls.img_ps_url}thumbnail/{$flag['id_flag']}.{$flag['type']}">
                {else}
                    {$flag['name_flag']}
                {/if}

            </li>
        {/foreach}
     </ul>
{/block}


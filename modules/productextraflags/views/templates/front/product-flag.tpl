{block name='product_flags'}
    <ul class="product-flags js-product-flags product-flag-extra" >
        {foreach $flags_data as $flag}
            {assign var="dateFrom" value= $flag['time_from']}
            {assign var="dateTo" value= $flag['time_to']}
            {assign var="currentDate" value= $smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}

            {if $dateFrom}
                {if $dateFrom <= $currentDate && $dateTo>= $currentDate}
                    <li class="product-flag extra-flags {$flag['position']}" style="color: {$flag['text_color']}; background-color:{$flag['bg_color']} ">
                        {if $flag['img_status']==1}
                            <img class="extra-flag-img" src="{$urls.img_ps_url}thumbnail/{$flag['id_flag']}.{$flag['type']}" title="{$flag['name_flag']}">
                        {else}
                            {$flag['name_flag']}
                        {/if}
                    </li>
                {/if}
            {else}
                <li class="product-flag extra-flags {$flag['position']}" style="color: {$flag['text_color']}; background-color:{$flag['bg_color']} ">
                    {if $flag['img_status']==1}
                        <img class="extra-flag-img" src="{$urls.img_ps_url}thumbnail/{$flag['id_flag']}.{$flag['type']}" title="{$flag['name_flag']}">
                    {else}
                        {$flag['name_flag']}
                    {/if}
                </li>
            {/if}
        {/foreach}
    </ul>
{/block}



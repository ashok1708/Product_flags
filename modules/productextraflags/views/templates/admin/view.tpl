
<div id="product-thumbnails" class="mb-3">
    <div id="thumbnails-title" class="row">
        <div class="col-md-12">
            <h2>{$title}</h2>
        </div>
    </div>
    <div id="thumbnails-content" class="row">
        <div class="col-md-5">
            {if $flags_data}
                {foreach $flags_data as $row}
                    <input type="checkbox" name="flags_item[]" maxlength="50" value="{$row['name_flag']}" >
                    <label> {$row['name_flag']}</label>

                    <br/>
                {/foreach}
            {else}
                <span>No Flags Found</span><br>
                <span>Add flags : Catalog->Product Flags</span>
            {/if}
        </div>

    </div>
</div>

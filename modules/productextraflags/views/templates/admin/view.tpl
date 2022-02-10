
<div id="product-thumbnails" class="mb-3">
    <div id="thumbnails-title" class="row">
        <div class="col-md-12">
            <h2>{$title}</h2>
        </div>
    </div>
    <div id="thumbnails-content" class="row">
        <div class="col-md-5">
                {foreach $flags_data as $row}
                    <input type="checkbox" name="flags_item[]" maxlength="50" value="{$row['name_flag']}" >
                    <label> {$row['name_flag']}</label>

                    <br/>
                {/foreach}
        </div>

    </div>
</div>

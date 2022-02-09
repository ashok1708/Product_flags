
<div id="product-thumbnails" class="mb-3">
    <div id="thumbnails-title" class="row">
        <div class="col-md-12">
            <h2>Thumbnails</h2>
        </div>
    </div>
    <div id="thumbnails-content" class="row">
        <div class="col-md-5">
                {foreach $thumbnails_data as $row}
                    <input type="checkbox" name="thumbnails_item[]" maxlength="50" value="{$row['thumbnails_name']}" >
                    <label> {$row['thumbnails_name']}</label>

                    <br/>
                {/foreach}
        </div>

    </div>
</div>

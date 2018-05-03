<!-- Block mymodule -->
<div id="imenorca360viewer_block_home" class="block">
    <div class="slider">
        <ul class="slides">
            {foreach from=$images item=image}
                <li>
                    <div class="panorama">{$image.image_uri}</div>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
{$script}
<!-- /Block mymodule -->
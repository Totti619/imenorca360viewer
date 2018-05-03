<div class="panel"><h3><i class="icon-list-ul"></i> {l s='Images' mod='imenorca360viewer'}
        <span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn"
           href="{$link->getAdminLink('AdminModules')}&configure=imenorca360viewer&add=1">
			<span title="" data-toggle="tooltip" class="label-tooltip"
                  data-original-title="{l s='Add new' mod='imenorca360viewer'}" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
    </h3>
    <div id="imagesContent">
        <div id="images">
            <div class="row">
                {foreach from=$images item=image}
                    <div id="image_{$image.id_imenorca360viewer}" class="col-sm-12 col-lg-3">
                        <div class="card">
                            <img style="width: 100%" class="card-img-top" src="{$image.image_uri}" alt="image"/>
                            <div class="card-body">
                                <div class="btn-group-action pull-right" style="margin: 10px">
                                    {*{$image.status}*}

                                    <a class="btn btn-secondary"
                                       href="{$link->getAdminLink('AdminModules')}&configure=imenorca360viewer&modify={$image.id_imenorca360viewer}">
                                        <i class="icon-edit"></i>
                                        {l s='Edit' mod='imenorca360viewer'}
                                    </a>
                                    <a class="btn btn-danger"
                                       href="{$link->getAdminLink('AdminModules')}&configure=imenorca360viewer&delete={$image.id_imenorca360viewer}">
                                        <i class="icon-trash"></i>
                                        {l s='Delete' mod='imenorca360viewer'}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
            <div class="row">
                <button type="submit" value="1" id="configuration_form_submit_btn_1" name="submitimenorca360viewer"
                        class="btn btn-default pull-right">
                    <i class="process-icon-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

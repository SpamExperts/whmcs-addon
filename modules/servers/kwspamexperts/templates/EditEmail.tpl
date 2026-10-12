<div class="card">
    <div class="card-body">
        <h3 class="card-title">{$lang.header}</h3>
        {if $_result && isset($_result.list)}
            {foreach $_result.list as $item}
                <div class="alert {if $item.code==1}alert-success{else}alert-danger{/if}">{$item.msg|escape}</div>
            {/foreach}
        {elseif $_result}
            <div class="alert {if $_result.code==1}alert-success{elseif $_result.code==3}alert-info{else}alert-danger{/if}">{$_result.msg|escape}</div>
        {/if}
        {if $_status && $_status.code==2}
            <div class="alert alert-danger">{$_status.msg|escape}</div>
        {else}
            {if $_status}
                <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg|escape}</div>
            {/if}
            <p>{$lang.description}</p>
            <form class="form-horizontal" action="clientarea.php?action=productdetails&id={$serviceid}&manage=EditEmail" method="post">
                <input type="hidden" name="token" value="{$token}" />
                <input type="hidden" name="_action" value="save" />
                <div class="form-group row">
                    <label for="se-primaryemail" class="col-sm-4 control-label col-form-label">{$lang.primarycontact}</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="se-primaryemail" name="primaryemail" value="{$primarycontact|escape}" />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="se-adminemail" class="col-sm-4 control-label col-form-label">{$lang.admincontact}</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="se-adminemail" name="adminemail" value="{$admincontact|escape}" />
                    </div>
                </div>
                {* Centered in Six (Bootstrap 3); text-sm-left only exists in
                   Bootstrap 4, whose themes align form buttons left *}
                <div class="form-group">
                    <div class="text-center text-sm-left">
                        <button type="submit" name="submit" class="btn btn-primary">{$lang.save}</button>
                    </div>
                </div>
            </form>
        {/if}
    </div>
</div>

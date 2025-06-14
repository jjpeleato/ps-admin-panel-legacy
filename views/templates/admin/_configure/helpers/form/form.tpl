{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}
{extends file="helpers/form/form.tpl"}

{block name="field"}
  {if $input.type == 'image_lang'}
    <div class="col-lg-8">
      <div class="form-group">
        {foreach from=$languages item=language}
          {if $languages|count > 1}
            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display: none;"{/if}>
          {/if}
            <div class="col-lg-{if $languages|count > 1}10{else}12{/if}">
              <div class="dummyfile input-group">
                <input id="{$input.name}_{$language.id_lang}" type="file" name="{$input.name}_{$language.id_lang}" class="hide-file-upload" />
                <span class="input-group-addon"><i class="icon-file"></i></span>
                <input id="{$input.name}_{$language.id_lang}-name" type="text" name="filename" class="disabled" readonly />
                <span class="input-group-btn">
                  <button id="{$input.name}_{$language.id_lang}-add" type="button" name="submitAddAttachments" class="btn btn-default">
                    <i class="icon-folder-open"></i> {l s='Choose a file' d='Admin.Actions'}
                  </button>
                </span>
              </div>
            </div>
            {if $languages|count > 1}
              <div class="col-lg-2">
                <button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
                  {$language.iso_code}
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                  {foreach from=$languages item=lang}
                    <li><a href="javascript:hideOtherLanguage({$lang.id_lang});" tabindex="-1">{$lang.name}</a></li>
                  {/foreach}
                </ul>
              </div>
            {/if}
            {if isset($fields_value[$input.name][$language.id_lang]) && $fields_value[$input.name][$language.id_lang] != ''}
              <div id="{$input.name}_{$language.id_lang}-thumbnail" class="col-lg-12" style="margin-top: 10px;">
                <img src="{$uri}upload/{$fields_value[$input.name][$language.id_lang]}" class="img-thumbnail" width="200" />
              </div>
              <div id="{$input.name}_{$language.id_lang}-actions" class="col-lg-12" style="margin-top: 5px;">
                <button id="{$input.name}_{$language.id_lang}-delete"  type="button" name="submitDeleteAttachments" class="btn btn-danger">
                  <i class="icon-trash"></i> {l s='Delete a file' d='Admin.Actions'}
                </button>
              </div>
            {/if}
          {if $languages|count > 1}
            </div>
          {/if}
          <script>
            $(document).ready(function() {
              $('#{$input.name}_{$language.id_lang}-add').click(function(e){
                $('#{$input.name}_{$language.id_lang}').trigger('click');
              });

              $('#{$input.name}_{$language.id_lang}').change(function(e){
                var val = $(this).val();
                var file = val.split(/[\\/]/);
                $('#{$input.name}_{$language.id_lang}-name').val(file[file.length-1]);
              });

              $('#{$input.name}_{$language.id_lang}-delete').click(function(e){
                $.ajax({
                  url: window.psapl_controller_delete_url,
                  type: 'POST',
                  dataType: 'JSON',
                  async: false,
                  data: {
                    ajax: true,
                    controller: window.psapl_controller_delete,
                    action: 'DeleteMedia',
                    body: {
                      name: '{$input.name}',
                      lang: '{$language.id_lang}',
                    }
                  },
                  success: function (result, status, xhr) {
                    if (result.success) {
                      $('#{$input.name}_{$language.id_lang}-thumbnail').remove();
                      $('#{$input.name}_{$language.id_lang}-actions').remove();
                      alert(result.message || "Image deleted successfully.");
                    } else {
                      alert(result.message || "Error deleting image.");
                    }
                  },
                  error: function (xhr, status, error) {
                    console.error("Error deleting image");
                  }
                });
              });
            });
          </script>
        {/foreach}
      </div>
    </div>
  {else}
    {$smarty.block.parent}
  {/if}
{/block}

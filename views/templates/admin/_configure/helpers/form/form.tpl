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
  {if $input.type === 'image_lang' || $input.type === 'video_lang'}
    <div class="col-lg-8">
      <div class="form-group">
        {foreach from=$languages item=language}
          {if $languages|count > 1}
            <div class="translatable-field lang-{$language.id_lang}" {if $language.id_lang != $defaultFormLanguage}style="display: none;"{/if}>
          {/if}
            <div class="col-lg-{if $languages|count > 1}10{else}12{/if}">
              <div class="dummyfile input-group">
                <input class="hide-file-upload js-attachment" name="{$input.name}_{$language.id_lang}" data-name="{$input.name}" data-lang="{$language.id_lang}" type="file" />
                <span class="input-group-addon"><i class="icon-file"></i></span>
                <input class="disabled js-attachment-name" name="filename" data-name="{$input.name}" data-lang="{$language.id_lang}" type="text" readonly />
                <span class="input-group-btn">
                  <button class="btn btn-default js-attachment-add" name="submitAddAttachments" data-name="{$input.name}" data-lang="{$language.id_lang}" type="button">
                    <i class="icon-folder-open"></i> {l s='Choose a file' d='Admin.Actions'}
                  </button>
                </span>
              </div>
            </div>
            {if $languages|count > 1}
              <div class="col-lg-2">
                <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" tabindex="-1">
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
              <div class="col-lg-12 js-media" data-name="{$input.name}" data-lang="{$language.id_lang}" style="margin-top: 10px;">
                {if $input.type === 'video_lang'}
                  <video controls class="embed-responsive" width="400">
                    <source src="{$uri}upload/{$fields_value[$input.name][$language.id_lang]}">
                    {l s='Your browser does not support the video tag.' d='Admin.Actions'}
                  </video>
                {else}
                  <img src="{$uri}upload/{$fields_value[$input.name][$language.id_lang]}" class="img-thumbnail" width="200" />
                {/if}
              </div>
              <div class="col-lg-12 js-media-actions" data-name="{$input.name}" data-lang="{$language.id_lang}" style="margin-top: 5px;">
                <button class="btn btn-danger js-media-delete" name="submitDeleteAttachments" data-name="{$input.name}" data-lang="{$language.id_lang}" type="button">
                  <i class="icon-trash"></i> {l s='Delete a file' d='Admin.Actions'}
                </button>
              </div>
            {/if}
          {if $languages|count > 1}
            </div>
          {/if}
        {/foreach}
      </div>
    </div>
  {else}
    {$smarty.block.parent}
  {/if}
{/block}

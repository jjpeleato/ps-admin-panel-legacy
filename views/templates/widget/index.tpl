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
<div class="container">
  <div class="row">
    <div class="col-12">
      {if false === empty($title)}
        <h3>{$title}</h3>
      {/if}
      {if false === empty($short_description)}
        <div class="description">
          {$short_description nofilter}
        </div>
      {/if}
      {if false === empty($description)}
        <div class="description">
          {$description nofilter}
        </div>
      {/if}
      {if false === empty($image)}
        <div class="image">
          <img src="{$path}/upload/{$image}" alt="{$title}" title="{$title}" />
        </div>
      {/if}
    </div>
  </div>
</div>

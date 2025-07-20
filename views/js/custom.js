/**
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
 */
(function ($) {
  $(document).ready(function () {
    const moduleForm = $("#module_form");
    if (!moduleForm.hasClass("ps_dynamic_admin_panel")) {
      return;
    }

    $(".js-attachment-add").click(function () {
      const name = $(this).data("name");
      const language = $(this).data("lang");
      const dom = $(
        `.js-attachment[data-name="${name}"][data-lang="${language}"]`
      );

      dom.trigger("click");
    });

    $(".js-attachment").change(function (e) {
      const name = $(this).data("name");
      const language = $(this).data("lang");
      const val = $(this).val();
      const file = val.split(/[\\/]/);
      const dom = $(
        `.js-attachment-name[data-name="${name}"][data-lang="${language}"]`
      );

      dom.val(file[file.length - 1]);
    });

    $(".js-media-delete").click(function (e) {
      const name = $(this).data("name");
      const language = $(this).data("lang");
      const domThumbnail = $(
        `.js-media[data-name="${name}"][data-lang="${language}"]`
      );
      const domActions = $(
        `.js-media-actions[data-name="${name}"][data-lang="${language}"]`
      );

      $.ajax({
        url: window.psapl_controller_delete_url,
        type: "POST",
        dataType: "JSON",
        data: {
          ajax: true,
          controller: window.psapl_controller_delete,
          action: "DeleteMedia",
          body: {
            name: name,
            lang: language,
          },
        },
        success: function (result, status, xhr) {
          if (result.success) {
            domThumbnail.remove();
            domActions.remove();
            window.showSuccessMessage(
              result.message || "Image deleted successfully."
            );
          } else {
            window.showErrorMessage(result.message || "Error deleting image.");
          }
        },
        error: function (xhr, status, error) {
          window.showErrorMessage("Error deleting image. Please try again.");
          console.error("Error deleting image:", error);
        },
      });
    });
  });
})(jQuery);

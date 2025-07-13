(function ($) {
  $(document).ready(function () {
    const moduleForm = $("#module_form");
    if (!moduleForm.hasClass("ps_admin_panel_legacy")) {
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

    $(".js-thumbnail-delete").click(function (e) {
      const name = $(this).data("name");
      const language = $(this).data("lang");
      const domThumbnail = $(
        `.js-thumbnail[data-name="${name}"][data-lang="${language}"]`
      );
      const domActions = $(
        `.js-thumbnail-actions[data-name="${name}"][data-lang="${language}"]`
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
            window.showSuccessMessage(result.message || "Image deleted successfully.");
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

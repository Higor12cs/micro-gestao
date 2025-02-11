function refreshDataTables() {
  $(".data-table").each(function () {
    $(this).DataTable().ajax.reload(null, false);
  });
}

function clearValidationErrors() {
  $(".invalid-feedback").remove();
  $("input").removeClass("is-invalid");
}

function displayValidationErrors(errors) {
  for (const field in errors) {
    if (errors.hasOwnProperty(field)) {
      const input = $(`[name="${field}"]`);
      input
        .addClass("is-invalid")
        .after(
          `<div class="invalid-feedback"><strong>${errors[field][0]}</strong></div>`
        );
    }
  }
}

function disableModalInputs(disabled) {
  $("#crud-modal input, #crud-modal select, #crud-modal button").prop(
    "disabled",
    disabled
  );
}

function setInputValues(data) {
  for (const field in data) {
    if (data.hasOwnProperty(field)) {
      const input = $(`#crud-form [name="${field}"]`);
      if (input.attr("type") === "checkbox") {
        input.prop("checked", data[field]);
      } else {
        input.val(data[field]).change();
      }
    }
  }
}

function openModal(modalTitle, formAction, method = "POST", data = {}) {
  const modal = $("#crud-modal");
  const form = $("#crud-form");

  modal.find(".modal-title").text(modalTitle);
  form.attr("action", formAction).find('input[name="_method"]').remove();

  if (method === "PUT") {
    form.append('<input type="hidden" name="_method" value="PUT">');
  }

  form[0].reset();
  clearValidationErrors();
  setInputValues(data);

  modal.modal("show").on("shown.bs.modal", function () {
    form.find("input:visible").first().focus();
  });
}

function handleAjaxError(xhr) {
  console.error("Error:", xhr.responseText);
}

$(document).ready(function () {
  $(document).on("click", ".create-entity", function () {
    openModal("Novo Registro", $(this).data("action-url"));
  });

  $(document).on("click", ".edit-entity", function () {
    const entityId = $(this).data("id");
    if (!entityId) return;

    const formAction = `/ajax/${$(this).data("entity")}/${entityId}`;
    openModal("Editar Registro", formAction, "PUT", {});
    disableModalInputs(true);

    $.ajax({
      url: formAction,
      type: "GET",
      beforeSend: function (xhr) {
        xhr.setRequestHeader(
          "X-CSRF-TOKEN",
          $('meta[name="csrf-token"]').attr("content")
        );
      },
      success: function (response) {
        openModal("Editar Registro", formAction, "PUT", response);
      },
      error: handleAjaxError,
      complete: function () {
        disableModalInputs(false);
      },
    });
  });

  $(document).on("click", ".delete-entity", function () {
    const entityId = $(this).data("id");
    const entityName = $(this).data("entity");

    if (!entityId) return;

    $("#confirm-delete-modal").modal("show");

    $("#confirm-delete")
      .off("click")
      .on("click", function () {
        $.ajax({
          url: `/ajax/${entityName}/${entityId}`,
          type: "DELETE",
          beforeSend: function (xhr) {
            xhr.setRequestHeader(
              "X-CSRF-TOKEN",
              $('meta[name="csrf-token"]').attr("content")
            );
          },
          success: function () {
            $("#confirm-delete-modal").modal("hide");
            refreshDataTables();
          },
          error: handleAjaxError,
        });
      });
  });

  $("#crud-form").submit(function (event) {
    event.preventDefault();
    clearValidationErrors();
    const form = $(this);
    const actionUrl = form.attr("action");
    const method = form.find('input[name="_method"]').val() || "POST";

    $.ajax({
      url: actionUrl,
      type: method,
      data: form.serialize(),
      beforeSend: function (xhr) {
        xhr.setRequestHeader(
          "X-CSRF-TOKEN",
          $('meta[name="csrf-token"]').attr("content")
        );
        disableModalInputs(true);
      },
      success: function () {
        $("#crud-modal").modal("hide");
        refreshDataTables();
        form[0].reset();
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          displayValidationErrors(xhr.responseJSON.errors);
        } else {
          handleAjaxError(xhr);
        }
      },
      complete: function () {
        disableModalInputs(false);
      },
    });
  });
});

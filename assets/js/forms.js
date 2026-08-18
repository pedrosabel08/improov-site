(function () {
  "use strict";
  function tr(key) {
    return window.ImproovI18n?.translate(key) || key;
  }
  function setup(form) {
    const status = form.querySelector("[data-form-status]");
    const submit = form.querySelector("button[type=submit]");
    let submitting = false;
    form.querySelectorAll("input[type=file]").forEach((input) =>
      input.addEventListener("change", () => {
        const file = input.files[0];
        const label = input
          .closest(".upload-field")
          ?.querySelector("[data-file-name]");
        if (label) label.textContent = file ? file.name : "";
        input.setCustomValidity(
          file &&
            Number(input.dataset.maxSize || 0) &&
            file.size > Number(input.dataset.maxSize)
            ? tr("form.fileSize")
            : "",
        );
      }),
    );
    form.querySelectorAll("textarea[maxlength]").forEach((area) => {
      const count = area.parentElement.querySelector("[data-character-count]");
      const update = () => {
        if (count) count.textContent = `${area.value.length}/${area.maxLength}`;
      };
      area.addEventListener("input", update);
      update();
    });
    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      if (submitting) return;
      form.classList.add("was-validated");
      status.className = "form-status";
      if (!form.checkValidity()) {
        status.textContent = tr("form.validation");
        status.classList.add("is-error");
        form.querySelector(":invalid")?.focus();
        return;
      }
      submitting = true;
      submit.disabled = true;
      submit.setAttribute("aria-busy", "true");
      status.textContent = tr("form.sending");
      try {
        const response = await fetch(form.action, {
          method: "POST",
          body: new FormData(form),
          headers: { Accept: "application/json" },
        });
        const payload = await response.json();
        if (!response.ok || !payload.success)
          throw new Error(payload.message || tr("form.error"));
        form.reset();
        form.classList.remove("was-validated");
        form
          .querySelectorAll("[data-file-name]")
          .forEach((el) => (el.textContent = ""));
        form
          .querySelectorAll("[data-character-count]")
          .forEach(
            (el) => (el.textContent = el.textContent.replace(/^\d+/, "0")),
          );
        document
          .querySelectorAll("[data-language-input]")
          .forEach(
            (input) =>
              (input.value = window.ImproovI18n?.getLanguage() || "pt-BR"),
          );
        status.textContent = tr("form.success");
        status.classList.add("is-success");
      } catch (error) {
        status.textContent = error.message || tr("form.error");
        status.classList.add("is-error");
      } finally {
        submitting = false;
        submit.disabled = false;
        submit.removeAttribute("aria-busy");
      }
    });
  }
  document.addEventListener("DOMContentLoaded", () =>
    document.querySelectorAll("[data-async-form]").forEach(setup),
  );
})();

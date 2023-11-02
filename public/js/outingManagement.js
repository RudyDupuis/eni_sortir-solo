const cancelButton = document.querySelector("#outing-cancel");

if (cancelButton) {
  cancelButton.addEventListener("click", function () {
    const hiddenForm = document.querySelector(".cancelform-hidden");
    hiddenForm.classList.remove("cancelform-hidden");

    cancelButton.classList.add("cancelform-hidden");
  });
}

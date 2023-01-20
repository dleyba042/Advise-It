const planForm = document.getElementById("plan_form");
const printButton = document.getElementById("print_button");

planForm.onsubmit = () => {
  alert("New information being saved in the database.");
  return true;
};

printButton.addEventListener("click", () => {
  window.print();
});

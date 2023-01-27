const planForm = document.getElementById("plan_form");
const printButton = document.getElementById("print_button");
const prevButton = document.getElementById("prev_button");
const nextButton = document.getElementById("next_button");

planForm.onsubmit = () => {
  alert("New information being saved in the database.");
  return true;
};

printButton.addEventListener("click", () => {
  window.print();
});

$("#prev_button").click(function (e) {
  e.preventDefault();
  $.ajax({
    url: "https://dleyba-brown.greenriverdev.com/CapstoneProject/View/process.php",
    type: "POST",
    data: { prev_button: true, token: prevButton.value },
    success: function () {
      alert("ok");
    },
  });
});

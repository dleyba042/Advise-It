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
  let info = prevButton.value.split(":");
  let studentToken = info[0];
  let nextYearUp = info[1];
  let initialYear = info[2];

  if (initialYear - nextYearUp > 2) {
    alert("cannot got back further than two years!");
  } else {
    //Create an initial DB entry for this plan year
    e.preventDefault();
    $.ajax({
      url: "https://dleyba-brown.greenriverdev.com/CapstoneProject/View/process.php",
      type: "POST",
      data: { prev_button: true, token: studentToken },
      success: function () {
        alert("ok");
      },
    });

    //Append a new template for this year
  }
});

const planForm = document.getElementById("plan_form");
const printButton = document.getElementById("print_button");
const prevButton = document.getElementById("prev_button");
const nextButton = document.getElementById("next_button");
const planDiv = document.getElementById("all_plans");

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
      data: { token: studentToken, year: nextYearUp },
      success: function () {
        alert("ok");
      },
    });

    //Adjust previous button value
    prevButton.value = `${studentToken}:${nextYearUp - 1}:${initialYear}`;

    //Append a new template for this year

    let header = document.createElement("h1");
    header.innerHTML = "YEAHHH";

    planDiv.prepend(header);
  }
});

$("#next_button").click(function (e) {
  let info = nextButton.value.split(":");
  let studentToken = info[0];
  let nextYearUp = info[1];
  let initialYear = info[2];

  if (nextYearUp - initialYear > 17) {
    alert("cannot go further than 2040!");
  } else {
    //Create an initial DB entry for this plan year

    console.log("Next BUTTON :: INIT YEAR: " + nextYearUp);

    e.preventDefault();
    $.ajax({
      url: "https://dleyba-brown.greenriverdev.com/CapstoneProject/View/process.php",
      type: "POST",
      data: { token: studentToken, year: nextYearUp },
      success: function () {
        alert("ok");
      },
    });

    //Adjust next button value
    nextButton.value = `${studentToken}:${
      parseInt(nextYearUp) + 1
    }:${initialYear}`;

    //Append a new template for this year

    let header = document.createElement("h1");
    header.innerHTML = "YEAHHH";

    planDiv.append(header);
  }
});

const planForm = document.getElementById("plan_form");
const printButton = document.getElementById("print_button");
const prevButton = document.getElementById("prev_button");
const nextButton = document.getElementById("next_button");
const planDiv = document.getElementById("all_plans");
const months = ["fall", "winter", "spring", "summer"];

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
    //Adjust previous button value
    prevButton.value = `${studentToken}:${nextYearUp - 1}:${initialYear}`;

    //prepend a new template for this year
    let form = createNewPlanForm(nextYearUp);

    //Create header for new from
    let header = document.createElement("h1");
    header.innerHTML = nextYearUp + " School Year";

    planDiv.prepend(form);
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
    //Adjust next button value
    nextButton.value = `${studentToken}:${
      parseInt(nextYearUp) + 1
    }:${initialYear}`;

    //Append a new template for this year
    let form = createNewPlanForm(nextYearUp);

    //Create header for new from
    let header = document.createElement("h1");
    header.innerHTML = nextYearUp + " School Year";

    planDiv.append(header);
    planDiv.append(form);
  }
});

const createNewPlanForm = (year) => {
  let container = document.createElement("div");
  container.classList.add("plan_container");

  for (let i = 0; i < months.length; i++) {
    let textContainer = document.createElement("div");
    textContainer.classList.add("textContainer");

    let month = months[i].charAt(0).toUpperCase() + months[i].substring(1);

    let header = document.createElement("h5");
    if (i == 0) {
      header.innerHTML = `${month} ${year}`;
    } else {
      header.innerHTML = `${month} ${parseInt(year) + 1}`;
    }

    let textarea = document.createElement("textarea");

    textarea.id = months[i];
    textarea.name = `${months[i]}_${year}`;

    textContainer.appendChild(header);
    textContainer.appendChild(textarea);
    container.appendChild(textContainer);
  }

  return container;
};

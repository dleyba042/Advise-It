const adminButton = document.getElementById("admin_button");
const loginButton = document.getElementById("login_button");
const username = document.getElementById("user_name");
const password = document.getElementById("password");
const passWarning = document.getElementById("pass_warning");
const userWarning = document.getElementById("user_warning");
const hiddenDiv = document.getElementById("hidden_div");

adminButton.addEventListener("click", () => {
  console.log("clicked");

  if (hiddenDiv.classList.contains("hidden")) {
    console.log("yep");
    hiddenDiv.classList.remove("hidden");
    hiddenDiv.classList.add("shown");
  } else {
    //  console.log("what?");
    hiddenDiv.classList.remove("shown");
    hiddenDiv.classList.add("hidden");
  }
});

loginButton.addEventListener("click", () => {
  if (username.value == "admin" && password.value == "@dm1n") {
    removeError(userWarning);
    removeError(passWarning);
    window.location = "admin.php";
  } else {
    //TODO add those hiddent elements in html
    showError(userWarning);
    showError(passWarning);
  }
});

const showError = (elem) => {
  elem.classList.remove("hidden");
  elem.classList.add("error");
};

const removeError = (elem) => {
  elem.classList.add("hidden");
  elem.classList.remove("error");
};

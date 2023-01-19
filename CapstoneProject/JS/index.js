const adminButton = document.getElementById("admin_button");
const loginButton = document.getElementById("login_button");
const hiddenDiv = document.getElementById("entry_div");
const username = document.getElementById("user_name");
const password = document.getElementById("password");

adminButton.addEventListener("click", () => {
  if (hiddenDiv.style.display == "none") {
    hiddenDiv.style.display = "grid";
  } else {
    hiddenDiv.style.display = "none";
  }
});

loginButton.addEventListener("click", () => {
  if (username.value == "admin" && password.value == "@dm1n") {
    console.log("Redirect here");
    //Todo use window.location = ""
    window.location = "https://www.msn.com/en-us";
  } else {
    //TODO add those hiddent elements in html
    console.log("display errors on page");
  }
});

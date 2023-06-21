function changePassword() {
  const newPassword = document.getElementById("newPassword").value; 
  const newPasswordRepeated = document.getElementById("newPasswordRepeated").value;
  const messageContainer = document.querySelector("#changePasswordDialog p");

  if (newPassword !== newPasswordRepeated) {
    messageContainer.innerHTML = "Паролите не съвпадат";
  }
  fetch("../api/users", {
    method: "GET",
    headers: {
      Authorization: `Bearer ${getToken()}`
    }
  })
    .then((response) => response.json())
    .then((users) => {
      if(users.length === 0) {
        messageContainer.innerHTML = "Не можахме да намерим потребителските Ви данни в базата данни. ";
      } else {
        const user = users[0];
        fetch(`../api/users/${user.id}`, {
          method: "PATCH",
          headers: {
            Authorization: `Bearer ${getToken()}`
          },
          body: JSON.stringify({password: newPassword})
        })
          .then((response) => {
            if(response.ok) {
              messageContainer.innerHTML = "Паролата беше променена успешно";
              logout();
            } else {
              messageContainer.innerHTML = "Неуспешна смяна на паролата. Моля опитайте по-късно";
            }
          })
        .catch((error) => console.error("Couldn't change password: ", error));
      }
    })
  .catch((error) => console.error("Couldn't get user: ", error));
  document.getElementById("changePasswordDialog").showModal();
}

function deleteAccount() {
  const messageContainer = document.querySelector("#deleteAccountDialog p")

  fetch("../api/users", {
    method: "GET",
    headers: {
      Authorization: `Bearer ${getToken()}`
    }
  })
    .then((response) => response.json())
    .then((users) => {
      if(users.length === 0) {
        messageContainer.innerHTML = "Не можахме да намерим потребителските Ви данни в базата данни. ";
      } else {
        const user = users[0];
        fetch(`../api/users/${user.id}`, {
          method: "DELETE",
          headers: {
            Authorization: `Bearer ${getToken()}`
          }
        })
          .then((response) => {
            if(response.ok) {
              messageContainer.innerHTML = "Акаунтът Ви беше изтрит. ";
              logout();
            } else {
              messageContainer.innerHTML = "Неуспешна смяна на паролата. Моля опитайте по-късно";
            }
          })
        .catch((error) => console.error("Couldn't delete user: ", error));
      }
    })
  .catch((error) => console.error("Couldn't get user: ", error));
  document.getElementById("deleteAccountDialog").showModal();
}

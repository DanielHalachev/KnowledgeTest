<?php
// Get the currentPassword and newPassword from the request parameters
$currentPassword = $_GET['currentPassword'];
$newPassword = $_GET['newPassword'];

// Read the token value from the cookie
$token = $_COOKIE['token'];

// Hash the passwords
$currentPasswordHashed = password_hash($currentPassword, PASSWORD_DEFAULT);
$newPasswordHashed = password_hash($newPassword, PASSWORD_DEFAULT);

// Perform the GET request to /api/users with the bearer token
$url = '../api/users';
$options = [
    'http' => [
        'header' => "Authorization: Bearer $token",
        'method' => 'GET'
    ]
];
$response = file_get_contents($url, false, stream_context_create($options));

if ($response) {
  $userData = json_decode($response, true);

  $userId = $userData['id'];
  $userPassword = $userData['password'];

  if (password_verify($currentPassword, $userPassword)) {
    $patchUrl = "../api/users/$userId?password=$newPasswordHashed";
    $patchOptions = [
      'http' => [
        'header' => "Authorization: Bearer $token",
        'method' => 'PATCH'
      ]
    ];
    $patchResponse = file_get_contents($patchUrl, false, stream_context_create($patchOptions));

    if ($patchResponse) {
      echo "Password updated successfully!";
      setcookie("token", time()-3600);
      unset($_COOKIE["token"]);
      header("Location: ./index.html");
    } else {
      echo "Failed to update the password.";
    }
  } else {
    echo "Invalid current password.";
  }
} else {
  echo "Failed to fetch user data.";
}
?>
          <form action="changePassword.php">
            <input type="password" name="currentPassword" placeholder="Въведете новата парола"/>
            <input type="password" name="currentPassword" placeholder="Въведете настоящата парола"/>
            <button type="button" formmethod="dialog">Отмяна</button>
            <button type="button" formmethod="submit">Смяна на паролата</button>
          </form>


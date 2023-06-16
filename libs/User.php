<?php

class User implements JsonSerializable {

    private int $id;
    private ?string $googleId;
    private string $email;
    private string $password;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $profilePicture;

    public function __construct(int $id, ?string $googleId, string $email, string $password, ?string $firstName,
                                ?string $lastName, ?string $profilePicture) {
        $this->id = $id;
        $this->googleId = $googleId;
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->profilePicture = $profilePicture;
    }


    public function jsonSerialize() : array {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }
  public function getId(): int {
    return $this->id;
  }
  public function getGoogleId(): ?string {
    return $this->googleId;
  }
  public function getEmail(): string {
    return $this->email;
  }
  public function getPassword(): string {
    return $this->password;
  }
  public function getFirstName(): ?string {
    return $this->firstName;
  }
  public function getLastName(): ?string {
    return $this->lastName;
  }
}

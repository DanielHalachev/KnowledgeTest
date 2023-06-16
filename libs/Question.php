<?php

class Question implements JsonSerializable {

    private int $id;
    private ?int $testId;
    private ?string $aim;
    private int $questionTypeId;
    private bool $isMultipleChoice;
    private string $text;
    private ?string $correctFeedback;
    private ?string $incorrectFeedback;

    public function __construct(int $id, ?int $testId, ?string $aim, int $questionTypeId, bool $isMultipleChoice,
                                string $text, ?string $correctFeedback, ?string $incorrectFeedback)
    {
        $this->id = $id;
        $this->testId = $testId;
        $this->aim = $aim;
        $this->questionTypeId = $questionTypeId;
        $this->isMultipleChoice = $isMultipleChoice;
        $this->text = $text;
        $this->correctFeedback = $correctFeedback;
        $this->incorrectFeedback = $incorrectFeedback;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'testId' => $this->testId,
            'aim' => $this->aim,
            'questionTypeId' => $this->questionTypeId,
            'isMultipleChoice' => $this->isMultipleChoice,
            'text' => $this->text,
            'correctFeedback' => $this->correctFeedback,
            'incorrectFeedback' => $this->incorrectFeedback,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTestId(): ?int {
        return $this->testId;
    }

    public function getAim(): ?string {
        return $this->aim;
    }

    public function getQuestionTypeId(): int {
        return $this->questionTypeId;
    }

    public function isMultipleChoice(): bool {
        return $this->isMultipleChoice;
    }

    public function getText(): string {
        return $this->text;
    }

    public function getCorrectFeedback(): ?string {
        return $this->correctFeedback;
    }

    public function getIncorrectFeedback(): ?string {
        return $this->incorrectFeedback;
    }
}

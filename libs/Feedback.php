<?php

class Feedback implements JsonSerializable {

    private int $id;
    private int $questionId;
    private ?int $complexity;
    private ?string $feedback;

    public function __construct(int $id, int $questionId, ?int $complexity, ?string $feedback) {
        $this->id = $id;
        $this->questionId = $questionId;
        $this->complexity = $complexity;
        $this->feedback = $feedback;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'questionId' => $this->questionId,
            'text' => $this->text,
            'isCorrect' => $this->isCorrect,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getQuestionId(): int {
        return $this->questionId;
    }

    public function getComplexity(): ?int {
        return $this->complexity;
    }

    public function getFeedback(): ?string {
        return $this->feedback;
    }
}
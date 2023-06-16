<?php

class Answer implements JsonSerializable {

    private int $id;
    private int $questionId;
    private int $text;
    private int $isCorrect;

    public function __construct(int $id, int $questionId, int $text, int $isCorrect) {
        $this->id = $id;
        $this->questionId = $questionId;
        $this->text = $text;
        $this->isCorrect = $isCorrect;
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

    public function getText(): int {
        return $this->text;
    }

    public function getIsCorrect(): int {
        return $this->isCorrect;
    }
}
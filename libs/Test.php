<?php

class Test implements JsonSerializable {

    private int $id;
    private ?int $uploaderId;
    private ?int $authorId;
    private ?int $topicId;

    public function __construct(int $id, ?int $uploaderId, ?int $authorId, ?int $topicId) {
        $this->id = $id;
        $this->uploaderId = $uploaderId;
        $this->authorId = $authorId;
        $this->topicId = $topicId;
    }

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'uploaderId' => $this->uploaderId,
            'authorId' => $this->authorId,
            'topicId' => $this->topicId,
        ];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUploaderId(): ?int {
        return $this->uploaderId;
    }

    public function getAuthorId(): ?int {
        return $this->authorId;
    }

    public function getTopicId(): ?int {
        return $this->topicId;
    }
}
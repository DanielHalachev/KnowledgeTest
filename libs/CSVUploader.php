<?php
require_once "ControllerBase.php";
require_once "JWT.php";
require_once "Settings.php";
require_once "DatabaseHandler.php";
class CSVUploader extends ControllerBase {
    private $token;
    private $payload;

    public function __construct() {
        $this->token = $this->getAuthToken();
    }

    public function processCSV($csvFile) : string {
        if (!$this->token) {
            $this->sendUnauthorizedResponse();
            return 'You have to be logged in so as to perform that operation.';
        }
        $this->payload = JWT::decode($this->token, getenv("SECRET_KEY"));

        if(!$this->verifyPayload($this->payload, $this->payload["userId"])) {
            return 'You have to be logged in so as to perform that operation.';
        }

        if ($csvFile['error'] !== UPLOAD_ERR_OK) {
            return 'There was an error uploading the CSV file';
        }

        $tmpFilePath = $csvFile['tmp_name'];
        if (self::readCSV($tmpFilePath, $this->payload['userId'])) {
            return 'The CSV file was uploaded and processed successfully';
        }
        return 'There was an error processing the CSV file';
    }

    private static function readCSV(string $fileName, int $uploaderId) : bool {
        if (($handle = fopen($fileName, OPENING_MODE)) !== FALSE) {
            $row = 0;
            while (($data = fgetcsv($handle, MAXIMUM_LINE_LENGTH, DELIMITER)) !== FALSE) {
                $numberOfFields = count($data);
                $row++;
                if ($row === 1) {
                    continue;
                }
                for ($c = 0; $c < $numberOfFields; $c++) {
                    $data[$c] = mb_convert_encoding($data[$c], TO_ENCODING, FROM_ENCODING);
                }
                $testId = DatabaseHandler::getTestId($uploaderId, $data[INDEX_COLUMN_FACULTY_NUMBER]);
                $questionTypeId = DatabaseHandler::getQuestionTypeId($data[INDEX_COLUMN_QUESTION_TYPE]);
                $questionId = DatabaseHandler::createQuestion(
                    $testId,
                    $uploaderId,
                    $data[INDEX_COLUMN_QUESTION_AIM],
                    $questionTypeId,
                    true,
                    $data[INDEX_COLUMN_QUESTION_TEXT],
                    $data[INDEX_COLUMN_CORRECT_FEEDBACK],
                    $data[INDEX_COLUMN_INCORRECT_FEEDBACK]
                );
                for ($i = 0; $i < 4; $i++) {
                    $isCorrect = false;
                    if ($i === $data[INDEX_COLUMN_QUESTION_CORRECT_ANSWER] - 1) {
                        $isCorrect = true;
                    }
                    DatabaseHandler::createAnswer($questionId, $uploaderId ,$data[INDEX_COLUMN_QUESTION_FIRST_OPTION + $i],
                        $isCorrect);
                }
                DatabaseHandler::createFeedback($questionId, $data[INDEX_COLUMN_QUESTION_COMPLEXITY]);
            }
            fclose($handle);
            return true;
        }
        return false;
    }
}
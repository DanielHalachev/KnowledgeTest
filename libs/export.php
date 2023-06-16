<?php
include_once "Settings.php";
include_once "DatabaseConnection.php";

$databaseConnection = new DatabaseConnection();
$connection = $databaseConnection->getConnection();
$questionsQuery = "SELECT * FROM questions";
$questionsResult = $connection->query($questionsQuery);

$questions = array();

foreach ($questionsResult as $questionRow) {
    $questionId = $questionRow['id'];

    $answersQuery = "SELECT * FROM answers WHERE questionId = $questionId";
    $answersResult = $connection->query($answersQuery);

    $answers = array();

    foreach ($answersResult as $answerRow) {
        $answer = array(
            'answer' => $answerRow['label'],
            'fraction' => $answerRow['isCorrect'] ? 100 : 0,
            'feedback' => array(
                'answer' => $questionRow['correctFeedback'],
                'fraction' => $answerRow['isCorrect'] ? 100 : 0
            )
        );

        if (!$answerRow['isCorrect'] && !empty($questionRow['incorrectFeedback'])) {
            $answer['feedback']['answer'] = $questionRow['incorrectFeedback'];
        }

        $answers[] = $answer;
    }

    $questions[] = array(
        'question' => $questionRow['label'],
        'questiontype' => $questionRow['questionType'],
        'answernumbering' => 'none',
        'answer' => $answers
    );
}

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><quiz></quiz>');
foreach ($questions as $question) {
    $q = $xml->addChild('question');
    $q->addAttribute('type', 'multichoice');
    $name = $q->addChild('name');
    $text = $name->addChild('text', htmlspecialchars($question['question']));

    $questiontext = $q->addChild('questiontext');
    $text = $questiontext->addChild('text', htmlspecialchars($question['question']));

//    $q->addChild('qtype', $question['questiontype']);
    $q->addChild('answernumbering', $question['answernumbering']);

    foreach ($question['answer'] as $answer) {
        $a = $q->addChild('answer');
        $a->addAttribute('fraction', $answer['fraction']);
        $text = $a->addChild('text', htmlspecialchars($answer['answer']));

        $feedback = $a->addChild('feedback');
        $feedbackText = $feedback->addChild('text', htmlspecialchars($answer['feedback']['answer']));
    }
}

header('Content-type: text/xml; charset=utf-8');
echo $xml->asXML();
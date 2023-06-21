function getToken() {
    return document.cookie
        .split("; ")
        .find((row) => row.startsWith("token="))
        .split("=")[1];
}

function getQuestions(testId) {
    return fetch(`../api/questions?testId=${testId}&size=-1`, {
        headers: {
            Authorization: `Bearer ${getToken()}`,
        },
    })
        .then(response => response.json())
        .then(data => data)
        .catch(error => {
            console.error('Error fetching questions:', error);
            return [];
        });
}

function getAnswers(questionId) {
    return fetch(`../api/answers?questionId=${questionId}`, {
        headers: {
            Authorization: `Bearer ${getToken()}`,
        },
    })
        .then(response => response.json())
        .then(data => data)
        .catch(error => {
            console.error('Error:', error);
            return [];
        });
}

function generateMoodleXML(questions, topic) {
    const xml = document.createElement('quiz');
    xml.appendChild(document.createTextNode('\n'));
    const questionTopicElement = document.createElement('question');
    questionTopicElement.appendChild(document.createTextNode('\n'));
    questionTopicElement.setAttribute('type', 'category');
    xml.appendChild(questionTopicElement);

    const questionCategoryElement = document.createElement('category');
    questionCategoryElement.appendChild(document.createTextNode('\n'));
    const textElement = document.createElement('text');
    textElement.appendChild(document.createTextNode('\n'));
    textElement.textContent = topic;
    questionCategoryElement.appendChild(textElement);
    questionTopicElement.appendChild(questionCategoryElement);
    xml.appendChild(questionTopicElement);
    xml.appendChild(document.createTextNode('\n'));


    questions.forEach(question => {
        const questionElement = document.createElement('question');
        questionElement.appendChild(document.createTextNode('\n'));
        questionElement.setAttribute('type', 'multichoice');
        xml.appendChild(questionElement);

        const name = document.createElement('name');
        name.appendChild(document.createTextNode('\n'));
        const text = document.createElement('text');
        text.appendChild(document.createTextNode('\n'));
        text.textContent = question.label;
        name.appendChild(text);
        questionElement.appendChild(name);
        questionElement.appendChild(document.createTextNode('\n'));

        const questiontextElement = document.createElement('questiontext');
        questiontextElement.appendChild(document.createTextNode('\n'));
        questiontextElement.setAttribute('format', 'plain_text');
        const questionText = document.createElement('text');
        questionText.appendChild(document.createTextNode('\n'));
        questionText.textContent = question.label;
        questiontextElement.appendChild(questionText);
        questionElement.appendChild(questiontextElement);
        questionElement.appendChild(document.createTextNode('\n'));

        const answernumberingElement = document.createElement('answernumbering');
        answernumberingElement.appendChild(document.createTextNode('\n'));
        answernumberingElement.textContent = 'ABCD';
        questionElement.appendChild(answernumberingElement);
        questionElement.appendChild(document.createTextNode('\n'));

        const numberOfCorrectAnswers = question.answers.filter(answer => answer.isCorrect === true).length;

        question.answers.forEach(answer => {
            const answerElement = document.createElement('answer');
            answerElement.appendChild(document.createTextNode('\n'));
            answerElement.setAttribute('fraction', answer.isCorrect ? 100.0 / numberOfCorrectAnswers : 0);
            answerElement.setAttribute('format', 'plain_text');
            const answerText = document.createElement('text');
            answerText.appendChild(document.createTextNode('\n'));
            answerText.textContent = answer.label;
            answerElement.appendChild(answerText);

            const feedback = document.createElement('feedback');
            feedback.appendChild(document.createTextNode('\n'));
            const feedbackText = document.createElement('text');
            feedbackText.appendChild(document.createTextNode('\n'));
            feedbackText.textContent = answer.isCorrect ? question.correctFeedback : question.incorrectFeedback;
            feedback.appendChild(feedbackText);
            answerElement.appendChild(feedback);
            questionElement.appendChild(answerElement);
            questionElement.appendChild(document.createTextNode('\n'));
        });

        xml.appendChild(questionElement);
        xml.appendChild(document.createTextNode('\n'));
    });

    const serializer = new XMLSerializer();
    const xmlDeclaration = '<?xml version="1.0" encoding="UTF-8"?>\n';
    return xmlDeclaration + serializer.serializeToString(xml);
}


function exportToMoodleXML(testId, topic) {
    getQuestions(testId)
        .then(questions => {
            if (questions.length === 0) {
                console.log('No questions found.');
                return;
            }
            const questionPromises = questions.map(question => getAnswers(question.id));
            Promise.all(questionPromises)
                .then(answers => {
                    questions.forEach((question, index) => {
                        question.answers = answers[index];
                    });
                    const xmlString = generateMoodleXML(questions, topic);
                    const element = document.createElement('a');
                    element.setAttribute('href', 'data:text/xml;charset=utf-8,' + encodeURIComponent(xmlString));
                    element.setAttribute('download', 'moodleXmlFormat.xml');
                    element.style.display = 'none';
                    document.body.appendChild(element);
                    element.click();
                    document.body.removeChild(element);
                });
        });
}
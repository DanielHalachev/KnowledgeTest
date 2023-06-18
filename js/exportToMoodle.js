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
    const questionTopicElement = document.createElement('question');
    questionTopicElement.setAttribute('type', 'category')
    const questionCategoryElement = document.createElement('category');
    const textElement = document.createElement('text');
    textElement.textContent = topic;

    questionCategoryElement.appendChild(textElement);
    questionTopicElement.appendChild(questionCategoryElement);
    xml.appendChild(questionTopicElement);

    questions.forEach(question => {
        const questionElement = document.createElement('question');
        questionElement.setAttribute('type', 'multichoice');

        const name = document.createElement('name');
        const text = document.createElement('text');
        text.textContent = question.label;
        name.appendChild(text);
        questionElement.appendChild(name);

        const questiontextElement = document.createElement('questiontext');
        questiontextElement.setAttribute('format', 'plain_text');
        const questionText = document.createElement('text');
        questionText.textContent = question.label;
        questiontextElement.appendChild(questionText);
        questionElement.appendChild(questiontextElement);

        const answernumberingElement = document.createElement('answernumbering');
        answernumberingElement.textContent = 'ABCD';
        questionElement.appendChild(answernumberingElement);

        const numberOfCorrectAnswers = question.answers.filter(answer => answer.isCorrect === true).length;

        question.answers.forEach(answer => {
            const answerElement = document.createElement('answer');
            answerElement.setAttribute('fraction', answer.isCorrect ? 100.0 / numberOfCorrectAnswers : 0);
            answerElement.setAttribute('format', 'plain_text');
            const answerText = document.createElement('text');
            answerText.textContent = answer.label;
            answerElement.appendChild(answerText);

            const feedback = document.createElement('feedback');
            const feedbackText = document.createElement('text');
            feedbackText.textContent = answer.isCorrect ? question.correctFeedback : question.incorrectFeedback;
            feedback.appendChild(feedbackText);
            answerElement.appendChild(feedback);
            questionElement.appendChild(answerElement);
        });

        xml.appendChild(questionElement);
    });

    const serializer = new XMLSerializer();
    const xmlDeclaration = '<' + '?xml version="1.0" encoding="UTF-8"?>\n';
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
                    element.setAttribute('download', 'questions.xml');
                    element.style.display = 'none';
                    document.body.appendChild(element);
                    element.click();
                    document.body.removeChild(element);
                });
        });
}
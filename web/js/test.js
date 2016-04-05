/* config */
var WORD_COUNT = 10; // words per one test, const
//var REPEATS = 2; // iteration level, const (how many times to repeat one word)
var wrongAnswers = 0; // init global variable

var wordCount = WORD_COUNT;
var allKeys = Object.keys(dict);
if (allKeys.length < WORD_COUNT) {
    wordCount = allKeys.length;
}

var testWords = getTestWords();

var questEl = $("#question");
var rightAnswer = $("#rightAnswer");
var userAnswer = $("#userAnswer");
var answerContainer = $(".right-answer");
var testForm = $("#testForm");
var resultContainer = $("#result");

putToHtml(testWords);

var confirm = $("#confirmButton");
confirm.on("click", confirmHandler);
userAnswer.on("keypress", pressingEnter);


/* FUNCTIONS */
function getTestWords() {
    var testWords = [];
    var startKey = getRandomInt(0, allKeys.length - wordCount + 1);
    var endKey = startKey + wordCount;

    for (var i = startKey, j = 0, keyName; i < endKey; i++, j++) {
        keyName = allKeys[i];
        testWords[j] = [keyName, dict[keyName]];
    }
    return testWords;
}

function putToHtml(testWords) {
    var wordPair = testWords.pop();
    questEl.text(wordPair[1]);
    rightAnswer.text(wordPair[0]);
}

function confirmHandler() {
    answerContainer.hide();
    if (userAnswer.val() == rightAnswer.text()) {
        if (testWords.length > 0) {
            putToHtml(testWords);
        } else {
            showResults();
        }
    } else {
        wrongAnswers++;
        answerContainer.show();
    }
    userAnswer.val('');
}

function pressingEnter(e) {
    if (e.charCode == 13) {
        confirmHandler();
    }
}

function showResults() {
    testForm.hide();
    var text = "Результат: " + (wordCount - wrongAnswers)
                + " правильных ответов из " + wordCount;
    resultContainer.text(text);
    resultContainer.show();
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}

<?php

session_start();

// check if old session is still open
if (!isset($_SESSION["userId"])) {
    header("Location: /mounira/quiz/auth/login.php");
    exit();
}

function escape(string $string): string
{
    return htmlspecialchars($string);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $quizId = $_POST["quiz_id"];
    $questionText = escape($_POST["question_text"]);
    $options = $_POST["options"];
    $correctOption = $_POST["correct_option"];

    // save question_text on questions table in DB
    $dbConnection = mysqli_connect("172.19.0.2", "root", "root", "quiz");
    $query = "INSERT INTO questions (`quiz_id`, `question_text`) VALUES ('{$quizId}', '{$questionText}')";
    $request = mysqli_query($dbConnection, $query);

    // questionId
    $questionId = mysqli_insert_id($dbConnection);

    foreach ($options as $index => $option) {
        if (empty($option)) {
            continue;
        }

        $isCorrect = (int) false;

        if ($correctOption == $index) {
            $isCorrect = (int) true;
        }

        $optionQuery = "INSERT INTO options (`question_id`, `option_text`, `is_correct`) VALUES ({$questionId}, '{$option}', {$isCorrect})";
        $request = mysqli_query($dbConnection, $optionQuery);
    }

    /* Close the connection as soon as it's no longer needed */
    mysqli_close($dbConnection);
    // collect question id to save with question options
}

// fetch quiz title form database
$dbConnection = mysqli_connect("172.19.0.2", "root", "root", "quiz");
$query = "SELECT `id`, `title` FROM quizzes";
$request = mysqli_query($dbConnection, $query);

$response = mysqli_fetch_all($request);

/* Close the connection as soon as it's no longer needed */
mysqli_close($dbConnection);
?>

<h2>Create questions</h2>

<div>
    <form method="POST">
        <div>
            <select name="quiz_id" required>
                <?php foreach ($response as $title) {
                    echo "<option value='{$title[0]}'>{$title[1]}</option>";
                } ?>
            </select>
        </div>

        <div>
            <textarea name="question_text" placeholder="Question text" required></textarea>
        </div>

        <div>
            <label for="option-1">Option one</label>
            <input type="text" id="option-1" name="options[]" placeholder="option 1">
        </div>

        <div>
            <label for="option-2">Option </label>
            <input type="text" id="option-2" name="options[]" placeholder="option 2">
        </div>

        <div>
            <label for="option-3">Option three</label>
            <input type="text" id="option-3" name="options[]" placeholder="option 3">
        </div>

        <div>
            <label for="option-4">Option four</label>
            <input type="text" id="option-4" name="options[]" placeholder="option 4">
        </div>

        <div>
            <label for="option-5">Option five</label>
            <input type="text" id="option-5" name="options[]" placeholder="option 5">
        </div>

        <div>
            <label for="correct-value">Correct answer</label>
            <select name="correct_option" id="correct-value">
                <option value="0">Option one</option>
                <option value="1">Option two</option>
                <option value="2">Option three</option>
                <option value="3">Option four</option>
                <option value="4">Option five</option>
            </select>
        </div>

        <div>
            <button type="submit">Add Question</button>
        </div>
    </form>

</div>

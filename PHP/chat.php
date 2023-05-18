<?php
session_start();

if (!isset($_SESSION["korisnik"])) {
    header("Location: login.php");
    exit;
}

$naslov = "Help";
require_once("header.php");
require_once("adminNav.php");

function validate_input($input)
{
    $validation_regex = '/^[a-zA-Z0-9_\,\.\-\ ]{5,170}$/';
    return preg_match($validation_regex, $input);
}

function sanitize_input($input)
{
    return htmlentities($input);
}

function make_api_request($input)
{
    $kontekst = "You are now specialized customer support agent, polite user input is: ";
    $apiKey = "get yours for free at ";
    $url = "https://simple-chatgpt-api.p.rapidapi.com/ask";
    $data = array(
        "question" => $kontekst . $input
    );

    $dataString = json_encode($data);

    $headers = array(
        "X-RapidAPI-Host: simple-chatgpt-api.p.rapidapi.com",
        "X-RapidAPI-Key: " . $apiKey,
        "content-type: application/json"
    );

    $ch = curl_init();

    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $dataString,
        CURLOPT_HTTPHEADER => $headers,
    ));

    $response = curl_exec($ch);
    $err = curl_error($ch);

    curl_close($ch);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        $output = json_decode($response, true);
        if (isset($output["answer"])) {
            return $output["answer"];
        } else {
            return "Error: Invalid API response format, or API key";
        }
    }
}

?>

<div class="container">
    <h1>Help us, to help you resolve your issues</h1>
    <div style="margin: 0 auto;">
        <form method="post">
            <label for="inputField">Describe your issue:</label><br>
            <textarea name="inputField" id="inputField" rows="20" cols="75"></textarea><br><br>
            <input type="submit" name="submit" value="Submit">
            <div id="conversationContainer">
                <?php
                if (isset($_POST['submit'])) {
                    $inputValue = $_POST['inputField'];

                    if (!validate_input($inputValue)) {
                        echo "Please enter a valid input (5-100 characters allowed)";
                    } else {
                        $inputValue = sanitize_input($inputValue);
                        $answer = make_api_request($inputValue);

                        echo '<div class="output">';
                        echo "<p>Question: " . $inputValue . "</p>";
                        echo "<p>Output: " . $answer . "</p>";
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </form>
    </div>
</div>

<?php
include('footer.php');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Rate an Assignment</title>
  <link href="css/rating.css" rel="stylesheet" type="text/class">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script type="text/javascript" src="js/rating.js"></script>
<script>
  $( function() {
    $( "#accordion" ).accordion();
  });
</script>
<!--
<script language="javascript" type="text/javascript">
$(function() {
    $("#rating_star").spaceo_rating_widget({
	starLength: '5',
	initialValue: '',
	callbackFunctionName: 'processRating',
	imageDirectory: 'img/',
	inputAttr: 'post_id'
    });
  });
  function processRating(val, attrVal){
	$.ajax({
		type: 'POST',
		url: 'rating.php',
		data: 'post_id=1&points='+val,
		dataType: 'json',
		success: function(data) {
			if (data.status == 'ok') {
				alert('You have rated '+val);
			}else {
				alert('Please try again in a minute.');
			}
		}
	});
    }
  </script>
-->
</head>
<body>

<?PHP

if (empty($_POST['launch_presentation_return_url']))
{
        echo "<br /><br /><br /><center>";
	echo "<div style='width: 60%; text-align: left'>";
        echo "<br />This link is only to be used through the Web Link tool in Blackboard.";
	echo "</div></center>";
        die();
}


$url = $_POST['launch_presentation_return_url'];
$query=parse_url($url,PHP_URL_QUERY);
parse_str($query, $out);

$course_id=$out['course_id'];
$course_title=$_POST['context_title'];
$course_batchUID=$_POST['context_label'];
$user_id=$_POST['user_id'];
$name=$_POST['lis_person_name_full'];

preg_match_all('/\//', $url,$matches, PREG_OFFSET_CAPTURE);
$clientURL = substr($url, 0, $matches[0][2][1]);

require_once('classes/Rest.class.php');
require_once('classes/Token.class.php');

$rest = new Rest($clientURL);
$token = new Token();

$token = $rest->authorize();
$access_token = $token->access_token;


$user = $rest->readUser($access_token, "uuid:".$user_id);
// Get user id to use when finding if they are instructor or student
$userid = $user->id;

// Gets the membership of all users in the course
// We use this to find the user's id and get their courseRoleId (instructor or student)
$membership = $rest->readMembership($access_token, $course_id, "");
// Get results so we can loop through them
$m = $membership->results;
$user_status = "";
foreach($m as $row)
{
	if($row->userId == $userid)
	{
		$user_status = $row->courseRoleId;
		break;
	}
}

// Grabs all of the assignments from the course
$columns = $rest->readGradebookColumns($access_token, $course_id);
// Get results so we can loop through them
$c=$columns->results;
echo $user_status . "<br /><br />";
// Use jQuery to load the assignments into their own div block
echo "<div id='accordion'>";
$i = 1;
foreach($c as $row)
{
	// We don't want to show Total or Weighted Total because these arent real assignments
        if ($row->name == "Total" || $row->name == "Weighted Total")
        {
		continue;
        } else {
		echo "<h3>" . $row->name . "</h3>";
		echo "<div><p><h4>Assignment ID: " . $row->id . "</h4>";
		echo "<textarea name='ta" . $i . "' rows='4' cols='50'></textarea>";
		echo "<input type='submit' name='sub" . $i . "' /></p></div>";
		//echo "<input name='rating" . $i . "' value='0' id='rating_star' type='hidden' postID='1'/></p></div>";
	}
	$i++;
}
echo "</div>";

?>
</body>
</html>
